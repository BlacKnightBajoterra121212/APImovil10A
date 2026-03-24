@extends('layouts.layoutDashboard')

@section('titulo')
Administración de Sucursales - TostaTech
@endsection

@section('contenido')
<div class="container-fluid">
    {{-- Encabezado --}}
    <div class="row m-b-lg">
        <div class="col-lg-12 d-flex justify-content-between align-items-center" style="margin-bottom: 30px;">
            <div>
                <h2 style="color: #000; font-weight: 800; margin: 0;">📍 Directorio de Sucursales</h2>
                <p style="color: #666;">Visualiza y gestiona los puntos de venta de TostaTech</p>
            </div>
            <button class="btn btn-warning" style="background-color: #ffb700; border: none; font-weight: 700; color: #000; padding: 10px 20px; border-radius: 10px;">
                <i class="fa fa-plus"></i> AGREGAR NUEVA SUCURSAL
            </button>
        </div>
    </div>

    {{-- GRID DE TARJETAS (CARDS) --}}
    <div class="row">
        
        {{-- Tarjeta de Ejemplo 1 --}}
        <div class="col-lg-4 col-md-6 col-sm-12 m-b-md">
            <div class="sucursal-card">
                <div class="sucursal-header">
                    <i class="fa fa-store fa-2x sucursal-icon"></i>
                    <span class="status-indicator online">Activa</span>
                </div>
                <div class="sucursal-body">
                    <h3>TostaTech Centro</h3>
                    <p><i class="fa fa-map-marker-alt"></i> Av. Reforma #45, Col. Centro, CP 06000</p>
                    <p><i class="fa fa-phone"></i> (555) 123-4567</p>
                    <p><i class="fa fa-user-tie"></i> <strong>Encargado:</strong> Juan Pérez</p>
                </div>
                <div class="sucursal-footer">
                    <button class="btn-edit"><i class="fa fa-edit"></i> Editar</button>
                    <button class="btn-delete"><i class="fa fa-trash"></i> Eliminar</button>
                </div>
            </div>
        </div>

        {{-- Tarjeta de Ejemplo 2 --}}
        <div class="col-lg-4 col-md-6 col-sm-12 m-b-md">
            <div class="sucursal-card">
                <div class="sucursal-header">
                    <i class="fa fa-store fa-2x sucursal-icon"></i>
                    <span class="status-indicator online">Activa</span>
                </div>
                <div class="sucursal-body">
                    <h3>TostaTech Norte</h3>
                    <p><i class="fa fa-map-marker-alt"></i> Plaza Galería, Local 12-B</p>
                    <p><i class="fa fa-phone"></i> (555) 987-6543</p>
                    <p><i class="fa fa-user-tie"></i> <strong>Encargado:</strong> Ana López</p>
                </div>
                <div class="sucursal-footer">
                    <button class="btn-edit"><i class="fa fa-edit"></i> Editar</button>
                    <button class="btn-delete"><i class="fa fa-trash"></i> Eliminar</button>
                </div>
            </div>
        </div>

        {{-- Tarjeta de Ejemplo 3 --}}
        <div class="col-lg-4 col-md-6 col-sm-12 m-b-md">
            <div class="sucursal-card">
                <div class="sucursal-header">
                    <i class="fa fa-store fa-2x sucursal-icon"></i>
                    <span class="status-indicator offline">Cerrada</span>
                </div>
                <div class="sucursal-body">
                    <h3>TostaTech Sur</h3>
                    <p><i class="fa fa-map-marker-alt"></i> Av. Insurgentes Sur #102</p>
                    <p><i class="fa fa-phone"></i> (555) 456-7890</p>
                    <p><i class="fa fa-user-tie"></i> <strong>Encargado:</strong> Por asignar</p>
                </div>
                <div class="sucursal-footer">
                    <button class="btn-edit"><i class="fa fa-edit"></i> Editar</button>
                    <button class="btn-delete"><i class="fa fa-trash"></i> Eliminar</button>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    /* Estilos de las Tarjetas (Cards) */
    .sucursal-card {
        background-color: #1a1a1a; /* Fondo oscuro como tus gráficas */
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 25px;
        border: 1px solid #333;
    }

    .sucursal-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(255, 183, 0, 0.15);
        border-color: #ffb700;
    }

    .sucursal-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
    }

    .sucursal-icon {
        color: #ffb700;
    }

    .status-indicator {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .status-indicator.online { background: #28a745; color: #fff; }
    .status-indicator.offline { background: #dc3545; color: #fff; }

    .sucursal-body h3 {
        color: #ffb700;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .sucursal-body p {
        color: #bbb;
        font-size: 0.9rem;
        margin-bottom: 8px;
    }

    .sucursal-body p i {
        color: #ffb700;
        width: 20px;
        margin-right: 10px;
    }

    .sucursal-footer {
        margin-top: 25px;
        display: flex;
        gap: 10px;
    }

    .btn-edit, .btn-delete {
        flex: 1;
        padding: 8px;
        border-radius: 10px;
        border: none;
        font-weight: 600;
        font-size: 0.85rem;
        transition: 0.2s;
    }

    .btn-edit { background: #333; color: #fff; }
    .btn-edit:hover { background: #444; }

    .btn-delete { background: rgba(220, 53, 69, 0.2); color: #dc3545; }
    .btn-delete:hover { background: #dc3545; color: #fff; }
</style>
@endsection