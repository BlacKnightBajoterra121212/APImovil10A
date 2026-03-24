@extends('layouts.layoutDashboard')

@section('titulo', 'Inventario - TostaTech')

@section('contenido')
<div class="container-fluid" style="background: #ffffff; min-height: 100vh; padding: 30px;">
    
    <div class="d-flex justify-content-between align-items-center m-b-lg">
        <div>
            <h2 style="font-weight: 800; color: #000;">📦 Control de Inventario</h2>
            <p style="color: #666;">Stock disponible por sucursal</p>
        </div>
        <div class="d-flex gap-2">
            {{-- Filtro rápido por sucursal --}}
            <select class="form-select" style="border-radius: 10px; border: 1px solid #ffb700;">
                <option selected>Todas las sucursales</option>
                <option>TostaTech Centro</option>
                <option>TostaTech Norte</option>
            </select>
            <button class="btn" style="background: #000; color: #ffb700; font-weight: 700; border-radius: 10px; margin-left: 10px;">
                <i class="fa fa-download"></i> Exportar
            </button>
        </div>
    </div>

    <div class="row m-t-md">
        <div class="col-lg-12">
            <div class="ibox shadow-lg">
                <div class="ibox-content" style="background: #1a1a1a; border-radius: 20px; padding: 0; overflow: hidden;">
                    <table class="table table-hover" style="color: white; margin-bottom: 0;">
                        <thead style="background: #222;">
                            <tr style="color: #ffb700; border-bottom: 2px solid #333;">
                                <th class="p-3">PRODUCTO</th>
                                <th>SUCURSAL</th>
                                <th>CATEGORÍA</th>
                                <th>STOCK ACTUAL</th>
                                <th>ESTADO</th>
                                <th class="text-right p-3">ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Ejemplo de fila --}}
                            <tr style="border-bottom: 1px solid #333;">
                                <td class="p-3"><strong>Tostada Clásica</strong></td>
                                <td>Centro</td>
                                <td>Tostadas</td>
                                <td><span style="font-size: 1.1rem; font-weight: 700;">150</span> unidades</td>
                                <td><span class="badge" style="background: #28a745;">Suficiente</span></td>
                                <td class="text-right p-3">
                                    <button class="btn btn-xs" style="background: #ffb700; color: #000;"><i class="fa fa-sync"></i> Ajustar</button>
                                </td>
                            </tr>
                            {{-- Ejemplo de stock bajo --}}
                            <tr style="border-bottom: 1px solid #333;">
                                <td class="p-3"><strong>Salsa Especial</strong></td>
                                <td>Norte</td>
                                <td>Complementos</td>
                                <td><span style="font-size: 1.1rem; font-weight: 700; color: #ff5555;">12</span> litros</td>
                                <td><span class="badge" style="background: #dc3545;">Stock Crítico</span></td>
                                <td class="text-right p-3">
                                    <button class="btn btn-xs" style="background: #ffb700; color: #000;"><i class="fa fa-sync"></i> Ajustar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table-hover tbody tr:hover {
        background-color: rgba(255, 183, 0, 0.05) !important;
    }
    .badge {
        padding: 5px 12px;
        border-radius: 10px;
        font-weight: 600;
    }
    .ibox { border: none; }
</style>
@endsection