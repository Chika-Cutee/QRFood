<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// PASTIKAN INI MENGARAH KE FOLDER "Api"
use App\Http\Controllers\Api\AuthController; 
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\PesananController;

// Rute ini akan menjadi .../api/register
Route::post('/register', [AuthController::class, 'register']);

// Rute ini akan menjadi .../api/login
Route::post('/login', [AuthController::class, 'login']);

// Rute ini akan menjadi .../api/products
Route::get('/products', [ProductController::class, 'getProducts']);

// Rute untuk Lupa Password
Route::post('/forgot-password/request', [PasswordResetController::class, 'requestOtp']);
Route::post('/forgot-password/verify', [PasswordResetController::class, 'verifyOtp']);
Route::post('/forgot-password/reset', [PasswordResetController::class, 'resetPassword']);

// Rute ini akan menjadi http://.../api/checkout
Route::post('/checkout', [OrderController::class, 'createOrder']);

Route::post('midtrans-callback', [PesananController::class, 'callback']);


// Route baru untuk mendapatkan status transaksi final
// Route::get('/transaksi/status/{id}', [OrderController::class, 'getTransaksiStatus']);
Route::get('/transaksi/{id}/status', [OrderController::class, 'getTransaksiStatus']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();

});