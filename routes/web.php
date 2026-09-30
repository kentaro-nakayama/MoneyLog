<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;


Route::get('/', [HomeController::class, 'showHome'])->name('home');
Route::get('/register_form', [RegisterController::class,'showRegisterForm'])->name('register_form');
Route::post('/register', [RegisterController::class, 'register'])->name('register');
