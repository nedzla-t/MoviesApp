<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieController;

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {

    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->middleware('auth:api')->name('me');
    Route::apiResource('movies', MovieController::class);
    Route::post('movies/{movie}/favorite', [MovieController::class, 'favorite']);
    Route::get('movies-favorites', [MovieController::class, 'getFavorites']);
    Route::post('movies/{movie}/follow', [MovieController::class, 'follow']);
    Route::get('movies-follows', [MovieController::class, 'getFollows']);

});
