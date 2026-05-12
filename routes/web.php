<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

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
