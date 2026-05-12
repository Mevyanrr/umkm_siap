<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Web\AssessmentWebController;

// Landing Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Register
Route::get('/register/umkm', function () {
    return view('auth.register_umkm');
})->name('register.umkm');

Route::post('/register/umkm', [AuthController::class, 'register'])->name('register.umkm.post');

Route::get('/register/buyer', function () {
    return view('auth.register_buyer');
})->name('register.buyer');

Route::post('/register/buyer', [AuthController::class, 'register'])->name('register.buyer.post');

// Login
Route::get('/login/umkm', function () {
    return view('auth.login_umkm');
})->name('login.umkm');

Route::get('/login/buyer', function () {
    return view('auth.login_buyer');
})->name('login.buyer');

// UMKM Routes
Route::prefix('umkm')->group(function () {
    Route::get('/assessment',        [AssessmentWebController::class, 'index'])->name('umkm.assessment');
    Route::get('/assessment/result', [AssessmentWebController::class, 'result'])->name('umkm.assessment.result');

    // Placeholder routes
    Route::get('/dashboard', fn() => view('umkm.dashboard'))->name('umkm.dashboard');
    Route::get('/market',    fn() => view('umkm.market'))->name('umkm.market');
    Route::get('/catalog',   fn() => view('umkm.catalog'))->name('umkm.catalog');
    Route::get('/products',  fn() => view('umkm.products'))->name('umkm.products');
    Route::get('/profile',   fn() => view('umkm.profile'))->name('umkm.profile');
    Route::get('/settings',  fn() => view('umkm.settings'))->name('umkm.settings');
});