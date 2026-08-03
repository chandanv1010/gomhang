<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\RouterController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\Payment\VnpayController;
use App\Http\Controllers\Frontend\Payment\PaypalController;
use App\Http\Controllers\Frontend\ProductCatalogueController as FeProductCatalogueController;
use App\Http\Controllers\Frontend\ContactController as FeContactController;
use App\Http\Controllers\CrawlerController;

//@@useController@@

require __DIR__ . '/web/user.route.php';
require __DIR__ . '/web/customer.route.php';
require __DIR__ . '/web/core.route.php';
require __DIR__ . '/web/product.route.php';
require __DIR__ . '/web/post.route.php';
require __DIR__ . '/web/auth.route.php';
require __DIR__ . '/web/ajax.route.php';
require __DIR__ . '/web/custom.route.php';

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
/* FRONTEND ROUTES  */
Route::group(['middleware' => ['locale']], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home.index');
    Route::get('lien-he.html', [FeContactController::class, 'index'])->name('contact.index');
    Route::get('/thumb', [App\Http\Controllers\ImageResizerController::class, 'resize'])->name('thumb');

    Route::get('tim-kiem', [FeProductCatalogueController::class, 'search'])->name('product.catalogue.search');
    Route::get('tim-kiem/trang-{page}', [FeProductCatalogueController::class, 'search'])->name('product.catalogue.search')->where('page', '[0-9]+');
    Route::get('yeu-thich' . config('apps.general.suffix'), [FeProductCatalogueController::class, 'wishlist'])->name('product.wishlist.index');
    Route::get('so-sanh' . config('apps.general.suffix'), [FeProductCatalogueController::class, 'compare'])->name('product.compare.index');

    Route::get('crawler', [CrawlerController::class, 'crawl'])->name('crawl.index');

    /** CART */

    Route::group(['middleware' => ['customer_auth']], function () {
        Route::get('gio-hang' . config('apps.general.suffix'), [CartController::class, 'checkout'])->name('cart.checkout');
        Route::get('thanh-toan' . config('apps.general.suffix'), [CartController::class, 'pay'])->name('cart.pay');
        Route::post('cart/create', [CartController::class, 'store'])->name('cart.store');
        Route::post('cart/createPay', [CartController::class, 'storePay'])->name('cart.storePay');
        Route::get('cart/success' . config('apps.general.suffix'), [CartController::class, 'success'])->name('cart.success');
    });


    /* PORT PAYMENT */
    Route::get('return/vnpay' . config('apps.general.suffix'), [VnpayController::class, 'vnpay_return'])->name('vnpay.momo_return');
    Route::get('return/vnpay_ipn' . config('apps.general.suffix'), [VnpayController::class, 'vnpay_ipn'])->name('vnpay.vnpay_ipn');


    Route::get('paypal/success' . config('apps.general.suffix'), [PaypalController::class, 'success'])->name('paypal.success');
    Route::get('paypal/cancel' . config('apps.general.suffix'), [PaypalController::class, 'cancel'])->name('paypal.cancel');

    /** DYNAMIC ROUTE */
    Route::get('{canonical}' . config('apps.general.suffix'), [RouterController::class, 'index'])->name('router.index')->where('canonical', '[a-zA-Z0-9-]+');
    Route::get('{canonical}/trang-{page}' . config('apps.general.suffix'), [RouterController::class, 'page'])->name('router.page')->where('canonical', '[a-zA-Z0-9-]+')->where('page', '[0-9]+');

    /*Schools*/
});
