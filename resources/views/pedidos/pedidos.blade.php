@extends('layouts.layoutDashboard')

@section('titulo')
    Pedidos - TostaTech
@endsection

@section('contenido')
<style>
    /* Estilos Dark para la tabla de Pedidos */
    .ibox-content {
        background-color: #1a1a1a !important;
        border: none !important;
        border-radius: 8px;
    }

    .table-tostatech {
        background-color: #1a1a1a !important;
        color: #e0e0e0 !important;
    }

    .table-tostatech thead th {
        color: #ffb700 !important;
        border-bottom: 2px solid #ff7e00 !important;
    }

    .table-tostatech tbody tr {
        border-bottom: 1px solid #333;
    }

    .table-tostatech tbody tr:hover {
        background-color: #252525 !important;
    }

    .badge-status {
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: bold;
    }
</style>

<div class="row wrapper border-bottom white-bg page-heading" style="background: #ffffff !important; border-bottom: 1px solid #333 !important;">
    <div class="col-lg-10">
        <h2 style="color: #000000;">Gestión de Pedidos</h2>
        <ol class="breadcrumb" style="background: transparent;">
            <li class="breadcrumb-item"><a href="/dashboard" style="color: #000000;">Inicio</a></li>
            <li class="breadcrumb-item active" style="color: #000000;"><strong>Pedidos</strong></li>
        </ol>
    </div>
    <div class="col-lg-2 text-right" style="padding-top: 30px;">
        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalPedido" style="background: #ff7e00; border: none;">
            <i class="fa fa-plus"></i> Nuevo Pedido
        </button>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-tostatech table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Simulación de datos --}}
                                <tr>
                                    <td>#1001</td>
                                    <td>Juan Pérez</td>
                                    <td>2026-03-22</td>
                                    <td>$250.00</td>
                                    <td><span class="badge badge-primary" style="background: #00c853;">Completado</span></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-warning"><i class="fa fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>#1002</td>
                                    <td>Maria López</td>
                                    <td>2026-03-22</td>
                                    <td>$120.00</td>
                                    <td><span class="badge badge-warning" style="background: #ffb700; color: black;">Pendiente</span></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-warning"><i class="fa fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPedido" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background: #1a1a1a; color: white; border: 1px solid #ff7e00;">
            <div class="modal-header" style="border-bottom: 1px solid #333;">
                <h5 class="modal-title" style="color: #ffb700;">Datos del Pedido</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formPedido">
                    <div class="mb-3">
                        <label class="form-label">Cliente</label>
                        <input type="text" class="form-control" style="background: #333; color: white; border: 1px solid #444;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select class="form-control" style="background: #333; color: white; border: 1px solid #444;">
                            <option>Pendiente</option>
                            <option>En Preparación</option>
                            <option>Completado</option>
                            <option>Cancelado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Total ($)</label>
                        <input type="number" class="form-control" style="background: #333; color: white; border: 1px solid #444;">
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #333;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" style="background: #ff7e00; border: none;">Guardar Pedido</button>
            </div>
        </div>
    </div>
</div>
@endsection