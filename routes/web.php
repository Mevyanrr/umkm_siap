<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController as WebAuthController;
use App\Http\Controllers\Web\AssessmentWebController;
use App\Http\Controllers\Web\CatalogWebController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\MarketWebController;
use App\Http\Controllers\Web\BuyerController;
use App\Http\Controllers\Web\WhistlistController;
use App\Http\Controllers\Web\BuyerProfileController;

// Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Redirect default Laravel auth ke login umkm
Route::redirect('/login', '/login/umkm')->name('login');


// ======================
// REGISTER
// ======================

Route::get('/register/umkm', function () {
    return view('auth.register_umkm');
})->name('register.umkm');

Route::post('/register/umkm', [WebAuthController::class, 'register'])
    ->name('register.umkm.post');

Route::get('/register/buyer', function () {
    return view('auth.register_buyer');
})->name('register.buyer');

Route::post('/register/buyer', [WebAuthController::class, 'register'])
    ->name('register.buyer.post');


// ======================
// LOGIN
// ======================

Route::get('/login/umkm', function () {
    return view('auth.login_umkm');
})->name('login.umkm');

Route::post('/login/umkm', [WebAuthController::class, 'login'])
    ->name('login.umkm.post');

Route::get('/login/buyer', function () {
    return view('auth.login_buyer');
})->name('login.buyer');

Route::post('/login/buyer', [WebAuthController::class, 'login'])
    ->name('login.buyer.post');


// ======================
// PROTECTED ROUTES
// ======================

Route::middleware('auth')->group(function () {

    // Dashboard UMKM
    Route::get('/umkm/dashboard', function () {
        return view('dashboard.umkm');
    })->name('dashboard.umkm');

    // BUYER
    // Dashboard Buyer & Saved Products
    Route::get('/buyer/dashboard', [BuyerController::class, 'dashboard'])
        ->name('dashboard.buyer');

    Route::post('/wishlist/toggle', [WhistlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/buyer/saved', [WhistlistController::class, 'savedProducts'])->name('buyer.saved');

    // Toggle wishlist (AJAX)
    Route::post('/wishlist/toggle', [WhistlistController::class, 'toggle'])
        ->middleware('auth')
        ->name('wishlist.toggle');

    // Buyer Profile
    Route::get('/buyer/profile', [BuyerProfileController::class, 'index'])
        ->name('buyer.profile');

    Route::put('/buyer/profile', [BuyerProfileController::class, 'update'])
        ->name('buyer.profile.update');

    // Assessment
    Route::get('/umkm/assessment', [AssessmentWebController::class, 'index'])
        ->name('umkm.assessment');

    Route::get('/umkm/assessment/result', [AssessmentWebController::class, 'result'])
        ->name('umkm.assessment.result');

    // Katalog B2B
    Route::get('/umkm/catalog', [CatalogWebController::class, 'index'])
        ->name('umkm.catalog');

    // Produk Saya
    Route::get('/umkm/products', [ProductController::class, 'index'])
        ->name('umkm.produk_saya');

    Route::post('/umkm/products', [ProductController::class, 'store'])
        ->name('umkm.produk.store');

    Route::put('/umkm/products/{id}', [ProductController::class, 'update'])
        ->name('umkm.produk.update');

    Route::delete('/umkm/products/{id}', [ProductController::class, 'destroy'])
        ->name('umkm.produk.destroy');

    // Profil UMKM
    Route::get('/umkm/profile', [ProfileController::class, 'index'])
        ->name('umkm.profile');

    Route::put('/umkm/profile', [ProfileController::class, 'update'])
        ->name('umkm.profile.update');

    // Market Intelligence
    Route::get('/umkm/market', [MarketWebController::class, 'index'])
        ->name('umkm.market');
});


// ======================
// LOGOUT & AJAX
// ======================

Route::post('/logout', [WebAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');