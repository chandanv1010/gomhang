<?php

namespace Tests\Feature;

use App\Enums\PromotionEnum;
use App\Services\V1\Product\PromotionPricingService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * The homepage, the catalogue listing and the product detail page each used to
 * work out the sale price their own way: the homepage read products.percent
 * directly, while the other two went through a helper whose promotion branch
 * never ran. The same product could therefore show three different prices.
 *
 * These tests pin the behaviour that fixed it - one pricing source, agreed on by
 * every page - and are wrapped in a transaction so the campaign they create is
 * never left behind in the database.
 */
class PromotionPricingConsistencyTest extends TestCase
{
    private int $productId;
    private float $productPrice;
    private string $productCanonical;
    private int $promotionId;

    protected function setUp(): void
    {
        parent::setUp();

        // Widgets are memoised in a static for the life of the process, so an
        // earlier test that loaded the homepage would otherwise pin its prices.
        \App\Services\V1\Core\WidgetService::flushCache();

        DB::beginTransaction();

        $product = DB::table('products as p')
            ->join('product_language as pl', function ($join) {
                $join->on('pl.product_id', '=', 'p.id')->where('pl.language_id', 1);
            })
            ->join('routers as r', function ($join) {
                $join->on('r.module_id', '=', 'p.id')
                    ->where('r.controllers', 'App\Http\Controllers\Frontend\ProductController');
            })
            ->whereNull('p.deleted_at')
            ->where('p.publish', 2)
            ->where('p.price', '>', 0)
            ->orderBy('p.id')
            ->first(['p.id', 'p.price', 'pl.canonical']);

        if ($product === null) {
            DB::rollBack();
            $this->markTestSkipped('No published product with a route to price.');
        }

        $this->productId = (int) $product->id;
        $this->productPrice = (float) $product->price;
        $this->productCanonical = (string) $product->canonical;

        // Clear anything already attached so the assertions are about our campaign.
        DB::table('promotion_product_variant')->where('product_id', $this->productId)->delete();

        $this->promotionId = $this->createCampaign('TESTONLY50', 50, Carbon::now()->subHour(), Carbon::now()->addDays(3));
    }

    protected function tearDown(): void
    {
        DB::rollBack();

        parent::tearDown();
    }

    public function test_every_page_shows_the_same_promotion_price(): void
    {
        $expected = convert_price($this->productPrice * 0.5, true) . 'đ';

        foreach (['/', $this->catalogueUrl(), $this->productUrl()] as $url) {
            $response = $this->get($url);
            $response->assertOk();

            $this->assertStringContainsString(
                $expected,
                $response->getContent(),
                sprintf(
                    "%s did not show %s for product #%d. Prices on the page: %s",
                    $url,
                    $expected,
                    $this->productId,
                    implode(', ', array_slice($this->pricesOn($response->getContent()), 0, 8)) ?: '(none)'
                )
            );
        }
    }

    /** @return array<int, string> */
    private function pricesOn(string $html): array
    {
        preg_match_all('/(?:sale-price|price-highlight)[^>]*>\s*([0-9.]+đ)/u', $html, $matches);

        return array_values(array_unique($matches[1] ?? []));
    }

    public function test_the_detail_page_counts_down_to_the_real_campaign_deadline(): void
    {
        $response = $this->get($this->productUrl());

        $response->assertOk();
        // Match the element, not the class name - the class also appears in the
        // page stylesheet whether or not the block is rendered.
        $response->assertSee('id="flash-sale-container"', false);
        // The countdown is fed by the chain, not by a synthetic end-of-month date.
        $response->assertSee('"promotion_id":' . $this->promotionId, false);
    }

    public function test_a_product_with_no_campaign_shows_full_price_and_no_flash_sale(): void
    {
        DB::table('promotion_product_variant')->where('product_id', $this->productId)->delete();

        $response = $this->get($this->productUrl());

        $response->assertOk();
        $response->assertSee(convert_price($this->productPrice, true) . 'đ', false);
        $response->assertDontSee('id="flash-sale-container"', false);
    }

    public function test_the_deeper_of_two_running_campaigns_wins_and_the_other_follows_it(): void
    {
        // A shallower campaign that outlives the 50% one.
        $this->createCampaign('TESTONLY10', 10, Carbon::now()->subHour(), Carbon::now()->addDays(20));

        $chain = app(PromotionPricingService::class)->chainsFor([$this->productId])[$this->productId] ?? [];

        $this->assertCount(2, $chain, 'Expected the 50% campaign followed by the 10% one.');
        $this->assertSame(50, $chain[0]['percent']);
        $this->assertSame(10, $chain[1]['percent']);
        // The second segment picks up exactly where the first ends.
        $this->assertSame($chain[0]['endsAt'], $chain[1]['startsAt']);
    }

    public function test_an_expired_campaign_does_not_discount_anything(): void
    {
        DB::table('promotion_product_variant')->where('product_id', $this->productId)->delete();
        $this->createCampaign('TESTONLYOLD', 40, Carbon::now()->subDays(5), Carbon::now()->subMinute());

        $this->assertSame([], app(PromotionPricingService::class)->chainsFor([$this->productId]));
    }

    private function createCampaign(string $code, int $percent, Carbon $start, Carbon $end): int
    {
        $promotionId = DB::table('promotions')->insertGetId([
            'name' => 'Test ' . $code,
            'code' => $code,
            'description' => 'phpunit',
            'method' => PromotionEnum::PRODUCT_AND_QUANTITY,
            'startDate' => $start,
            'endDate' => $end,
            'publish' => 2,
            'order' => 0,
            'discountValue' => $percent,
            'discountType' => 'percent',
            'maxDiscountValue' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('promotion_product_variant')->insert([
            'promotion_id' => $promotionId,
            'product_id' => $this->productId,
            'model' => 'Product',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $promotionId;
    }

    private function productUrl(): string
    {
        return '/' . ltrim($this->productCanonical, '/') . config('apps.general.suffix');
    }

    private function catalogueUrl(): string
    {
        $canonical = DB::table('product_catalogue_product as pcp')
            ->join('routers as r', function ($join) {
                $join->on('r.module_id', '=', 'pcp.product_catalogue_id')
                    ->where('r.controllers', 'App\Http\Controllers\Frontend\ProductCatalogueController');
            })
            ->where('pcp.product_id', $this->productId)
            ->value('r.canonical');

        $this->assertNotNull($canonical, 'The test product is not in any routed catalogue.');

        return '/' . ltrim((string) $canonical, '/') . config('apps.general.suffix');
    }
}
