<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TransactionController;

Route::get('/', [HomeController::class, 'showHome'])->name('home');

Route::get('/register_form', [TransactionController::class, 'showRegisterForm'])->name('register_form');
Route::post('/register', [TransactionController::class, 'registerTransaction'])->name('register');
Route::get('/edit_form/{id}', [TransactionController::class, 'showEditForm'])->name('edit_form');
Route::post('/edit/{id}', [TransactionController::class, 'editTransaction'])->name('edit');
Route::post('/delete/{id}', [TransactionController::class, 'deleteTransaction'])->name('delete');
