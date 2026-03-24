<?php

use App\Http\Controllers\loginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\rutasController;
use App\Http\Controllers\userController;
use Illuminate\Support\Facades\Route;

///////////////////////////////////////////////////////////////////////////////
/////////////////////////// RUTAS PÚBLICAS ////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

// Ruta principal (redirige a login)
Route::get('/', [loginController::class, 'showLoginForm']);

// Rutas de autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [loginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [loginController::class, 'login']);
});

///////////////////////////////////////////////////////////////////////////////
/////////////////////////// RUTAS PROTEGIDAS //////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

Route::middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [rutasController::class, 'dashboardAdmin'])->name('dashboard');
    
    // Logout
    Route::post('/logout', [loginController::class, 'logout'])->name('logout');
    
    ///////////////////////////////////////////////////////////////////////////
    /////////////////////////// MÓDULO DE PERSONAL ////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    Route::prefix('personal')->group(function () {
        Route::get('/', [userController::class, 'showPersonal'])->name('personal');
        Route::get('/buscar', [userController::class, 'buscarPersonal'])->name('personal.buscar');
        Route::post('/guardar', [userController::class, 'guardarPersonal'])->name('personal.guardar');
        Route::put('/actualizar/{id}', [userController::class, 'actualizarPersonal'])->name('personal.actualizar');
        Route::delete('/eliminar/{id}', [userController::class, 'eliminarPersonal'])->name('personal.eliminar');
        Route::get('/obtener/{id}', [userController::class, 'obtenerPersonal'])->name('personal.obtener');
        Route::patch('/reactivar/{id}', [userController::class, 'reactivarPersonal'])->name('personal.reactivar');
    });
    
    ///////////////////////////////////////////////////////////////////////////
    /////////////////////////// PERFIL DE USUARIO /////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
});

require __DIR__.'/auth.php';


Route::get('/sucursales', function () {
    // Apunta a la carpeta 'sucursales' y al archivo 'sucursales.blade.php'
    return view('sucursales.sucursales'); 
})->name('sucursales.index');