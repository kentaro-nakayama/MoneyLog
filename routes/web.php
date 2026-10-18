<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AuthController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.store');
    Route::get('/signup', [AuthController::class, 'showSignupForm'])->name('signup');
    Route::post('/signup', [AuthController::class, 'signup'])->middleware('throttle:5,1')->name('signup.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'showHome'])->name('home');
    Route::get('/register_form', [TransactionController::class, 'showRegisterForm'])->name('register_form');
    Route::post('/register', [TransactionController::class, 'registerTransaction'])->name('register');
    Route::get('/edit_form/{id}', [TransactionController::class, 'showEditForm'])->name('edit_form');
    Route::post('/edit/{id}', [TransactionController::class, 'editTransaction'])->name('edit');
    Route::post('/delete/{id}', [TransactionController::class, 'deleteTransaction'])->name('delete');
});
