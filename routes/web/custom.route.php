<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\AuthController;
use App\Http\Controllers\Frontend\CustomerController;
use App\Http\Controllers\Ajax\CartController;


Route::post('/dang-xuat.html', [AuthController::class, 'logout'])->name('customer.logout');
Route::get('/dang-ky.html', [AuthController::class, 'register'])->name('customer.register');
Route::post('/dang-ky.html', [AuthController::class, 'registerAccount'])->name('customer.doregister');
Route::get('/dang-nhap.html', [AuthController::class, 'index'])->name('customer.login');
Route::post('/dang-nhap.html', [AuthController::class, 'login'])->name('customer.dologin');
Route::get('/gioi-thieu.html', [\App\Http\Controllers\Frontend\AboutController::class, 'index'])->name('about.index');
Route::get('/cong-nghe.html', [\App\Http\Controllers\Frontend\TechnologyController::class, 'index'])->name('frontend.technology.index');
Route::get('/phan-phoi.html', [\App\Http\Controllers\Frontend\DistributionController::class, 'index'])->name('frontend.distribution.index');
Route::get('/ajax/distribution/getProvinces', [\App\Http\Controllers\Frontend\DistributionController::class, 'getProvinces'])->name('frontend.ajax.distribution.getProvinces');
// Route::get('/tai-khoan.html', [CustomerController::class, 'profile'])->name('customer.account');


/*
|--------------------------------------------------------------------------
| CUSTOMER PROFILE – CẦN LOGIN
|--------------------------------------------------------------------------
*/

Route::middleware(['customer_auth'])->group(function () {

    // Hồ sơ cá nhân
    Route::get('/tai-khoan.html', [CustomerController::class, 'profile'])->name('customer.account');

    // Cập nhật thông tin cá nhân
    Route::post('/tai-khoan/cap-nhat.html', [CustomerController::class, 'updateProfile'])
        ->name('customer.profile.update');

    // Upload avatar
    Route::post('/tai-khoan/upload-avatar.html', [CustomerController::class, 'uploadAvatar'])
        ->name('customer.avatar.upload');

    // Trang thay đổi mật khẩu
    Route::get('/tai-khoan/thay-doi-mat-khau.html', [CustomerController::class, 'changePassword'])
        ->name('customer.password');

    // Trang lịch sử đơn hàng
    Route::get('/tai-khoan/lich-su-don-hang.html', [CustomerController::class, 'historyOrder'])
        ->name('customer.order');

    // Lịch sử giao dịch điểm
    Route::get('/tai-khoan/lich-su-giao-dich.html', [CustomerController::class, 'pointHistory'])
        ->name('customer.point.history');

    // Chi tiết đơn hàng
    Route::get('/tai-khoan/don-hang/{code}.html', [CustomerController::class, 'orderDetail'])
        ->name('customer.order.detail');

    // Xử lý đổi mật khẩu
    Route::post('/tai-khoan/thay-doi-mat-khau.html', [CustomerController::class, 'updatePassword'])
        ->name('customer.password.update');

    Route::post('/ajax/customer/uploadAvatar', [CustomerController::class, 'uploadAvatar'])
        ->name('customer.avatar.upload.ajax');

    Route::post('ajax/cart/checkPoint', [CartController::class, 'checkPoint']);


    /*
    |--------------------------------------------------------------------------
    | ORDERS – ĐƠN HÀNG
    |--------------------------------------------------------------------------
    */

    // Danh sách đơn hàng đã mua
    // Route::get('/don-hang.html', [OrderController::class, 'index'])
    //     ->name('customer.orders');

    // Chi tiết 1 đơn hàng
    // Route::get('/don-hang/{code}.html', [OrderController::class, 'detail'])
    //     ->name('customer.orders.detail');

    // Lịch sử giao dịch / điểm tích lũy / hoạt động tài khoản
    // Route::get('/lich-su-giao-dich.html', [CustomerController::class, 'history'])
    //     ->name('customer.history');
});
