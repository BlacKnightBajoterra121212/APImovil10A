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

    {{-- Componente de Tabla de Personal Reutilizable --}}
    <div class="clients-list">
        <ul class="nav nav-tabs" style="justify-content: flex-end">
            {{-- Botón crear usuario --}}
            <li class="me-4">
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalCrearUsuario">
                    <i class="fa fa-user"></i>
                </button>
            </li>

            {{-- Botón buscar usuario --}}
            <li class="me-4">
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalBuscarUsuario">
                    <i class="fa fa-search"></i>
                </button>
            </li>
        </ul>

        {{-- Área para mensajes de búsqueda (estructura visual) --}}
        <div class="mt-3 d-flex justify-content-between align-items-center" style="display: none;">
            <div class="alert alert-info mb-0">
                Resultados de búsqueda para:
                <strong></strong>
            </div>
            <a href="#" class="btn btn-warning">
                Mostrar todo
            </a>
        </div>

        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox">
                        <div class="ibox-content">
                            {{-- Área para mensaje cuando no hay datos --}}
                            <div class="empty-message" style="display: none;">
                                <p>
                                <h2>No hay empleados disponibles.</h2>
                                </p>
                            </div>

                            {{-- Tabla de datos --}}
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover dataTables-example"
                                    id="tablaPersonal">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Rol</th>
                                            <th>Telefono</th>
                                            <th>Estado</th>
                                            <th>Editar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Filas de datos irán aquí dinámicamente --}}
                                    </tbody>
                                </table>
                            </div>

                            {{-- Paginación --}}
                            <div class="row mt-3">
                                <div class="d-flex justify-content-center mt-3">
                                    {{-- Paginación irá aquí --}}
                                </div>
                            </div>
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
                                    <input type="text" class="form-control" name="nomreg" placeholder="Nombre" required>
                                </div>

                                <div class="input-group mb-3">
                                    <span class="input-group-text text-danger"><i class="fa fa-user"></i></span>
                                    <input type="text" class="form-control" name="nompat" placeholder="Apellido Paterno"
                                        required>
                                </div>

                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fa fa-user"></i></span>
                                    <input type="text" class="form-control" name="nommat" placeholder="Apellido Materno">
                                </div>
                            </div>

                            {{-- Columna derecha --}}
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-phone-square"></i></span>
                                    <select class="form-control" name="pais" style="height: 40px;">
                                        <option selected disabled class="text-muted">País</option>
                                        <option value="Mexico">México - 52</option>
                                        <option value="USA">USA - 1</option>
                                    </select>
                                </div>

                                <div class="input-group mb-3">
                                    <span class="input-group-text text-danger"><i class="fab fa-whatsapp"></i></span>
                                    <input type="tel" class="form-control no-spinner" id="numcel" name="numcel"
                                        placeholder="Teléfono" maxlength="10" inputmode="numeric" required>
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

                            {{-- Email --}}
                            <div class="col-md-12 mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                                </div>
                            </div>

                            {{-- Contraseña por defecto --}}
                            <input type="hidden" name="nompas" value="12345">
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

                <form action="#" method="GET">
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="formEditarUsuario" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="usuario_id" name="id">

                    <div class="modal-body">
                        {{-- Pestañas --}}
                        <ul class="nav nav-tabs" id="editarTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="perfil-tab" data-bs-toggle="tab" href="#perfilEdit"
                                    role="tab">
                                    <i class="fa fa-user"></i> Perfil
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pagos-tab" data-bs-toggle="tab" href="#pagosEdit" role="tab">
                                    <i class="fa fa-credit-card"></i> Pagos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="fiscal-tab" data-bs-toggle="tab" href="#fiscalEdit" role="tab">
                                    <i class="fa fa-file-invoice"></i> Fiscal
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content mt-3">
                            {{-- TAB PERFIL --}}
                            <div class="tab-pane fade show active" id="perfilEdit">
                                <div class="row g-3">
                                    <div class="col-md-12 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text text-danger"><i class="fa fa-user"></i></span>
                                            <input type="text" class="form-control" id="edit_nomreg" name="nomreg"
                                                placeholder="Nombre">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text text-danger"><i class="fa fa-user"></i></span>
                                            <input type="text" class="form-control" id="edit_nompat" name="nompat"
                                                placeholder="Apellido Paterno">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                                            <input type="text" class="form-control" id="edit_nommat" name="nommat"
                                                placeholder="Apellido Materno">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text text-danger"><i
                                                    class="fab fa-whatsapp"></i></span>
                                            <input type="tel" class="form-control no-spinner" id="edit_numcel" name="numcel"
                                                placeholder="Teléfono" maxlength="10" inputmode="numeric">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text text-danger"><i class="fa fa-envelope"></i></span>
                                            <input type="email" class="form-control" id="edit_email" name="email"
                                                placeholder="Email">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small">
                                            <h4>F. Nacimiento</h4>
                                        </label>
                                        <input type="date" class="form-control" id="edit_fnac" name="fnac">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label small">
                                            <h4>F. Alta</h4>
                                        </label>
                                        <input type="date" class="form-control" id="edit_falta" name="falta">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa fa-user-check"></i></span>
                                            <select id="edit_estatus" name="estatus" class="form-control">
                                                <option value="1">Activo</option>
                                                <option value="0">Inactivo</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text py-2">
                                                <i class="fas fa-user-tag fa-sm"></i>
                                            </span>
                                            <select class="form-control form-control-sm" name="id_per" id="edit_id_per">
                                                <option value="">Selecciona Rol</option>
                                                {{-- Opciones de roles irán aquí --}}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB PAGOS --}}
                            <div class="tab-pane fade" id="pagosEdit" role="tabpanel">
                                <div class="row mt-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Comisión</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-money-bill"></i></span>
                                            <select class="form-control" id="edit_comisiona" name="comisiona">
                                                <option value="0">No Comisiona</option>
                                                <option value="1">Comisiona</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Datos Bancarios</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-university"></i></span>
                                            <select class="form-control" id="edit_banco" name="banco">
                                                <option value="">Seleccione un Banco</option>
                                                <option value="1">BBVA</option>
                                                <option value="2">Banorte</option>
                                                <option value="3">Santander</option>
                                                <option value="4">HSBC</option>
                                                <option value="5">Banamex</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Titular de la Cuenta</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-bookmark"></i></span>
                                            <input type="text" class="form-control" id="edit_titular" name="titular"
                                                placeholder="Titular">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">No. Cuenta</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-bookmark"></i></span>
                                            <input type="text" class="form-control" id="edit_cuenta" name="cuenta"
                                                placeholder="Ej: 0123456789" maxlength="10">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">CLABE Interbancaria</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="far fa-credit-card"></i></span>
                                            <input type="text" class="form-control" id="edit_clabe" name="clabe"
                                                placeholder="Ej: 012345678901234567" maxlength="18">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- TAB FISCAL --}}
                            <div class="tab-pane fade" id="fiscalEdit" role="tabpanel">
                                <div class="row mt-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">RFC</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                                            <input type="text" id="edit_rfc" name="rfc" class="form-control"
                                                placeholder="RFC" maxlength="13" minlength="12">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Régimen Fiscal</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-file-invoice-dollar"></i></span>
                                            <select id="edit_regimen" name="regimen" class="form-control">
                                                <option value="">Seleccione una opción</option>
                                                <option value="601">601 - General de Ley Personas Morales</option>
                                                <option value="603">603 - Personas Morales con Fines no Lucrativos</option>
                                                <option value="605">605 - Sueldos y Salarios e Ingresos Asimilados a
                                                    Salarios</option>
                                                <option value="606">606 - Arrendamiento</option>
                                                <option value="607">607 - Régimen de Enajenación o Adquisición de Bienes
                                                </option>
                                                <option value="608">608 - Demás Ingresos</option>
                                                <option value="609">609 - Consolidación</option>
                                                <option value="610">610 - Residentes en el Extranjero sin EP en México
                                                </option>
                                                <option value="611">611 - Ingresos por Dividendos (socios y accionistas)
                                                </option>
                                                <option value="612">612 - Personas Físicas con Actividades Empresariales y
                                                    Profesionales</option>
                                                <option value="614">614 - Ingresos por intereses</option>
                                                <option value="615">615 - Régimen de los Ingresos por Obtención de Premios
                                                </option>
                                                <option value="616">616 - Sin obligaciones fiscales</option>
                                                <option value="620">620 - Sociedades Cooperativas de Producción</option>
                                                <option value="621">621 - Incorporación Fiscal</option>
                                                <option value="622">622 - Actividades Agrícolas, Ganaderas, Silvícolas y
                                                    Pesqueras</option>
                                                <option value="623">623 - Opcional para Grupos de Sociedades</option>
                                                <option value="624">624 - Coordinados</option>
                                                <option value="625">625 - Régimen de las Actividades Empresariales con
                                                    ingresos a través de Plataformas Digitales</option>
                                                <option value="626">626 - Régimen Simplificado de Confianza (RESICO)
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Razón Social</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                                            <input type="text" id="edit_razon" name="razon" class="form-control"
                                                placeholder="Razón Social">
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Domicilio Fiscal</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                            <input type="text" id="edit_domicilio" name="domicilio" class="form-control"
                                                placeholder="Domicilio Fiscal">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Código Postal</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-location-arrow"></i></span>
                                            <input type="number" id="edit_cp" name="cp" class="form-control no-spinners"
                                                placeholder="C.P." min="10000" max="99999">
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Estado</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-map"></i></span>
                                            <select id="edit_estado" name="estado" class="form-control">
                                                <option value="">Seleccione un estado</option>
                                                <option>Aguascalientes</option>
                                                <option>Baja California</option>
                                                <option>Baja California Sur</option>
                                                <option>Campeche</option>
                                                <option>Chiapas</option>
                                                <option>Chihuahua</option>
                                                <option>CDMX</option>
                                                <option>Coahuila</option>
                                                <option>Colima</option>
                                                <option>Durango</option>
                                                <option>Guanajuato</option>
                                                <option>Guerrero</option>
                                                <option>Hidalgo</option>
                                                <option>Jalisco</option>
                                                <option>Michoacán</option>
                                                <option>Morelos</option>
                                                <option>Nayarit</option>
                                                <option>Nuevo León</option>
                                                <option>Oaxaca</option>
                                                <option>Puebla</option>
                                                <option>Querétaro</option>
                                                <option>Quintana Roo</option>
                                                <option>San Luis Potosí</option>
                                                <option>Sinaloa</option>
                                                <option>Sonora</option>
                                                <option>Tabasco</option>
                                                <option>Tamaulipas</option>
                                                <option>Tlaxcala</option>
                                                <option>Veracruz</option>
                                                <option>Yucatán</option>
                                                <option>Zacatecas</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            <input type="email" id="edit_email_fiscal" name="email_fiscal"
                                                class="form-control" placeholder="Correo Fiscal">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" id="btnGuardarPerfil" class="btn btn-primary">Guardar Cambios</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .nav.nav-tabs>li {
            margin-right: 12px !important;
        }
    </style>

    <script>
        // Script para prevenir caracteres no numéricos en teléfono
        document.getElementById('numcel')?.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>

@endsection