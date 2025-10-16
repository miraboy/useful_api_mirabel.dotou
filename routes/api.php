<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\UrlShortenerController;

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

// Route::get('/test', function () {
//     return "ça marche";
// })->middleware('auth:sanctum', 'module.active:URL Shortener');   