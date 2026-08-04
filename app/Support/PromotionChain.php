<?php

namespace App\Support;

use App\Repositories\Product\PromotionRepository;
use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Turns the promotion campaigns attached to a product into the ordered sequence
 * of discounts a shopper will actually see over time.
 *
 * A product can belong to several campaigns at once, so at any given moment the
 * applicable one is whichever is cheapest for the shopper. When that campaign
 * ends, the next best one still running takes over - the chain continues instead
 * of the price jumping back to full and staying there.
 *
 * The naive approach - sort every campaign by discount and queue them back to
 * back - looks right but silently drops real discounts: a 30% campaign starting
 * next week would be placed ahead of a 20% campaign running *right now*, leaving
 * the shopper at full price today. So this walks the timeline instead. At every
 * boundary (a campaign starting or ending) it asks which campaigns are in force
 * and keeps the best one, which handles overlaps, gaps and open ended campaigns
 * uniformly.
 */
class PromotionChain
{
    /**
     * @param  iterable  $campaigns  Rows from PromotionRepository::findActiveAndUpcomingByProducts()
     * @return array<int, array<string, mixed>> Segments in chronological order.
     *         Each segment: promotion_id, name, discount, percent, price,
     *         priceSale, startsAt, endsAt (null when open ended).
     */
    public static function build(iterable $campaigns, ?CarbonInterface $now = null): array
    {
        $now = $now ? Carbon::parse($now) : Carbon::now();

        $items = self::normalise($campaigns, $now);
        if (empty($items)) {
            return [];
        }

        $boundaries = self::boundaries($items, $now);

        $segments = [];
        foreach ($boundaries as $index => $from) {
            $best = self::bestAt($items, $from);
            if ($best === null) {
                continue; // A gap between campaigns: no discount applies.
            }

            // The segment runs until the next boundary, or until this campaign
            // ends if that comes first (it cannot come later - its end is a
            // boundary itself).
            $endsAt = self::earlier($best['end'], $boundaries[$index + 1] ?? null);

            $segments[] = self::segment($best, $from, $endsAt);
        }

        return self::mergeAdjacent($segments);
    }

    /**
     * The single discount in force right now, or null at full price.
     *
     * @return array<string, mixed>|null
     */
    public static function current(iterable $campaigns, ?CarbonInterface $now = null): ?array
    {
        $chain = self::build($campaigns, $now);
        $now = $now ? Carbon::parse($now) : Carbon::now();

        foreach ($chain as $segment) {
            $startsAt = Carbon::parse($segment['startsAt']);
            $endsAt = $segment['endsAt'] ? Carbon::parse($segment['endsAt']) : null;

            if ($startsAt->lessThanOrEqualTo($now) && ($endsAt === null || $endsAt->greaterThan($now))) {
                return $segment;
            }
        }

        return null;
    }

    /** Drop campaigns that cannot ever apply, and parse their dates once. */
    private static function normalise(iterable $campaigns, CarbonInterface $now): array
    {
        $items = [];

        foreach ($campaigns as $row) {
            $price = (float) (self::get($row, 'product_price') ?? 0);
            $discount = (float) (self::get($row, 'discount') ?? 0);

            if ($price <= 0 || $discount <= 0) {
                continue;
            }

            $neverEnds = self::get($row, 'neverEndDate') === PromotionRepository::NEVER_ENDS;
            $rawEnd = self::get($row, 'endDate');
            $end = ($neverEnds || empty($rawEnd)) ? null : Carbon::parse($rawEnd);

            if ($end !== null && $end->lessThanOrEqualTo($now)) {
                continue; // Already over.
            }

            $rawStart = self::get($row, 'startDate');
            $start = empty($rawStart) ? $now->copy() : Carbon::parse($rawStart);

            $items[] = [
                'promotion_id' => (int) self::get($row, 'promotion_id'),
                'name' => (string) (self::get($row, 'promotion_name') ?? ''),
                'price' => $price,
                // Never discount below zero.
                'discount' => min($discount, $price),
                'start' => $start,
                'end' => $end,
            ];
        }

        return $items;
    }

    /**
     * Instants where the winning campaign can change: now, plus every future
     * start and end.
     *
     * @return array<int, Carbon>
     */
    private static function boundaries(array $items, CarbonInterface $now): array
    {
        $keyed = [$now->format('Y-m-d H:i:s') => Carbon::parse($now)];

        foreach ($items as $item) {
            foreach ([$item['start'], $item['end']] as $moment) {
                if ($moment !== null && $moment->greaterThan($now)) {
                    $keyed[$moment->format('Y-m-d H:i:s')] = $moment->copy();
                }
            }
        }

        ksort($keyed);

        return array_values($keyed);
    }

    /**
     * Best campaign in force at $moment: largest discount, and on a tie the one
     * ending soonest so the chain moves on to the next campaign earlier.
     *
     * @return array<string, mixed>|null
     */
    private static function bestAt(array $items, CarbonInterface $moment): ?array
    {
        $best = null;

        foreach ($items as $item) {
            if ($item['start']->greaterThan($moment)) {
                continue;
            }
            if ($item['end'] !== null && $item['end']->lessThanOrEqualTo($moment)) {
                continue;
            }

            if ($best === null || self::beats($item, $best)) {
                $best = $item;
            }
        }

        return $best;
    }

    private static function beats(array $candidate, array $incumbent): bool
    {
        if ($candidate['discount'] !== $incumbent['discount']) {
            return $candidate['discount'] > $incumbent['discount'];
        }

        // Same discount: prefer the one that expires first.
        if ($candidate['end'] === null) {
            return false;
        }
        if ($incumbent['end'] === null) {
            return true;
        }

        return $candidate['end']->lessThan($incumbent['end']);
    }

    private static function earlier(?CarbonInterface $a, ?CarbonInterface $b): ?Carbon
    {
        if ($a === null) {
            return $b ? Carbon::parse($b) : null;
        }
        if ($b === null) {
            return Carbon::parse($a);
        }

        return Carbon::parse($a->lessThan($b) ? $a : $b);
    }

    private static function segment(array $item, CarbonInterface $from, ?CarbonInterface $to): array
    {
        $priceSale = max(0.0, $item['price'] - $item['discount']);

        return [
            'promotion_id' => $item['promotion_id'],
            'name' => $item['name'],
            'discount' => $item['discount'],
            'percent' => (int) round(($item['discount'] / $item['price']) * 100),
            'price' => $item['price'],
            'priceSale' => $priceSale,
            'startsAt' => Carbon::parse($from)->format('Y-m-d H:i:s'),
            'endsAt' => $to ? Carbon::parse($to)->format('Y-m-d H:i:s') : null,
        ];
    }

    /** Collapse consecutive segments that are the same campaign. */
    private static function mergeAdjacent(array $segments): array
    {
        $merged = [];

        foreach ($segments as $segment) {
            $last = count($merged) ? $merged[count($merged) - 1] : null;

            if ($last && $last['promotion_id'] === $segment['promotion_id'] && $last['endsAt'] === $segment['startsAt']) {
                $merged[count($merged) - 1]['endsAt'] = $segment['endsAt'];
                continue;
            }

            $merged[] = $segment;
        }

        return $merged;
    }

    /** Rows may arrive as stdClass, Eloquent models or arrays. */
    private static function get($row, string $key)
    {
        if (is_array($row)) {
            return $row[$key] ?? null;
        }

        return $row->{$key} ?? null;
    }
}
