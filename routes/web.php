<?php

use App\Http\Controllers\loginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\rutasController;
use Illuminate\Support\Facades\Route;

Route::get('/', [loginController::class, 'showLoginForm'])->name('login');

Route::get('/dashboard', [rutasController::class, 'dashboardAdmin'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
