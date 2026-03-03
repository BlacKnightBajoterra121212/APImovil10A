<?php

use App\Http\Controllers\loginController;
use Illuminate\Support\Facades\Route;

Route::get('/', [loginController::class, 'showLoginForm'])->name('loginvista');

