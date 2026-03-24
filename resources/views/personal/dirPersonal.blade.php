@extends('layouts.layoutDashboard')

@section('titulo')
    Dashboard Admin
@endsection

@section('contenido')

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-9">
            <h2>Personal</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="dashboard">Inicio</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="">Directorio</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Personal</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-3 d-flex align-items-center justify-content-end">
            <ul class="nav nav-tabs" style="justify-content: flex-end; border: none; gap: 10px;">
                {{-- Botón crear usuario --}}
                <li>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario"
                        style="background-color: #ffb700; border: none; font-weight: 700; color: #000; padding: 8px 20px; border-radius: 8px;">
                        <i class="fa fa-user"></i>
                    </button>
                </li>

                {{-- Botón buscar usuario --}}
                <li>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                        data-bs-target="#modalBuscarUsuario"
                        style="background-color: #ffb700; border: none; font-weight: 700; color: #000; padding: 8px 20px; border-radius: 8px;">
                        <i class="fa fa-search"></i>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    {{-- Mostrar mensajes de éxito/error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Componente de Tabla de Personal --}}
    <div class="clients-list">

        {{-- Área para mensajes de búsqueda --}}
        @if(session('busqueda'))
            <div class="mt-3 d-flex justify-content-between align-items-center px-3">
                <div class="alert alert-info mb-0">
                    <i class="fas fa-search"></i> Resultados de búsqueda para:
                    <strong>{{ session('busqueda') }}</strong>
                </div>
                <a href="{{ route('personal') }}" class="btn btn-warning">
                    <i class="fas fa-sync-alt"></i> Mostrar todo
                </a>
            </div>
        @endif

        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox">
                        <div class="ibox-content">
                            {{-- Mensaje cuando no hay datos --}}
                            @if($personal->isEmpty())
                                <div class="text-center py-5">
                                    <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                    <h3 class="text-muted">No hay empleados disponibles</h3>
                                    <p class="text-muted">Haz clic en el botón <i class="fa fa-user"></i> para agregar un nuevo
                                        empleado</p>
                                </div>
                            @else
                                {{-- Tabla de datos --}}
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" id="tablaPersonal">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nombre</th>
                                                <th>Email</th>
                                                <th>Rol</th>
                                                <th>Teléfono</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($personal as $data)
                                                <tr data-user-id="{{ $data->id }}">
                                                    <td>{{ $data->id }}</td>
                                                    <td>
                                                        <strong>{{ $data->name }}</strong>
                                                    </td>
                                                    <td>{{ $data->email }}</td>
                                                    <td>
                                                        @if($data->role)
                                                            <span class="badge bg-primary rounded-pill px-3 py-2">
                                                                <i class="fas fa-user-tag me-1"></i>
                                                                {{ $data->role->name }}
                                                            </span>
                                                        @else
                                                            <span class="badge bg-secondary rounded-pill px-3 py-2">
                                                                <i class="fas fa-question-circle me-1"></i>
                                                                Sin asignar
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $data->phone ?? 'No registrado' }}</td>
                                                    <td>
                                                        @if($data->status == 'active')
                                                            <span class="badge bg-success rounded-pill px-3 py-2">
                                                                <i class="fas fa-check-circle me-1"></i>
                                                                Activo
                                                            </span>
                                                        @else
                                                            <span class="badge bg-danger rounded-pill px-3 py-2">
                                                                <i class="fas fa-times-circle me-1"></i>
                                                                Inactivo
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-primary btn-sm btnEditar"
                                                            data-bs-toggle="modal" data-bs-target="#modalEditarUsuario"
                                                            data-id="{{ $data->id }}" data-name="{{ $data->name }}"
                                                            data-email="{{ $data->email }}" data-id_role="{{ $data->id_role }}"
                                                            data-id_company="{{ $data->id_company }}"
                                                            data-id_branch="{{ $data->id_branch }}" data-phone="{{ $data->phone }}"
                                                            data-status="{{ $data->status }}">
                                                            <i class="fas fa-edit"></i> Editar
                                                        </button>

                                                        @if($data->status == 'active')
                                                            <button type="button" class="btn btn-danger btn-sm btnEliminar"
                                                                data-id="{{ $data->id }}" data-name="{{ $data->name }}">
                                                                <i class="fas fa-trash-alt"></i> Desactivar
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-success btn-sm btnReactivar"
                                                                data-id="{{ $data->id }}" data-name="{{ $data->name }}">
                                                                <i class="fas fa-sync-alt"></i> Reactivar
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Paginación --}}
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <p class="text">
                                            Mostrando {{ $personal->firstItem() }} a {{ $personal->lastItem() }} de
                                            {{ $personal->total() }} empleados
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-end">
                                            {{ $personal->links('pagination::bootstrap-5') }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para crear usuario --}}
    <div class="modal fade" id="modalCrearUsuario" tabindex="-1" aria-labelledby="modalCrearUsuarioLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Registrar usuario</h3>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form action="{{ route('personal.guardar') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            {{-- Columna izquierda --}}
                            <div class="col-md-6">
                                {{-- Nombre completo --}}
                                <div class="input-group mb-3">
                                    <span class="input-group-text text-danger"><i class="fa fa-user"></i></span>
                                    <input type="text" class="form-control" name="name" placeholder="Nombre completo"
                                        required>
                                </div>

                                {{-- Email --}}
                                <div class="input-group mb-3">
                                    <span class="input-group-text text-danger"><i class="fa fa-envelope"></i></span>
                                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                                </div>

                                {{-- Teléfono --}}
                                <div class="input-group mb-3">
                                    <span class="input-group-text text-danger"><i class="fab fa-whatsapp"></i></span>
                                    <input type="tel" class="form-control no-spinner" id="numcel" name="phone"
                                        placeholder="Teléfono" maxlength="10" inputmode="numeric">
                                </div>
                            </div>

                            {{-- Columna derecha --}}
                            <div class="col-md-6">
                                {{-- Rol --}}
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-user-tag"></i>
                                    </span>
                                    <select class="form-control" name="id_role" required>
                                        <option value="" disabled selected>Seleccionar Rol del Sistema</option>
                                        @foreach($roles as $rol)
                                            <option value="{{ $rol->id }}">{{ $rol->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Compañía --}}
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    <select class="form-control" name="id_company" required>
                                        <option value="" disabled selected>Seleccionar Compañía</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Sucursal (opcional) --}}
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <i class="fas fa-store"></i>
                                    </span>
                                    <select class="form-control" name="id_branch">
                                        <option value="" selected>Sin sucursal</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Campos ocultos --}}
                        <input type="hidden" name="password" value="12345">
                        <input type="hidden" name="status" value="active">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal para buscar usuario --}}
    <div class="modal fade" id="modalBuscarUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Busca un usuario por nombre</h3>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form action="{{ route('personal.buscar') }}" method="GET">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nombre">Nombre del usuario:</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal para Editar Usuario --}}
    <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="modalEditarUsuarioLabel">Editar Usuario</h3>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="formEditarUsuario" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_id" name="id">

                    <div class="modal-body">
                        <div class="row">
                            {{-- Nombre completo --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                            </div>

                            {{-- Teléfono --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="edit_phone" name="phone" maxlength="10"
                                    placeholder="10 dígitos">
                            </div>

                            {{-- Rol --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rol <span class="text-danger">*</span></label>
                                <select class="form-control" name="id_role" id="edit_id_role" required>
                                    <option value="">Selecciona un Rol</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}">{{ $rol->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Compañía --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Compañía <span class="text-danger">*</span></label>
                                <select class="form-control" name="id_company" id="edit_id_company" required>
                                    <option value="">Selecciona una Compañía</option>
                                    @foreach($companies ?? [] as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Sucursal (opcional) --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sucursal</label>
                                <select class="form-control" name="id_branch" id="edit_id_branch">
                                    <option value="">Sin sucursal</option>
                                    @foreach($branches ?? [] as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Estado --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado <span class="text-danger">*</span></label>
                                <select id="edit_status" name="status" class="form-control" required>
                                    <option value="active">Activo</option>
                                    <option value="inactive">Inactivo</option>
                                </select>
                            </div>

                            {{-- Contraseña (opcional) --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Contraseña <small class="text-muted">(Dejar en blanco para
                                        mantener la actual)</small></label>
                                <input type="password" class="form-control" id="edit_password" name="password"
                                    placeholder="Nueva contraseña">
                                <small class="text-muted">Mínimo 6 caracteres</small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .nav.nav-tabs>li {
            margin-right: 12px !important;
        }

        /* Ajuste del encabezado de página para que no sea blanco */
        .page-heading {
            background: #e9e9e9 !important;
            border-bottom: 1px solid #333 !important;
            color: #1d1c1b !important;
        }

        .breadcrumb {
            background: transparent !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ============================================
            // LLENAR DATOS EN LA MODAL DE EDICIÓN
            // ============================================
            const btnsEditar = document.querySelectorAll('.btnEditar');
            btnsEditar.forEach(btn => {
                btn.addEventListener('click', function () {
                    // Obtener todos los datos del botón
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const email = this.getAttribute('data-email');
                    const id_role = this.getAttribute('data-id_role');
                    const id_company = this.getAttribute('data-id_company');
                    const id_branch = this.getAttribute('data-id_branch');
                    const phone = this.getAttribute('data-phone');
                    const status = this.getAttribute('data-status');

                    // Llenar campos del modal
                    document.getElementById('edit_id').value = id;
                    document.getElementById('edit_name').value = name;
                    document.getElementById('edit_email').value = email;
                    document.getElementById('edit_id_role').value = id_role;
                    document.getElementById('edit_phone').value = phone || '';
                    document.getElementById('edit_status').value = status;

                    // Llenar compañía
                    const companySelect = document.getElementById('edit_id_company');
                    if (companySelect) {
                        companySelect.value = id_company;
                    }

                    // Llenar sucursal
                    const branchSelect = document.getElementById('edit_id_branch');
                    if (branchSelect) {
                        branchSelect.value = id_branch || '';
                    }

                    // Limpiar campo de contraseña
                    const passwordInput = document.getElementById('edit_password');
                    if (passwordInput) {
                        passwordInput.value = '';
                    }

                    // Actualizar la acción del formulario
                    const form = document.getElementById('formEditarUsuario');
                    if (form) {
                        form.action = `/personal/actualizar/${id}`;
                    }
                });
            });

            // ============================================
            // FUNCIÓN PARA CERRAR MODAL
            // ============================================
            function cerrarModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.remove('show');
                    modal.style.display = 'none';
                    document.body.classList.remove('modal-open');

                    // Remover backdrop
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }

                    // Remover clases de Bootstrap
                    modal.setAttribute('aria-hidden', 'true');
                    modal.removeAttribute('aria-modal');
                }
            }

            // ============================================
            // FUNCIÓN PARA MOSTRAR TOAST (SIN BOOTSTRAP)
            // ============================================
            function showToast(type, message) {
                // Remover toasts existentes
                const existingToasts = document.querySelectorAll('.custom-toast');
                existingToasts.forEach(toast => toast.remove());

                // Crear elemento toast
                const toast = document.createElement('div');
                toast.className = `custom-toast toast-show ${type === 'success' ? 'toast-success' : 'toast-error'}`;

                // Estilos del toast
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
                    font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
                `;

                toast.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}" style="font-size: 20px;"></i>
                        <span style="font-size: 14px;">${message}</span>
                    </div>
                    <button type="button" style="
                        background: none;
                        border: none;
                        color: white;
                        font-size: 20px;
                        cursor: pointer;
                        padding: 0 5px;
                        opacity: 0.8;
                    " onclick="this.parentElement.remove()">
                        ×
                    </button>
                `;

                document.body.appendChild(toast);

                // Auto cerrar después de 3 segundos
                setTimeout(() => {
                    if (toast && toast.parentElement) {
                        toast.style.animation = 'slideOut 0.3s ease-out';
                        setTimeout(() => toast.remove(), 300);
                    }
                }, 3000);
            }

            // ============================================
            // FUNCIÓN PARA ACTUALIZAR LA TABLA EN TIEMPO REAL
            // ============================================
            function actualizarFilaEnTabla(usuario) {
                console.log('Actualizando fila con usuario:', usuario);

                // Buscar la fila por el ID del usuario
                const fila = document.querySelector(`tr[data-user-id="${usuario.id}"]`);

                if (fila) {
                    // Actualizar las celdas de la fila
                    const celdas = fila.querySelectorAll('td');

                    if (celdas.length >= 7) {
                        // ID (celda 0)
                        celdas[0].textContent = usuario.id;

                        // Nombre (celda 1)
                        celdas[1].innerHTML = `<strong>${escapeHtml(usuario.name)}</strong>`;

                        // Email (celda 2)
                        celdas[2].textContent = usuario.email;

                        // Rol (celda 3)
                        const rolNombre = usuario.role?.name || 'Sin asignar';
                        celdas[3].innerHTML = `<span class="badge bg-primary rounded-pill px-3 py-2">
                            <i class="fas fa-user-tag me-1"></i> ${escapeHtml(rolNombre)}
                        </span>`;

                        // Teléfono (celda 4)
                        celdas[4].textContent = usuario.phone || 'No registrado';

                        // Estado (celda 5)
                        const estadoClass = usuario.status === 'active' ? 'success' : 'danger';
                        const estadoIcon = usuario.status === 'active' ? 'fa-check-circle' : 'fa-times-circle';
                        const estadoTexto = usuario.status === 'active' ? 'Activo' : 'Inactivo';
                        celdas[5].innerHTML = `<span class="badge bg-${estadoClass} rounded-pill px-3 py-2">
                            <i class="fas ${estadoIcon} me-1"></i> ${estadoTexto}
                        </span>`;

                        // Actualizar botón editar
                        const btnEditar = fila.querySelector('.btnEditar');
                        if (btnEditar) {
                            btnEditar.setAttribute('data-name', usuario.name);
                            btnEditar.setAttribute('data-email', usuario.email);
                            btnEditar.setAttribute('data-id_role', usuario.id_role);
                            btnEditar.setAttribute('data-id_company', usuario.id_company);
                            btnEditar.setAttribute('data-id_branch', usuario.id_branch || '');
                            btnEditar.setAttribute('data-phone', usuario.phone || '');
                            btnEditar.setAttribute('data-status', usuario.status);
                        }

                        // Actualizar botones de desactivar/reactivar
                        const btnEliminar = fila.querySelector('.btnEliminar');
                        const btnReactivar = fila.querySelector('.btnReactivar');

                        if (usuario.status === 'active') {
                            if (btnEliminar) btnEliminar.style.display = 'inline-block';
                            if (btnReactivar) btnReactivar.style.display = 'none';
                        } else {
                            if (btnEliminar) btnEliminar.style.display = 'none';
                            if (btnReactivar) btnReactivar.style.display = 'inline-block';
                        }
                    }
                }
            }

            // Función para escapar HTML
            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // ============================================
            // MANEJAR EL ENVÍO DEL FORMULARIO DE EDICIÓN
            // ============================================
            const editarUsuarioForm = document.getElementById('formEditarUsuario');
            if (editarUsuarioForm) {
                editarUsuarioForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    // Obtener referencias de los elementos
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;

                    // Validaciones
                    const name = document.getElementById('edit_name').value.trim();
                    const email = document.getElementById('edit_email').value.trim();
                    const id_role = document.getElementById('edit_id_role').value;
                    const id_company = document.getElementById('edit_id_company').value;
                    const status = document.getElementById('edit_status').value;

                    if (!name) {
                        showToast('error', 'El nombre completo es requerido');
                        return false;
                    }

                    if (!email) {
                        showToast('error', 'El email es requerido');
                        return false;
                    }

                    if (!id_role) {
                        showToast('error', 'Debe seleccionar un rol');
                        return false;
                    }

                    if (!id_company) {
                        showToast('error', 'Debe seleccionar una compañía');
                        return false;
                    }

                    const password = document.getElementById('edit_password').value;
                    if (password && password.length < 6) {
                        showToast('error', 'La contraseña debe tener al menos 6 caracteres');
                        return false;
                    }

                    // Preparar datos para enviar
                    const formData = new FormData();
                    formData.append('_method', 'PUT');
                    formData.append('name', name);
                    formData.append('email', email);
                    formData.append('phone', document.getElementById('edit_phone').value);
                    formData.append('id_role', id_role);
                    formData.append('id_company', id_company);
                    formData.append('id_branch', document.getElementById('edit_id_branch').value);
                    formData.append('status', status);
                    if (password) {
                        formData.append('password', password);
                    }

                    const url = this.action;

                    // Mostrar loading en el botón
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

                    // Enviar petición
                    fetch(url, {
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
                            // Restaurar botón
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;

                            if (data.success) {
                                // Cerrar el modal manualmente
                                cerrarModal('modalEditarUsuario');

                                // Actualizar la tabla en tiempo real
                                actualizarFilaEnTabla(data.data);

                                // Mostrar mensaje de éxito
                                showToast('success', data.message || 'Usuario actualizado exitosamente');
                            } else {
                                showToast('error', data.message || 'Error al actualizar el usuario');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            // Restaurar botón en caso de error
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                            showToast('error', 'Error de conexión al servidor');
                        });
                });
            }

            // ============================================
            // MANEJAR ELIMINAR (DESACTIVAR)
            // ============================================
            const btnsEliminar = document.querySelectorAll('.btnEliminar');
            btnsEliminar.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const fila = this.closest('tr');

                    if (confirm(`⚠️ ¿Estás seguro de desactivar al empleado "${name}"?`)) {
                        const btnOriginal = this;
                        const originalHTML = btnOriginal.innerHTML;
                        btnOriginal.disabled = true;
                        btnOriginal.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                        fetch(`/personal/eliminar/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                btnOriginal.disabled = false;
                                btnOriginal.innerHTML = originalHTML;

                                if (data.success) {
                                    if (fila) {
                                        const statusCell = fila.querySelector('td:nth-child(6)');
                                        if (statusCell) {
                                            statusCell.innerHTML = `<span class="badge bg-danger rounded-pill px-3 py-2">
                                            <i class="fas fa-times-circle me-1"></i> Inactivo
                                        </span>`;
                                        }

                                        const btnEliminarFila = fila.querySelector('.btnEliminar');
                                        const btnReactivarFila = fila.querySelector('.btnReactivar');
                                        if (btnEliminarFila) btnEliminarFila.style.display = 'none';
                                        if (btnReactivarFila) btnReactivarFila.style.display = 'inline-block';

                                        const btnEditar = fila.querySelector('.btnEditar');
                                        if (btnEditar) {
                                            btnEditar.setAttribute('data-status', 'inactive');
                                        }
                                    }
                                    showToast('success', 'Usuario desactivado exitosamente');
                                } else {
                                    showToast('error', data.message || 'Error al desactivar el usuario');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                btnOriginal.disabled = false;
                                btnOriginal.innerHTML = originalHTML;
                                showToast('error', 'Error de conexión al servidor');
                            });
                    }
                });
            });

            // ============================================
            // MANEJAR REACTIVAR
            // ============================================
            const btnsReactivar = document.querySelectorAll('.btnReactivar');
            btnsReactivar.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const fila = this.closest('tr');

                    if (confirm(`✅ ¿Estás seguro de reactivar al empleado "${name}"?`)) {
                        const btnOriginal = this;
                        const originalHTML = btnOriginal.innerHTML;
                        btnOriginal.disabled = true;
                        btnOriginal.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                        fetch(`/personal/reactivar/${id}`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                btnOriginal.disabled = false;
                                btnOriginal.innerHTML = originalHTML;

                                if (data.success) {
                                    if (fila) {
                                        const statusCell = fila.querySelector('td:nth-child(6)');
                                        if (statusCell) {
                                            statusCell.innerHTML = `<span class="badge bg-success rounded-pill px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i> Activo
                                        </span>`;
                                        }

                                        const btnEliminarFila = fila.querySelector('.btnEliminar');
                                        const btnReactivarFila = fila.querySelector('.btnReactivar');
                                        if (btnEliminarFila) btnEliminarFila.style.display = 'inline-block';
                                        if (btnReactivarFila) btnReactivarFila.style.display = 'none';

                                        const btnEditar = fila.querySelector('.btnEditar');
                                        if (btnEditar) {
                                            btnEditar.setAttribute('data-status', 'active');
                                        }
                                    }
                                    showToast('success', 'Usuario reactivado exitosamente');
                                } else {
                                    showToast('error', data.message || 'Error al reactivar el usuario');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                btnOriginal.disabled = false;
                                btnOriginal.innerHTML = originalHTML;
                                showToast('error', 'Error de conexión al servidor');
                            });
                    }
                });
            });

            // Agregar estilos de animación para los toasts
            const style = document.createElement('style');
            style.textContent = `
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
            `;
            document.head.appendChild(style);
        });
    </script>

@endsection