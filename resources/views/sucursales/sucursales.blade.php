@extends('layouts.layoutDashboard')

@section('titulo')
Administración de Sucursales - TostaTech
@endsection

@section('contenido')
<div class="container-fluid">
    <div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-9">
        <h2>Sucursales</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Inicio</a>
            </li>
            <li class="breadcrumb-item">
                <a href="#">Directorio</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Sucursales</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-3 d-flex align-items-center justify-content-end">
        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalSucursal" 
                style="background-color: #ffb700; border: none; font-weight: 700; color: #000; padding: 8px 20px; border-radius: 8px;">
            <i class="fa fa-plus"></i> AGREGAR SUCURSAL
        </button>
    </div>
</div>

    {{-- Mostrar mensajes de éxito/error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- GRID DE TARJETAS --}}
    <div class="row" id="sucursales-container">
        @forelse($branches as $branch)
        <div class="col-lg-4 col-md-6 col-sm-12 m-b-md" data-branch-id="{{ $branch->id }}">
            <div class="sucursal-card">
                <div class="sucursal-header">
                    <i class="fa fa-store fa-2x sucursal-icon"></i>
                    <span class="status-indicator {{ $branch->status == 'active' ? 'online' : 'offline' }}">
                        {{ $branch->status == 'active' ? 'Activa' : 'Inactiva' }}
                    </span>
                </div>
                <div class="sucursal-body">
                    <h3>{{ $branch->name }}</h3>
                    <p><i class="fa fa-map-marker-alt"></i> {{ $branch->address ?? 'Dirección no registrada' }}</p>
                    <p><i class="fa fa-phone"></i> {{ $branch->phone ?? 'Teléfono no registrado' }}</p>
                    <p><i class="fa fa-building"></i> <strong>Empresa:</strong> {{ $branch->company->name ?? 'N/A' }}</p>
                </div>
                <div class="sucursal-footer">
                    <button class="btn-edit btnEditar" 
                            data-id="{{ $branch->id }}"
                            data-name="{{ $branch->name }}"
                            data-address="{{ $branch->address }}"
                            data-phone="{{ $branch->phone }}"
                            data-id_company="{{ $branch->id_company }}"
                            data-status="{{ $branch->status }}">
                        <i class="fa fa-edit"></i> Editar
                    </button>
                    
                    @if($branch->status == 'active')
                        <button class="btn-delete btnEliminar" data-id="{{ $branch->id }}" data-name="{{ $branch->name }}">
                            <i class="fa fa-trash"></i> Desactivar
                        </button>
                    @else
                        <button class="btn-reactivate btnReactivar" data-id="{{ $branch->id }}" data-name="{{ $branch->name }}">
                            <i class="fa fa-sync-alt"></i> Reactivar
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="fa fa-store fa-4x text-muted mb-3"></i>
            <h4>No hay sucursales registradas</h4>
            <p>Haz clic en "Agregar Nueva Sucursal" para comenzar</p>
        </div>
        @endforelse
    </div>
</div>

