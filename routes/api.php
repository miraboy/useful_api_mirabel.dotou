<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ModuleController;

Route::get('/register',[AuthController::class, 'register']);
Route::get('/login',[AuthController::class, 'login']);
Route::get('/user', [AuthController::class, 'get_user'])->middleware('auth:sanctum');

Route::get('/modules/{id}/activate', [ModuleController::class, 'activation'])->middleware('auth:sanctum');
Route::get('/modules/{id}/deactivate', [ModuleController::class, 'desactivation'])->middleware('auth:sanctum');

// Route::get('/test', function () {
//     return "ça marche";
// })->middleware('auth:sanctum', 'module.active:URL Shortener');   