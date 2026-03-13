<?php

use App\Http\Controllers\dashboardController;
use App\Http\Controllers\loginController;
use Illuminate\Support\Facades\Route;

Route::get('/', [loginController::class, 'showLoginForm'])->name('loginvista');
Route::get('/dashboard', [dashboardController::class, 'index'])->name('dashboard');