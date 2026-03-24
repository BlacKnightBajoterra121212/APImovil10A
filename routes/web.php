<?php

use App\Http\Controllers\dashboardController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\rutasController;
use App\Http\Controllers\userController;
use Illuminate\Support\Facades\Route;

Route::get('/', [loginController::class, 'showLoginForm'])->name('login');

Route::get('/dashboard', [rutasController::class, 'dashboardAdmin'])->name('dashboard');

///////////////////////////////////////////////////////////////////////////////
/////////////////////////// RUTAS DE DIRECTORIO ///////////////////////////////
///////////////////////////////////////////////////////////////////////////////
Route::prefix('personal')->group(function () {
    // Vista principal
    Route::get('/', [userController::class, 'showPersonal'])->name('personal');
    
    // Búsqueda
    Route::get('/buscar', [userController::class, 'buscarPersonal'])->name('personal.buscar');
    
    // CRUD
    Route::post('/guardar', [userController::class, 'guardarPersonal'])->name('personal.guardar');
    Route::put('/actualizar/{id}', [userController::class, 'actualizarPersonal'])->name('personal.actualizar');
    Route::delete('/eliminar/{id}', [userController::class, 'eliminarPersonal'])->name('personal.eliminar');
    
    // Obtener datos (para editar vía AJAX)
    Route::get('/obtener/{id}', [userController::class, 'obtenerPersonal'])->name('personal.obtener');
    
    // Reactivar usuario
    Route::patch('/reactivar/{id}', [userController::class, 'reactivarPersonal'])->name('personal.reactivar');
});

///////////////////////////////////////////////////////////////////////////////
/////////////////////////// RUTAS DE DIRECTORIO ///////////////////////////////
///////////////////////////////////////////////////////////////////////////////
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::get('/pedidos', function () {
    // 'pedidos.pedidos' significa: carpeta pedidos, archivo pedidos.blade.php
    return view('pedidos.pedidos');
});
require __DIR__.'/auth.php';


Route::get('/sucursales', function () {
    // Apunta a la carpeta 'sucursales' y al archivo 'sucursales.blade.php'
    return view('sucursales.sucursales'); 
})->name('sucursales.index');