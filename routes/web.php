<?php

use App\Http\Controllers\API\AuthController as ApiAuthController;
use App\Http\Controllers\API\DashboardController as ApiDashboardController;
use App\Http\Controllers\API\PersonalController as ApiPersonalController;
use App\Http\Controllers\API\ClientesController as ApiClientesController;
use App\Http\Controllers\API\PedidosController as ApiPedidosController;
use App\Http\Controllers\API\InventarioController as ApiInventarioController;
use App\Http\Controllers\API\SucursalesController as ApiSucursalesController;
use App\Http\Controllers\branchController;
use App\Http\Controllers\inventarioController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\pedidosController;
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


    ///////////////////////////////////////////////////////////////////////////
    /////////////////////////// MODULO SUCURSALES /////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
Route::middleware('auth')->group(function () {
    Route::prefix('sucursales')->group(function () {
        Route::get('/', [branchController::class, 'index'])->name('sucursales.index');
        Route::post('/guardar', [branchController::class, 'store'])->name('sucursales.guardar');
        Route::put('/actualizar/{id}', [branchController::class, 'update'])->name('sucursales.actualizar');
        Route::delete('/eliminar/{id}', [branchController::class, 'destroy'])->name('sucursales.eliminar');
        Route::patch('/reactivar/{id}', [branchController::class, 'reactivate'])->name('sucursales.reactivar');
    });
});

// Módulo de Inventario
Route::middleware('auth')->group(function () {
    Route::prefix('inventario')->group(function () {
        Route::get('/', [inventarioController::class, 'index'])->name('inventario.index');
        Route::post('/ajustar', [inventarioController::class, 'ajustarStock'])->name('inventario.ajustar');
        Route::get('/movimientos/{branch?}/{product?}', [inventarioController::class, 'getMovements'])->name('inventario.movimientos');
        Route::post('/filtrar', [inventarioController::class, 'filterByBranch'])->name('inventario.filtrar');
    });
});

// Módulo de Pedidos
Route::middleware('auth')->group(function () {
    Route::prefix('pedidos')->group(function () {
        Route::get('/', [pedidosController::class, 'index'])->name('pedidos.index');
        Route::post('/guardar', [pedidosController::class, 'store'])->name('pedidos.guardar');
        Route::put('/actualizar/{id}', [pedidosController::class, 'update'])->name('pedidos.actualizar');
        Route::get('/ver/{id}', [pedidosController::class, 'show'])->name('pedidos.ver');
        Route::get('/filtrar/{status?}', [pedidosController::class, 'filterByStatus'])->name('pedidos.filtrar');
    });
});


// Ruta para visualizar la vista de productos
Route::get('/productos', function () {
    // Apunta a la carpeta 'productos' y al archivo 'productos.blade.php'
    return view('productos.productos'); 
});


// Ruta temporal solo para previsualizar la vista de carrito (cliente.carrito)
Route::get('/tienda', function () {
    return view('cliente.carrito');
});

///////////////////////////////////////////////////////////////////////////////
/////////////////////////// RUTAS API MÓVIL ///////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

Route::prefix('api')->group(function () {
    Route::post('/login', [ApiAuthController::class, 'login']);

    Route::middleware([
        'force.json.accept',
        'auth',
    ])->group(function () {
        Route::post('/logout', [ApiAuthController::class, 'logout']);
        Route::get('/me', [ApiAuthController::class, 'me']);
        Route::get('/dashboard', [ApiDashboardController::class, 'index']);

        Route::prefix('/personal')->group(function () {
            Route::get('/', [ApiPersonalController::class, 'index']);
            Route::get('/catalogos', [ApiPersonalController::class, 'catalog']);
            Route::get('/{id}', [ApiPersonalController::class, 'show']);
            Route::post('/', [ApiPersonalController::class, 'store']);
            Route::put('/{id}', [ApiPersonalController::class, 'update']);
            Route::patch('/{id}/estado', [ApiPersonalController::class, 'updateStatus']);
        });

        Route::prefix('/clientes')->group(function () {
            Route::get('/', [ApiClientesController::class, 'index']);
            Route::get('/{id}', [ApiClientesController::class, 'show']);
            Route::post('/', [ApiClientesController::class, 'store']);
            Route::put('/{id}', [ApiClientesController::class, 'update']);
            Route::patch('/{id}/estado', [ApiClientesController::class, 'updateStatus']);
        });

        Route::prefix('/pedidos')->group(function () {
            Route::get('/', [ApiPedidosController::class, 'index']);
            Route::get('/{id}', [ApiPedidosController::class, 'show']);
            Route::get('/{id}/detalle', [ApiPedidosController::class, 'showDetail']);
            Route::post('/', [ApiPedidosController::class, 'store']);
            Route::put('/{id}', [ApiPedidosController::class, 'update']);
            Route::patch('/{id}/estado', [ApiPedidosController::class, 'updateStatus']);
        });

        Route::prefix('/inventario')->group(function () {
            Route::get('/', [ApiInventarioController::class, 'index']);
            Route::get('/catalogos', [ApiInventarioController::class, 'catalog']);
            Route::get('/movimientos', [ApiInventarioController::class, 'movements']);
            Route::post('/productos', [ApiInventarioController::class, 'storeProduct']);
            Route::get('/{id}', [ApiInventarioController::class, 'show']);
            Route::post('/', [ApiInventarioController::class, 'store']);
            Route::put('/{id}', [ApiInventarioController::class, 'update']);
            Route::patch('/{id}/stock', [ApiInventarioController::class, 'updateStock']);
            Route::post('/movimientos', [ApiInventarioController::class, 'storeMovement']);
        });

        Route::prefix('/sucursales')->group(function () {
            Route::get('/', [ApiSucursalesController::class, 'index']);
            Route::get('/catalogos', [ApiSucursalesController::class, 'catalog']);
            Route::get('/{id}', [ApiSucursalesController::class, 'show']);
            Route::post('/', [ApiSucursalesController::class, 'store']);
            Route::put('/{id}', [ApiSucursalesController::class, 'update']);
            Route::patch('/{id}/estado', [ApiSucursalesController::class, 'updateStatus']);
            Route::get('/{id}/operacion', [ApiSucursalesController::class, 'operation']);
        });

    });
});
