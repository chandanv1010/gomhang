<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * RouterController dispatches catalogue/product/post pages to the controller
 * named in the `routers` table. It used to `echo` the result instead of
 * returning it, which left the HTTP response body empty: the markup went
 * straight to PHP's output stream, so a browser still rendered the page while
 * Laravel reported a 0-byte response.
 *
 * That broke anything that reads the response rather than the output stream -
 * response caching, middleware post-processing, Content-Length, and feature
 * tests.
 */
class RouterControllerResponseTest extends TestCase
{
    /** @dataProvider routedControllers */
    public function test_routed_pages_return_their_markup_in_the_response(string $controller): void
    {
        $url = $this->firstUrlFor($controller);

        $response = $this->get($url);

        $response->assertOk();
        $this->assertNotSame(
            '',
            $response->getContent(),
            "{$url} returned an empty body - the controller result is not being returned."
        );
        $response->assertSee('</html>', false);
    }

    public function test_an_unknown_canonical_still_returns_404(): void
    {
        $this->get('/canonical-khong-ton-tai' . config('apps.general.suffix'))
            ->assertNotFound();
    }

    public static function routedControllers(): array
    {
        return [
            'product catalogue' => ['App\Http\Controllers\Frontend\ProductCatalogueController'],
            'product' => ['App\Http\Controllers\Frontend\ProductController'],
            'post' => ['App\Http\Controllers\Frontend\PostController'],
            'post catalogue' => ['App\Http\Controllers\Frontend\PostCatalogueController'],
        ];
    }

    private function firstUrlFor(string $controller): string
    {
        $canonical = DB::table('routers')
            ->where('controllers', $controller)
            ->where('language_id', 1)
            ->orderBy('id')
            ->value('canonical');

        $this->assertNotNull($canonical, "No route registered for {$controller}.");

        return '/' . ltrim((string) $canonical, '/') . config('apps.general.suffix');
    }
}
