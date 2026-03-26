@extends('layouts.layoutDashboard')

@section('titulo', 'Inventario - TostaTech')

@section('contenido')

{{-- Header --}}
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-9">
        <h2>Control de Inventario</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Inicio</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#">Operaciones</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Inventario</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-3 d-flex align-items-center justify-content-end">
        <select class="form-select me-2" id="filterBranch" style="width: 200px; border-radius: 8px;">
            <option value="">Todas las sucursales</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
            @endforeach
        </select>
        <button class="btn btn-warning" id="btnExportar" style="background-color: #ffb700; border: none; font-weight: 700; color: #000; padding: 8px 20px; border-radius: 8px;">
            <i class="fa fa-download"></i> Exportar
        </button>
    </div>
</div>

{{-- Tabla de inventario --}}
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content" style="background: #1a1a1a; border-radius: 20px; padding: 0; overflow: hidden;">
                    <div class="table-responsive">
                        <table class="table table-hover" style="color: white; margin-bottom: 0;" id="tablaInventario">
                            <thead style="background: #222;">
                                <tr style="color: #ffb700; border-bottom: 2px solid #333;">
                                    <th class="p-3">PRODUCTO</th>
                                    <th>SUCURSAL</th>
                                    <th>STOCK ACTUAL</th>
                                    <th>ESTADO</th>
                                    <th class="text-center">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody id="inventario-body">
                                @forelse($inventories as $item)
                                <tr data-inventory-id="{{ $item->id }}" data-branch-id="{{ $item->id_branch }}" data-product-id="{{ $item->id_product }}">
                                    <td class="p-3">
                                        <strong>{{ $item->product->name ?? 'Producto no encontrado' }}</strong>
                                    </td>
                                    <td>{{ $item->branch->name ?? 'Sucursal no encontrada' }}</td>
                                    <td>
                                        <span style="font-size: 1.1rem; font-weight: 700;" class="stock-value">
                                            {{ $item->stock }}
                                        </span> unidades
                                    </td>
                                    <td>
                                        @php
                                            $status = $item->stockStatus;
                                        @endphp
                                        <span class="badge stock-badge" style="background: {{ $status['class'] == 'critical' ? '#dc3545' : ($status['class'] == 'low' ? '#ffc107' : '#28a745') }}; color: {{ $status['class'] == 'low' ? '#000' : '#fff' }};">
                                            {{ $status['text'] }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btnAjustar" 
                                                data-id="{{ $item->id }}"
                                                data-branch-id="{{ $item->id_branch }}"
                                                data-branch-name="{{ $item->branch->name }}"
                                                data-product-id="{{ $item->id_product }}"
                                                data-product-name="{{ $item->product->name }}"
                                                data-stock="{{ $item->stock }}"
                                                style="background: #ffb700; color: #000; border-radius: 8px;">
                                            <i class="fa fa-sync"></i> Ajustar Stock
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="fa fa-boxes fa-4x text-muted mb-3"></i>
                                        <h4>No hay productos registrados en inventario</h4>
                                        <p>Agrega productos o ajusta stock para comenzar</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal para ajustar stock --}}
