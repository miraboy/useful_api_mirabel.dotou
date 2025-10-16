<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/register',[AuthController::class, 'register']);
Route::get('/login',[AuthController::class, 'login']);
Route::get('/user', [AuthController::class, 'get_user'])->middleware('auth:sanctum');
