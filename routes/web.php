<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;


Route::get('/', [HomeController::class, 'showHome'])->name('home');
Route::get('/register', [RegisterController::class,'showRegister'])->name('register');