{{-- Modal para Crear/Editar Sucursal --}}
<div class="modal fade" id="modalSucursal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalSucursalTitle">Agregar Sucursal</h3>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formSucursal" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="branch_id">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Nombre de la sucursal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="branch_name" name="name" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Empresa <span class="text-danger">*</span></label>
                            <select class="form-control" id="branch_id_company" name="id_company" required>
                                <option value="">Seleccione una empresa</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="branch_phone" name="phone" maxlength="10" placeholder="10 dígitos">
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Dirección</label>
                            <textarea class="form-control" id="branch_address" name="address" rows="2" placeholder="Calle, número, colonia, ciudad, CP"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-control" id="branch_status" name="status">
                                <option value="active">Activa</option>
                                <option value="inactive">Inactiva</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .sucursal-card {
        background-color: #1a1a1a;
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 25px;
        border: 1px solid #333;
        height: 100%;
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

    .btn-edit, .btn-delete, .btn-reactivate {
        flex: 1;
        padding: 8px;
        border-radius: 10px;
        border: none;
        font-weight: 600;
        font-size: 0.85rem;
        transition: 0.2s;
        cursor: pointer;
    }

    .btn-edit { background: #333; color: #fff; }
    .btn-edit:hover { background: #444; }

    .btn-delete { background: rgba(220, 53, 69, 0.2); color: #dc3545; }
    .btn-delete:hover { background: #dc3545; color: #fff; }
    
    .btn-reactivate { background: rgba(40, 167, 69, 0.2); color: #28a745; }
    .btn-reactivate:hover { background: #28a745; color: #fff; }

    /* Animaciones para toasts */
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
    // FUNCIÓN PARA CERRAR MODAL
    // ============================================
    function cerrarModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
            modal.style.display = 'none';
            document.body.classList.remove('modal-open');
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) backdrop.remove();
        }
    }

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
    // FUNCIÓN PARA ACTUALIZAR TARJETA EN TIEMPO REAL
    // ============================================
    function actualizarTarjeta(sucursal) {
        const tarjeta = document.querySelector(`[data-branch-id="${sucursal.id}"]`);
        
        if (tarjeta) {
            const card = tarjeta.querySelector('.sucursal-card');
            if (card) {
                // Actualizar título
                const titulo = card.querySelector('.sucursal-body h3');
                if (titulo) titulo.textContent = sucursal.name;
                
                // Actualizar dirección
                const direccion = card.querySelector('.sucursal-body p:first-child');
                if (direccion) direccion.innerHTML = `<i class="fa fa-map-marker-alt"></i> ${sucursal.address || 'Dirección no registrada'}`;
                
                // Actualizar teléfono
                const telefono = card.querySelector('.sucursal-body p:nth-child(2)');
                if (telefono) telefono.innerHTML = `<i class="fa fa-phone"></i> ${sucursal.phone || 'Teléfono no registrado'}`;
                
                // Actualizar empresa
                const empresa = card.querySelector('.sucursal-body p:nth-child(3)');
                if (empresa) empresa.innerHTML = `<i class="fa fa-building"></i> <strong>Empresa:</strong> ${sucursal.company?.name || 'N/A'}`;
                
                // Actualizar estado
                const statusBadge = card.querySelector('.status-indicator');
                if (statusBadge) {
                    statusBadge.className = `status-indicator ${sucursal.status === 'active' ? 'online' : 'offline'}`;
                    statusBadge.textContent = sucursal.status === 'active' ? 'Activa' : 'Inactiva';
                }
                
                // Actualizar botones
                const footer = card.querySelector('.sucursal-footer');
                const btnEditar = footer.querySelector('.btnEditar');
                const btnEliminar = footer.querySelector('.btnEliminar');
                const btnReactivar = footer.querySelector('.btnReactivar');
                
                if (btnEditar) {
                    btnEditar.setAttribute('data-name', sucursal.name);
                    btnEditar.setAttribute('data-address', sucursal.address || '');
                    btnEditar.setAttribute('data-phone', sucursal.phone || '');
                    btnEditar.setAttribute('data-id_company', sucursal.id_company);
                    btnEditar.setAttribute('data-status', sucursal.status);
                }
                
                if (sucursal.status === 'active') {
                    if (btnEliminar) btnEliminar.style.display = 'flex';
                    if (btnReactivar) btnReactivar.style.display = 'none';
                } else {
                    if (btnEliminar) btnEliminar.style.display = 'none';
                    if (btnReactivar) btnReactivar.style.display = 'flex';
                }
            }
        } else {
            location.reload();
        }
    }

    // ============================================
    // MANEJAR MODAL PARA CREAR/EDITAR
    // ============================================
    const modal = new bootstrap.Modal(document.getElementById('modalSucursal'));
    const form = document.getElementById('formSucursal');
    const modalTitle = document.getElementById('modalSucursalTitle');
    const formMethod = document.getElementById('formMethod');
    
    // Abrir modal para crear
    document.querySelector('[data-bs-target="#modalSucursal"]').addEventListener('click', function() {
        modalTitle.textContent = 'Agregar Sucursal';
        formMethod.value = 'POST';
        form.action = "{{ route('sucursales.guardar') }}";
        form.reset();
        document.getElementById('branch_id').value = '';
        document.getElementById('branch_status').value = 'active';
    });
    
    // Editar sucursal
    document.querySelectorAll('.btnEditar').forEach(btn => {
        btn.addEventListener('click', function() {
            modalTitle.textContent = 'Editar Sucursal';
            formMethod.value = 'PUT';
            const id = this.getAttribute('data-id');
            form.action = `/sucursales/actualizar/${id}`;
            
            document.getElementById('branch_id').value = id;
            document.getElementById('branch_name').value = this.getAttribute('data-name');
            document.getElementById('branch_address').value = this.getAttribute('data-address') || '';
            document.getElementById('branch_phone').value = this.getAttribute('data-phone') || '';
            document.getElementById('branch_id_company').value = this.getAttribute('data-id_company');
            document.getElementById('branch_status').value = this.getAttribute('data-status');
            
            modal.show();
        });
    });
    
    // Enviar formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        
        const formData = new FormData(this);
        
        fetch(form.action, {
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
                modal.hide();
                
                if (formMethod.value === 'POST') {
                    // Si es nueva, recargar para mostrar la nueva tarjeta
                    showToast('success', data.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    // Si es edición, actualizar en tiempo real
                    actualizarTarjeta(data.data);
                    showToast('success', data.message);
                }
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
    // ELIMINAR/DESACTIVAR SUCURSAL
    // ============================================
    document.querySelectorAll('.btnEliminar').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            
            if (confirm(`⚠️ ¿Estás seguro de desactivar la sucursal "${name}"?\n\nLa sucursal no estará disponible hasta que sea reactivada.`)) {
                const btnOriginal = this;
                const originalHTML = btnOriginal.innerHTML;
                btnOriginal.disabled = true;
                btnOriginal.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                
                fetch(`/sucursales/eliminar/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    btnOriginal.disabled = false;
                    btnOriginal.innerHTML = originalHTML;
                    
                    if (data.success) {
                        const tarjeta = document.querySelector(`[data-branch-id="${id}"]`);
                        if (tarjeta) {
                            const statusBadge = tarjeta.querySelector('.status-indicator');
                            if (statusBadge) {
                                statusBadge.className = 'status-indicator offline';
                                statusBadge.textContent = 'Inactiva';
                            }
                            
                            const btnEliminar = tarjeta.querySelector('.btnEliminar');
                            const btnReactivar = tarjeta.querySelector('.btnReactivar');
                            if (btnEliminar) btnEliminar.style.display = 'none';
                            if (btnReactivar) btnReactivar.style.display = 'flex';
                            
                            const btnEditar = tarjeta.querySelector('.btnEditar');
                            if (btnEditar) btnEditar.setAttribute('data-status', 'inactive');
                        }
                        showToast('success', data.message);
                    } else {
                        showToast('error', data.message);
                    }
                });
            }
        });
    });
    
    // ============================================
    // REACTIVAR SUCURSAL
    // ============================================
    document.querySelectorAll('.btnReactivar').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            
            if (confirm(`✅ ¿Estás seguro de reactivar la sucursal "${name}"?\n\nLa sucursal volverá a estar disponible.`)) {
                const btnOriginal = this;
                const originalHTML = btnOriginal.innerHTML;
                btnOriginal.disabled = true;
                btnOriginal.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                
                fetch(`/sucursales/reactivar/${id}`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    btnOriginal.disabled = false;
                    btnOriginal.innerHTML = originalHTML;
                    
                    if (data.success) {
                        const tarjeta = document.querySelector(`[data-branch-id="${id}"]`);
                        if (tarjeta) {
                            const statusBadge = tarjeta.querySelector('.status-indicator');
                            if (statusBadge) {
                                statusBadge.className = 'status-indicator online';
                                statusBadge.textContent = 'Activa';
                            }
                            
                            const btnEliminar = tarjeta.querySelector('.btnEliminar');
                            const btnReactivar = tarjeta.querySelector('.btnReactivar');
                            if (btnEliminar) btnEliminar.style.display = 'flex';
                            if (btnReactivar) btnReactivar.style.display = 'none';
                            
                            const btnEditar = tarjeta.querySelector('.btnEditar');
                            if (btnEditar) btnEditar.setAttribute('data-status', 'active');
                        }
                        showToast('success', data.message);
                    } else {
                        showToast('error', data.message);
                    }
                });
            }
        });
    });
});
</script>
@endsection