<div class="modal fade" id="modalAjustarStock" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Ajustar Stock</h3>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAjustarStock" method="POST">
                @csrf
                <input type="hidden" name="id" id="inventory_id">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Producto</label>
                        <input type="text" class="form-control" id="producto_nombre" readonly disabled>
                        <input type="hidden" name="id_product" id="id_product">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Sucursal</label>
                        <input type="text" class="form-control" id="sucursal_nombre" readonly disabled>
                        <input type="hidden" name="id_branch" id="id_branch">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Stock Actual</label>
                        <input type="text" class="form-control" id="stock_actual" readonly disabled>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tipo de Movimiento <span class="text-danger">*</span></label>
                        <select class="form-control" name="movement_type" id="movement_type" required>
                            <option value="">Seleccione</option>
                            <option value="in">Entrada (+) - Agregar stock</option>
                            <option value="out">Salida (-) - Quitar stock</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Cantidad <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="quantity" id="quantity" min="1" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Motivo</label>
                        <textarea class="form-control" name="reason" id="reason" rows="2" placeholder="Ej: Compra a proveedor, Devolución, Ajuste físico..."></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
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
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .custom-toast {
        animation: slideIn 0.3s ease-out;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ============================================
    // FUNCIÓN PARA MOSTRAR TOAST
    // ============================================
    function showToast(type, message) {
        const existingToasts = document.querySelectorAll('.custom-toast');
        existingToasts.forEach(toast => toast.remove());
        
        const toast = document.createElement('div');
        toast.className = `custom-toast toast-show ${type === 'success' ? 'toast-success' : 'toast-error'}`;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 300px;
            background-color: ${type === 'success' ? '#28a745' : '#dc3545'};
            color: white;
            padding: 16px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            animation: slideIn 0.3s ease-out;
        `;
        
        toast.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}" style="font-size: 20px;"></i>
                <span style="font-size: 14px;">${message}</span>
            </div>
            <button type="button" style="background: none; border: none; color: white; font-size: 20px; cursor: pointer;" onclick="this.parentElement.remove()">×</button>
        `;
        
        document.body.appendChild(toast);
        setTimeout(() => {
            if (toast && toast.parentElement) {
                toast.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => toast.remove(), 300);
            }
        }, 3000);
    }

    // ============================================
    // FUNCIÓN PARA ACTUALIZAR FILA EN TIEMPO REAL
    // ============================================
    function actualizarFila(data) {
        const fila = document.querySelector(`tr[data-product-id="${data.id_product}"][data-branch-id="${data.id_branch}"]`);
        
        if (fila) {
            const stockCell = fila.querySelector('.stock-value');
            if (stockCell) {
                stockCell.textContent = data.stock;
            }
            
            const badgeCell = fila.querySelector('.stock-badge');
            if (badgeCell) {
                const stockStatus = data.stock <= 10 ? 'Stock Crítico' : (data.stock <= 30 ? 'Stock Bajo' : 'Suficiente');
                const stockClass = data.stock <= 10 ? '#dc3545' : (data.stock <= 30 ? '#ffc107' : '#28a745');
                const textColor = data.stock <= 30 && data.stock > 10 ? '#000' : '#fff';
                badgeCell.style.background = stockClass;
                badgeCell.style.color = textColor;
                badgeCell.textContent = stockStatus;
            }
            
            const btnAjustar = fila.querySelector('.btnAjustar');
            if (btnAjustar) {
                btnAjustar.setAttribute('data-stock', data.stock);
            }
        } else {
            location.reload();
        }
    }

    // ============================================
    // ABRIR MODAL PARA AJUSTAR STOCK
    // ============================================
    document.querySelectorAll('.btnAjustar').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const branchId = this.getAttribute('data-branch-id');
            const branchName = this.getAttribute('data-branch-name');
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            const stock = this.getAttribute('data-stock');
            
            document.getElementById('inventory_id').value = id;
            document.getElementById('id_branch').value = branchId;
            document.getElementById('id_product').value = productId;
            document.getElementById('producto_nombre').value = productName;
            document.getElementById('sucursal_nombre').value = branchName;
            document.getElementById('stock_actual').value = stock + ' unidades';
            document.getElementById('movement_type').value = '';
            document.getElementById('quantity').value = '';
            document.getElementById('reason').value = '';
            
            const modal = new bootstrap.Modal(document.getElementById('modalAjustarStock'));
            modal.show();
        });
    });

    // ============================================
    // ENVIAR FORMULARIO DE AJUSTE
    // ============================================
    const form = document.getElementById('formAjustarStock');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        
        const formData = new FormData(this);
        
        fetch("{{ route('inventario.ajustar') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            
            if (data.success) {
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalAjustarStock'));
                modal.hide();
                
                actualizarFila(data.data.inventory);
                showToast('success', data.message);
            } else {
                showToast('error', data.message);
            }
        })
        .catch(error => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            showToast('error', 'Error de conexión');
        });
    });

    // ============================================
    // FILTRAR POR SUCURSAL
    // ============================================
    const filterBranch = document.getElementById('filterBranch');
    filterBranch.addEventListener('change', function() {
        const branchId = this.value;
        
        fetch("{{ route('inventario.filtrar') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ branch_id: branchId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tbody = document.getElementById('inventario-body');
                tbody.innerHTML = '';
                
                if (data.data.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fa fa-boxes fa-4x text-muted mb-3"></i>
                                <h4>No hay productos en esta sucursal</h4>
                            </td>
                        </tr>
                    `;
                } else {
                    data.data.forEach(item => {
                        const stockStatus = item.stock <= 10 ? 'Stock Crítico' : (item.stock <= 30 ? 'Stock Bajo' : 'Suficiente');
                        const stockClass = item.stock <= 10 ? '#dc3545' : (item.stock <= 30 ? '#ffc107' : '#28a745');
                        const textColor = item.stock <= 30 && item.stock > 10 ? '#000' : '#fff';
                        
                        const row = `
                            <tr data-inventory-id="${item.id}" data-branch-id="${item.id_branch}" data-product-id="${item.id_product}">
                                <td class="p-3"><strong>${item.product?.name || 'Producto no encontrado'}</strong></td>
                                <td>${item.branch?.name || 'Sucursal no encontrada'}</td>
                                <td><span style="font-size: 1.1rem; font-weight: 700;" class="stock-value">${item.stock}</span> unidades</td>
                                <td><span class="badge stock-badge" style="background: ${stockClass}; color: ${textColor};">${stockStatus}</span></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btnAjustar" 
                                            data-id="${item.id}"
                                            data-branch-id="${item.id_branch}"
                                            data-branch-name="${item.branch?.name || ''}"
                                            data-product-id="${item.id_product}"
                                            data-product-name="${item.product?.name || ''}"
                                            data-stock="${item.stock}"
                                            style="background: #ffb700; color: #000; border-radius: 8px;">
                                        <i class="fa fa-sync"></i> Ajustar Stock
                                    </button>
                                </td>
                            </tr>
                        `;
                        tbody.insertAdjacentHTML('beforeend', row);
                    });
                    
                    // Reasignar eventos a los nuevos botones
                    document.querySelectorAll('.btnAjustar').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const id = this.getAttribute('data-id');
                            const branchId = this.getAttribute('data-branch-id');
                            const branchName = this.getAttribute('data-branch-name');
                            const productId = this.getAttribute('data-product-id');
                            const productName = this.getAttribute('data-product-name');
                            const stock = this.getAttribute('data-stock');
                            
                            document.getElementById('inventory_id').value = id;
                            document.getElementById('id_branch').value = branchId;
                            document.getElementById('id_product').value = productId;
                            document.getElementById('producto_nombre').value = productName;
                            document.getElementById('sucursal_nombre').value = branchName;
                            document.getElementById('stock_actual').value = stock + ' unidades';
                            document.getElementById('movement_type').value = '';
                            document.getElementById('quantity').value = '';
                            document.getElementById('reason').value = '';
                            
                            const modal = new bootstrap.Modal(document.getElementById('modalAjustarStock'));
                            modal.show();
                        });
                    });
                }
            }
        });
    });

    // ============================================
    // EXPORTAR A EXCEL
    // ============================================
    document.getElementById('btnExportar').addEventListener('click', function() {
        const tabla = document.getElementById('tablaInventario');
        let html = '<table border="1">';
        html += '<thead><tr><th>Producto</th><th>Sucursal</th><th>Stock Actual</th><th>Estado</th></tr></thead><tbody>';
        
        const filas = tabla.querySelectorAll('tbody tr');
        filas.forEach(fila => {
            if (fila.cells.length >= 4) {
                html += '<tr>';
                html += `<td>${fila.cells[0]?.innerText || ''}</td>`;
                html += `<td>${fila.cells[1]?.innerText || ''}</td>`;
                html += `<td>${fila.cells[2]?.innerText || ''}</td>`;
                html += `<td>${fila.cells[3]?.innerText || ''}</td>`;
                html += '</tr>';
            }
        });
        
        html += '</tbody></table>';
        
        const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `inventario_${new Date().toISOString().slice(0, 19)}.xls`;
        link.click();
        URL.revokeObjectURL(link.href);
        
        showToast('success', 'Reporte exportado exitosamente');
    });
});
</script>
@endsection