<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Regression coverage for the brand filter on product catalogue pages.
 *
 * The filter matches products through the JSON `products.attribute` column,
 * where brand ids live under the "8" key (attribute catalogue "Thương hiệu"):
 *
 *     {"8": ["25"]}
 *
 * MySQL's JSON_CONTAINS() rejects a non-string candidate argument with
 * SQLSTATE[22032] 3146, so every bound candidate has to be a JSON text
 * literal - never a PHP int.
 */
class ProductCatalogueBrandFilterTest extends TestCase
{
    /** Attribute catalogue holding brands ("Thương hiệu"). */
    private const BRAND_CATALOGUE_KEY = '8';

    /** Canonical of a brand that exists in the seeded data. */
    private const BRAND_CANONICAL = 'apple';

    public function test_brand_filter_page_loads_without_a_database_error(): void
    {
        $url = $this->catalogueUrl($this->catalogueIdWithBrandProducts());

        $this->get($url . '?brand=' . self::BRAND_CANONICAL)->assertOk();
    }

    public function test_brand_filter_narrows_the_result_set(): void
    {
        $catalogueId = $this->catalogueIdWithBrandProducts();
        $url = $this->catalogueUrl($catalogueId);

        $unfiltered = $this->get($url);
        $filtered = $this->get($url . '?brand=' . self::BRAND_CANONICAL);

        $unfiltered->assertOk();
        $filtered->assertOk();

        $filteredCards = $this->countProductCards($filtered->getContent());

        $this->assertGreaterThan(0, $filteredCards, 'The brand filter returned no products at all.');
        $this->assertLessThan(
            $this->countProductCards($unfiltered->getContent()),
            $filteredCards,
            'The brand filter returned as many products as the unfiltered page, so it is not filtering.'
        );
    }

    public function test_json_contains_accepts_both_string_and_number_candidates(): void
    {
        $brandId = $this->brandId();
        $path = '$."' . self::BRAND_CATALOGUE_KEY . '"';

        // Both candidates must be bound as strings: '"25"' parses as a JSON
        // string, '25' parses as a JSON number. Binding (int) 25 raises 3146.
        $count = DB::table('products')
            ->whereNull('deleted_at')
            ->whereRaw(
                "(JSON_CONTAINS(products.attribute, ?, '{$path}') OR JSON_CONTAINS(products.attribute, ?, '{$path}'))",
                ['"' . $brandId . '"', (string) $brandId]
            )
            ->count();

        $this->assertGreaterThan(
            0,
            $count,
            "Expected at least one product tagged with brand id {$brandId}."
        );
    }

    public function test_an_unknown_brand_is_ignored_rather_than_fatal(): void
    {
        $url = $this->catalogueUrl($this->catalogueIdWithBrandProducts());

        $this->get($url . '?brand=khong-ton-tai-' . self::BRAND_CANONICAL)->assertOk();
    }

    private function brandId(): int
    {
        $id = DB::table('attributes as a')
            ->join('attribute_language as al', 'al.attribute_id', '=', 'a.id')
            ->where('a.attribute_catalogue_id', self::BRAND_CATALOGUE_KEY)
            ->where('al.canonical', self::BRAND_CANONICAL)
            ->value('a.id');

        $this->assertNotNull($id, 'No brand attribute found for canonical ' . self::BRAND_CANONICAL);

        return (int) $id;
    }

    /** A catalogue that both has a public route and holds products of the brand. */
    private function catalogueIdWithBrandProducts(): int
    {
        $brandId = $this->brandId();
        $path = '$."' . self::BRAND_CATALOGUE_KEY . '"';

        $catalogueId = DB::table('product_catalogue_product as pcp')
            ->join('products as p', 'p.id', '=', 'pcp.product_id')
            ->join('routers as r', function ($join) {
                $join->on('r.module_id', '=', 'pcp.product_catalogue_id')
                    ->where('r.controllers', 'App\Http\Controllers\Frontend\ProductCatalogueController');
            })
            ->whereNull('p.deleted_at')
            ->whereRaw("JSON_CONTAINS(p.attribute, ?, '{$path}')", ['"' . $brandId . '"'])
            ->value('pcp.product_catalogue_id');

        $this->assertNotNull($catalogueId, 'No routed catalogue contains products of the test brand.');

        return (int) $catalogueId;
    }

    private function catalogueUrl(int $catalogueId): string
    {
        $canonical = DB::table('routers')
            ->where('module_id', $catalogueId)
            ->where('controllers', 'App\Http\Controllers\Frontend\ProductCatalogueController')
            ->value('canonical');

        return '/' . ltrim((string) $canonical, '/') . config('apps.general.suffix');
    }

    /** One `product-grid-item` is rendered per product card in the listing. */
    private function countProductCards(string $html): int
    {
        return preg_match_all('/class="product-grid-item"/', $html);
    }
}
