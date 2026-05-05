<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\MarketController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login',    [AuthController::class, 'login']);
        Route::post('refresh',  [AuthController::class, 'refresh'])->middleware('auth:api');
        Route::post('logout',   [AuthController::class, 'logout'])->middleware('auth:api');
    });

    Route::middleware('auth:api')->group(function () {

        Route::prefix('assessment')->group(function () {
            Route::get('questions', [AssessmentController::class, 'questions']);
            Route::post('submit',   [AssessmentController::class, 'submit']);
            Route::post('chat',     [AssessmentController::class, 'chat']);
        });

        Route::prefix('market')->group(function () {
            Route::get('trade-data',        [MarketController::class, 'tradeData']);
            Route::get('trending-products', [MarketController::class, 'trendingProducts']);
        });

        Route::prefix('catalog')->group(function () {
            Route::get('products',         [CatalogController::class, 'index']);
            Route::get('products/{id}',    [CatalogController::class, 'show']);
            Route::post('products',        [CatalogController::class, 'store']);
            Route::put('products/{id}',    [CatalogController::class, 'update']);
            Route::delete('products/{id}', [CatalogController::class, 'destroy']);
        });
    });
});
