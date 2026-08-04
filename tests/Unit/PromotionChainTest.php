<?php

namespace Tests\Unit;

use App\Support\PromotionChain;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class PromotionChainTest extends TestCase
{
    private const NOW = '2026-08-03 12:00:00';
    private const PRICE = 200000.0;

    public function test_no_campaigns_means_no_chain(): void
    {
        $this->assertSame([], PromotionChain::build([], $this->now()));
        $this->assertNull(PromotionChain::current([], $this->now()));
    }

    public function test_a_single_running_campaign_is_the_current_discount(): void
    {
        $chain = PromotionChain::build([
            $this->campaign(1, 'A', 20000, '2026-08-01 00:00:00', '2026-08-10 00:00:00'),
        ], $this->now());

        $this->assertCount(1, $chain);
        $this->assertSame(1, $chain[0]['promotion_id']);
        $this->assertSame(10, $chain[0]['percent']);
        $this->assertSame(180000.0, $chain[0]['priceSale']);
        $this->assertSame(self::NOW, $chain[0]['startsAt']);
        $this->assertSame('2026-08-10 00:00:00', $chain[0]['endsAt']);
    }

    public function test_the_deepest_discount_wins_while_both_campaigns_overlap(): void
    {
        $chain = PromotionChain::build([
            $this->campaign(1, 'shallow', 20000, '2026-08-01 00:00:00', '2026-08-30 00:00:00'),
            $this->campaign(2, 'deep', 60000, '2026-08-01 00:00:00', '2026-08-10 00:00:00'),
        ], $this->now());

        // Deep runs first, then the chain steps down to shallow when it ends.
        $this->assertCount(2, $chain);

        $this->assertSame(2, $chain[0]['promotion_id']);
        $this->assertSame(30, $chain[0]['percent']);
        $this->assertSame('2026-08-10 00:00:00', $chain[0]['endsAt']);

        $this->assertSame(1, $chain[1]['promotion_id']);
        $this->assertSame(10, $chain[1]['percent']);
        $this->assertSame('2026-08-10 00:00:00', $chain[1]['startsAt']);
        $this->assertSame('2026-08-30 00:00:00', $chain[1]['endsAt']);
    }

    /**
     * The reason this class walks the timeline instead of just sorting by
     * discount: a better campaign starting later must not hide a smaller one
     * that is running right now.
     */
    public function test_a_bigger_future_campaign_does_not_hide_a_smaller_running_one(): void
    {
        $chain = PromotionChain::build([
            $this->campaign(1, 'running now', 20000, '2026-08-01 00:00:00', '2026-08-30 00:00:00'),
            $this->campaign(2, 'starts later', 80000, '2026-08-15 00:00:00', '2026-08-20 00:00:00'),
        ], $this->now());

        $this->assertCount(3, $chain);

        // Now until the big one starts: the small campaign applies.
        $this->assertSame(1, $chain[0]['promotion_id']);
        $this->assertSame(self::NOW, $chain[0]['startsAt']);
        $this->assertSame('2026-08-15 00:00:00', $chain[0]['endsAt']);

        // The big one takes over for its window.
        $this->assertSame(2, $chain[1]['promotion_id']);
        $this->assertSame(40, $chain[1]['percent']);
        $this->assertSame('2026-08-20 00:00:00', $chain[1]['endsAt']);

        // Then back down to the small one until it expires.
        $this->assertSame(1, $chain[2]['promotion_id']);
        $this->assertSame('2026-08-20 00:00:00', $chain[2]['startsAt']);
        $this->assertSame('2026-08-30 00:00:00', $chain[2]['endsAt']);

        // And right now the shopper gets the campaign that is actually live.
        $this->assertSame(1, PromotionChain::current([
            $this->campaign(1, 'running now', 20000, '2026-08-01 00:00:00', '2026-08-30 00:00:00'),
            $this->campaign(2, 'starts later', 80000, '2026-08-15 00:00:00', '2026-08-20 00:00:00'),
        ], $this->now())['promotion_id']);
    }

    public function test_a_gap_between_campaigns_has_no_discount(): void
    {
        $campaigns = [
            $this->campaign(1, 'first', 20000, '2026-08-01 00:00:00', '2026-08-05 00:00:00'),
            $this->campaign(2, 'second', 40000, '2026-08-20 00:00:00', '2026-08-25 00:00:00'),
        ];

        $chain = PromotionChain::build($campaigns, $this->now());

        $this->assertCount(2, $chain);
        $this->assertSame('2026-08-05 00:00:00', $chain[0]['endsAt']);
        // Nothing covers 05 -> 20; the next segment only starts on the 20th.
        $this->assertSame('2026-08-20 00:00:00', $chain[1]['startsAt']);
    }

    public function test_a_campaign_that_starts_later_leaves_full_price_for_now(): void
    {
        $campaigns = [$this->campaign(1, 'later', 40000, '2026-08-20 00:00:00', '2026-08-25 00:00:00')];

        $this->assertNull(PromotionChain::current($campaigns, $this->now()));
        $this->assertCount(1, PromotionChain::build($campaigns, $this->now()));
    }

    public function test_an_expired_campaign_is_ignored(): void
    {
        $chain = PromotionChain::build([
            $this->campaign(1, 'over', 40000, '2026-07-01 00:00:00', '2026-08-03 11:59:59'),
        ], $this->now());

        $this->assertSame([], $chain);
    }

    public function test_an_open_ended_campaign_never_expires(): void
    {
        $chain = PromotionChain::build([
            $this->campaign(1, 'forever', 20000, '2026-08-01 00:00:00', null, 'accept'),
        ], $this->now());

        $this->assertCount(1, $chain);
        $this->assertNull($chain[0]['endsAt']);
    }

    public function test_an_open_ended_campaign_resumes_after_a_deeper_one_ends(): void
    {
        $chain = PromotionChain::build([
            $this->campaign(1, 'forever', 20000, '2026-08-01 00:00:00', null, 'accept'),
            $this->campaign(2, 'flash', 60000, '2026-08-01 00:00:00', '2026-08-06 00:00:00'),
        ], $this->now());

        $this->assertCount(2, $chain);
        $this->assertSame(2, $chain[0]['promotion_id']);
        $this->assertSame(1, $chain[1]['promotion_id']);
        $this->assertNull($chain[1]['endsAt']);
    }

    public function test_the_discount_never_exceeds_the_price(): void
    {
        $chain = PromotionChain::build([
            $this->campaign(1, 'absurd', 500000, '2026-08-01 00:00:00', '2026-08-10 00:00:00'),
        ], $this->now());

        $this->assertSame(0.0, $chain[0]['priceSale']);
        $this->assertSame(100, $chain[0]['percent']);
    }

    public function test_equal_discounts_prefer_the_campaign_ending_soonest(): void
    {
        $chain = PromotionChain::build([
            $this->campaign(1, 'long', 30000, '2026-08-01 00:00:00', '2026-08-30 00:00:00'),
            $this->campaign(2, 'short', 30000, '2026-08-01 00:00:00', '2026-08-08 00:00:00'),
        ], $this->now());

        $this->assertSame(2, $chain[0]['promotion_id']);
        $this->assertSame(1, $chain[1]['promotion_id']);
    }

    private function now(): Carbon
    {
        return Carbon::parse(self::NOW);
    }

    private function campaign(
        int $id,
        string $name,
        float $discount,
        ?string $start,
        ?string $end,
        ?string $neverEnd = null
    ): object {
        return (object) [
            'promotion_id' => $id,
            'promotion_name' => $name,
            'discount' => $discount,
            'product_price' => self::PRICE,
            'startDate' => $start,
            'endDate' => $end,
            'neverEndDate' => $neverEnd,
        ];
    }
}
