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
        <ul class="nav nav-tabs" style="justify-content: flex-end">
            {{-- Botón crear usuario --}}
            <li class="me-4">
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario">
                    <i class="fa fa-user"></i> Nuevo
                </button>
            </li>

            {{-- Botón buscar usuario --}}
            <li class="me-4">
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalBuscarUsuario">
                    <i class="fa fa-search"></i> Buscar
                </button>
            </li>
        </ul>

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
                                    <p class="text-muted">Haz clic en el botón <i class="fa fa-user"></i> para agregar un nuevo empleado</p>
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
                                            <tr>
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
                                                    <button type="button" 
                                                            class="btn btn-primary btn-sm btnEditar"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalEditarUsuario"
                                                            data-id="{{ $data->id }}"
                                                            data-name="{{ $data->name }}"
                                                            data-email="{{ $data->email }}"
                                                            data-id_role="{{ $data->id_role }}"
                                                            data-phone="{{ $data->phone }}"
                                                            data-status="{{ $data->status }}">
                                                        <i class="fas fa-edit"></i> Editar
                                                    </button>
                                                    
                                                    @if($data->status == 'active')
                                                        <button type="button" 
                                                                class="btn btn-danger btn-sm btnEliminar"
                                                                data-id="{{ $data->id }}"
                                                                data-name="{{ $data->name }}">
                                                            <i class="fas fa-trash-alt"></i> Desactivar
                                                        </button>
                                                    @else
                                                        <button type="button" 
                                                                class="btn btn-success btn-sm btnReactivar"
                                                                data-id="{{ $data->id }}"
                                                                data-name="{{ $data->name }}">
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
                                        <p class="text-muted">
                                            Mostrando {{ $personal->firstItem() }} a {{ $personal->lastItem() }} de {{ $personal->total() }} empleados
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
                                <div class="input-group mb-3">
                                    <span class="input-group-text text-danger"><i class="fa fa-user"></i></span>
                                    <input type="text" class="form-control" name="name" placeholder="Nombre completo" required>
                                </div>

                                <div class="input-group mb-3">
                                    <span class="input-group-text text-danger"><i class="fa fa-envelope"></i></span>
                                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                                </div>
                            </div>

                            {{-- Columna derecha --}}
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text text-danger"><i class="fab fa-whatsapp"></i></span>
                                    <input type="tel" class="form-control no-spinner" id="numcel" name="phone"
                                        placeholder="Teléfono" maxlength="10" inputmode="numeric">
                                </div>

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
                            </div>

                            {{-- Contraseña por defecto --}}
                            <input type="hidden" name="password" value="12345">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Aceptar</button>
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
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Nombre completo</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="edit_phone" name="phone" maxlength="10">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rol</label>
                                <select class="form-control" name="id_role" id="edit_id_role" required>
                                    <option value="">Selecciona un Rol</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}">{{ $rol->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado</label>
                                <select id="edit_status" name="status" class="form-control">
                                    <option value="active">Activo</option>
                                    <option value="inactive">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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
        // Script para prevenir caracteres no numéricos en teléfono
        document.getElementById('numcel')?.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Manejar el modal de edición
        document.addEventListener('DOMContentLoaded', function() {
            // Botón Editar
            const btnsEditar = document.querySelectorAll('.btnEditar');
            btnsEditar.forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const email = this.getAttribute('data-email');
                    const id_role = this.getAttribute('data-id_role');
                    const phone = this.getAttribute('data-phone');
                    const status = this.getAttribute('data-status');
                    
                    document.getElementById('edit_id').value = id;
                    document.getElementById('edit_name').value = name;
                    document.getElementById('edit_email').value = email;
                    document.getElementById('edit_id_role').value = id_role;
                    document.getElementById('edit_phone').value = phone;
                    document.getElementById('edit_status').value = status;
                    
                    const form = document.getElementById('formEditarUsuario');
                    form.action = `/personal/actualizar/${id}`;
                });
            });
            
            // Botón Eliminar (Desactivar)
            const btnsEliminar = document.querySelectorAll('.btnEliminar');
            btnsEliminar.forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    
                    if(confirm(`¿Estás seguro de desactivar al empleado "${name}"?`)) {
                        fetch(`/personal/eliminar/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json'
                            }
                        }).then(response => {
                            if(response.ok) {
                                location.reload();
                            } else {
                                alert('Error al desactivar el usuario');
                            }
                        });
                    }
                });
            });
            
            // Botón Reactivar
            const btnsReactivar = document.querySelectorAll('.btnReactivar');
            btnsReactivar.forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    
                    if(confirm(`¿Estás seguro de reactivar al empleado "${name}"?`)) {
                        fetch(`/personal/reactivar/${id}`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json'
                            }
                        }).then(response => {
                            if(response.ok) {
                                location.reload();
                            } else {
                                alert('Error al reactivar el usuario');
                            }
                        });
                    }
                });
            });
        });
    </script>

@endsection