<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\UrlShortenerController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\MarketplaceController;

Route::post('/register',[AuthController::class, 'register']);
Route::post('/login',[AuthController::class, 'login']);
Route::get('/user', [AuthController::class, 'get_user'])->middleware('auth:sanctum');

Route::get('/modules', [ModuleController::class, 'getUserModules'])->middleware('auth:sanctum');

Route::post('/modules/{id}/activate', [ModuleController::class, 'activation'])->middleware('auth:sanctum');
Route::post('/modules/{id}/deactivate', [ModuleController::class, 'desactivation'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'module.active:URL Shortener'])->group(function () {
    Route::post('/shorten', [UrlShortenerController::class, 'shorten']);
    Route::get('/links', [UrlShortenerController::class, 'index']);
    Route::delete('/links/{id}', [UrlShortenerController::class, 'destroy']);
});

Route::get('/s/{code}', [UrlShortenerController::class, 'redirect']);      

Route::middleware(['auth:sanctum', 'module.active:Wallet'])->group(function () {
    Route::get('/wallet', [WalletController::class, 'show']);
    Route::post('/wallet/transfer', [WalletController::class, 'transfer']);
    Route::post('/wallet/topup', [WalletController::class, 'topup']);
    Route::get('/wallet/transactions', [WalletController::class, 'transactions']);
});   

Route::middleware(['auth:sanctum', 'module.active:Marketplace'])->group(function () {
    Route::post('/products', [MarketplaceController::class, 'createProduct']);
    Route::get('/products', [MarketplaceController::class, 'getProducts']);
    Route::post('/orders', [MarketplaceController::class, 'createOrder']);
    Route::post('/products/{id}/restock', [MarketplaceController::class, 'restock']);
});   

// Route::get('/test', function () {
//     return "ça marche";
// })->middleware('auth:sanctum', 'module.active:URL Shortener');   