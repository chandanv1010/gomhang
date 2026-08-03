<?php  
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\V1\User\AuthController;

/* FRONTEND AJAX ROUTE */
Route::get('admin', [AuthController::class, 'index'])->name('auth.admin')->middleware('login');
Route::get('logout', [AuthController::class, 'logout'])->name('auth.logout');
Route::post('login', [AuthController::class, 'login'])->name('auth.login');


