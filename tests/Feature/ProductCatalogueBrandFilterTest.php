<?php

namespace Tests\Feature;

use App\Repositories\Product\ProductCatalogueRepository;
use App\Services\V1\Product\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Regression coverage for the brand filter on product catalogue pages.
 *
 * Brands are stored in the JSON `products.attribute` column under the "8" key
 * (attribute catalogue "Thương hiệu"):
 *
 *     {"8": ["25"]}
 *
 * MySQL's JSON_CONTAINS() rejects a non-string candidate argument with
 * SQLSTATE[22032] 3146, so every bound candidate must be JSON *text* - never a
 * PHP int. Binding (int) 25 used to make the whole catalogue page 500.
 *
 * Note: catalogue pages are dispatched through RouterController, which `echo`s
 * the view instead of returning it, so the HTTP response body is empty under
 * PHPUnit. These tests therefore assert on the status code (which still proves
 * no database exception was thrown) and on the service layer directly.
 */
class ProductCatalogueBrandFilterTest extends TestCase
{
    /** Attribute catalogue holding brands ("Thương hiệu"). */
    private const BRAND_CATALOGUE_KEY = '8';

    private const BRAND_CANONICAL = 'apple';

    public function test_brand_filter_page_does_not_throw_a_database_error(): void
    {
        $catalogueId = $this->catalogueIdWithBrandProducts();

        $this->get($this->catalogueUrl($catalogueId) . '?brand=' . self::BRAND_CANONICAL)
            ->assertOk();
    }

    public function test_an_unknown_brand_is_ignored_rather_than_fatal(): void
    {
        $catalogueId = $this->catalogueIdWithBrandProducts();

        $this->get($this->catalogueUrl($catalogueId) . '?brand=thuong-hieu-khong-ton-tai')
            ->assertOk();
    }

    public function test_brand_filter_narrows_the_paginated_result_set(): void
    {
        $catalogueId = $this->catalogueIdWithBrandProducts();

        $unfiltered = $this->paginateCatalogue($catalogueId, null)->total();
        $filtered = $this->paginateCatalogue($catalogueId, self::BRAND_CANONICAL)->total();

        $this->assertGreaterThan(0, $filtered, 'The brand filter matched no products at all.');
        $this->assertLessThan(
            $unfiltered,
            $filtered,
            'The brand filter returned every product, so it is not filtering.'
        );
    }

    public function test_brand_filter_accepts_a_numeric_brand_id_as_well_as_a_canonical(): void
    {
        $catalogueId = $this->catalogueIdWithBrandProducts();

        $byCanonical = $this->paginateCatalogue($catalogueId, self::BRAND_CANONICAL)->total();
        $byId = $this->paginateCatalogue($catalogueId, (string) $this->brandId())->total();

        $this->assertSame($byCanonical, $byId, 'Filtering by brand id and by canonical disagree.');
    }

    public function test_json_contains_accepts_both_string_and_number_candidates(): void
    {
        $brandId = $this->brandId();
        $path = '$."' . self::BRAND_CATALOGUE_KEY . '"';

        // '"25"' parses as a JSON string, '25' parses as a JSON number. Both are
        // valid because they are bound as text; an int binding raises 3146.
        $count = DB::table('products')
            ->whereNull('deleted_at')
            ->whereRaw(
                "(JSON_CONTAINS(products.attribute, ?, '{$path}') OR JSON_CONTAINS(products.attribute, ?, '{$path}'))",
                ['"' . $brandId . '"', (string) $brandId]
            )
            ->count();

        $this->assertGreaterThan(0, $count, "No product is tagged with brand id {$brandId}.");
    }

    private function paginateCatalogue(int $catalogueId, ?string $brand)
    {
        $languageId = 1;

        $catalogue = app(ProductCatalogueRepository::class)
            ->getProductCatalogueById($catalogueId, $languageId);

        $request = Request::create('/', 'GET', $brand === null ? [] : ['brand' => $brand]);

        return app(ProductService::class)->paginate(
            $request,
            $languageId,
            $catalogue,
            1,
            ['path' => $catalogue->canonical]
        );
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
        $path = '$."' . self::BRAND_CATALOGUE_KEY . '"';

        $catalogueId = DB::table('product_catalogue_product as pcp')
            ->join('products as p', 'p.id', '=', 'pcp.product_id')
            ->join('routers as r', function ($join) {
                $join->on('r.module_id', '=', 'pcp.product_catalogue_id')
                    ->where('r.controllers', 'App\Http\Controllers\Frontend\ProductCatalogueController');
            })
            ->whereNull('p.deleted_at')
            ->whereRaw("JSON_CONTAINS(p.attribute, ?, '{$path}')", ['"' . $this->brandId() . '"'])
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
}
