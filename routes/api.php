<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Services\Cart\CartManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', UserController::class);
    Route::apiResource('order', OrderController::class)->only(['index', 'store']);
});

Route::middleware('no_token')->group(function () {
    Route::post('register', RegisterController::class)->name('register');
    Route::post('login', LoginController::class)->name('login');
});


Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class,'index'])->name('cart.index');
    Route::post('add', [CartController::class, 'add'])->name('cart.add');
    Route::delete('remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('clear', [CartController::class, 'clear'])->name('cart.clear');
});


Route::apiResource('products', ProductController::class)->only('index','show');
