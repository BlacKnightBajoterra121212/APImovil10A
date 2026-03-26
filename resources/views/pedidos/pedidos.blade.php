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

        .badge-pending {
            background: #ffc107;
            color: #000;
        }

        .badge-completed {
            background: #28a745;
            color: #fff;
        }

        .badge-cancelled {
            background: #dc3545;
            color: #fff;
        }

        /* Estilos para impresión */
        @media print {

            .navbar-static-side,
            .navbar-static-top,
            .footer,
            .btn,
            .modal-header .btn-close,
            .modal-footer {
                display: none !important;
            }

            #modalVerPedido {
                position: absolute;
                left: 0;
                top: 0;
                margin: 0;
                padding: 0;
                width: 100%;
            }

            .modal-dialog {
                margin: 0;
                width: 100%;
                max-width: 100%;
            }

            .modal-content {
                border: none;
                box-shadow: none;
            }

            body {
                background: white;
            }
        }
    </style>

    <div class="row wrapper border-bottom white-bg page-heading"
        style="background: #ffffff !important; border-bottom: 1px solid #333 !important;">
        <div class="col-lg-10">
            <h2 style="color: #000000;">Gestión de Pedidos</h2>
            <ol class="breadcrumb" style="background: transparent;">
                <li class="breadcrumb-item"><a href="/dashboard" style="color: #000000;">Inicio</a></li>
                <li class="breadcrumb-item active" style="color: #000000;"><strong>Pedidos</strong></li>
            </ol>
        </div>
        <div class="col-lg-2 text-right" style="padding-top: 30px;">
            <button class="btn btn-warning" id="btnNuevoPedido" style="background: #ff7e00; border: none;">
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
                            <table class="table table-tostatech table-hover" id="tablaPedidos">
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
                                    @forelse($orders as $order)
                                        <tr data-order-id="{{ $order->id }}">
                                            <td>#{{ $order->id }}</td>
                                            <td>{{ $order->client->name ?? 'N/A' }}</td>
                                            <td>{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') : $order->created_at->format('d/m/Y') }}
                                            </td>
                                            <td>${{ number_format($order->total, 2) }}</td>
                                            <td>
                                                <span class="badge-status badge-{{ $order->status }}">
                                                    {{ $order->statusText }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-warning btnVer"
                                                    data-id="{{ $order->id }}">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-warning btnCambiarEstado"
                                                    data-id="{{ $order->id }}" data-status="{{ $order->status }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <i class="fa fa-shopping-cart fa-4x text-muted mb-3"></i>
                                                <h4>No hay pedidos registrados</h4>
                                                <p>Haz clic en "Nuevo Pedido" para comenzar</p>
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

    {{-- Modal para crear pedido --}}
    <div class="modal fade" id="modalCrearPedido" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="background: #1a1a1a; color: white; border: 1px solid #ff7e00;">
                <div class="modal-header" style="border-bottom: 1px solid #333;">
                    <h5 class="modal-title" style="color: #ffb700;">Nuevo Pedido</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCrearPedido">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sucursal</label>
                                <select class="form-control" name="id_branch" id="branch_id"
                                    style="background: #333; color: white;" required>
                                    <option value="">Seleccionar sucursal</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cliente</label>
                                <select class="form-control" name="id_client" id="client_id"
                                    style="background: #333; color: white;" required>
                                    <option value="">Seleccionar cliente</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Productos</label>
                            <div id="productos-container">
                                <div class="row mb-2 producto-item">
                                    <div class="col-md-6">
                                        <select class="form-control producto-select" name="products[0][id_product]"
                                            style="background: #333; color: white;" required>
                                            <option value="">Seleccionar producto</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                                    {{ $product->name }} - ${{ number_format($product->price, 2) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control producto-cantidad"
                                            name="products[0][quantity]" placeholder="Cantidad" min="1" value="1"
                                            style="background: #333; color: white;" required>
                                    </div>
                                    <div class="col-md-2">
                                        <span class="producto-subtotal form-control-plaintext"
                                            style="color: white;">$0.00</span>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm eliminar-producto"
                                            style="display: none;">×</button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary" id="agregar-producto">
                                <i class="fa fa-plus"></i> Agregar producto
                            </button>
                        </div>

                        <div class="text-end mt-3">
                            <h4>Total: <span id="total-pedido" style="color: #ffb700;">$0.00</span></h4>
                        </div>
                    </form>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #333;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-warning" id="btnGuardarPedido"
                        style="background: #ff7e00; border: none;">Guardar Pedido</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para cambiar estado --}}
    <div class="modal fade" id="modalCambiarEstado" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="background: #1a1a1a; color: white; border: 1px solid #ff7e00;">
                <div class="modal-header" style="border-bottom: 1px solid #333;">
                    <h5 class="modal-title" style="color: #ffb700;">Cambiar Estado del Pedido</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="order_id">
                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select class="form-control" id="order_status" style="background: #333; color: white;">
                            <option value="pending">Pendiente</option>
                            <option value="completed">Completado</option>
                            <option value="cancelled">Cancelado</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #333;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-warning" id="btnActualizarEstado"
                        style="background: #ff7e00; border: none;">Actualizar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para ver detalles del pedido --}}
    <div class="modal fade" id="modalVerPedido" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="background: #1a1a1a; color: white;">
                <div class="modal-header" style="border-bottom: 1px solid #ff7e00;">
                    <h3 class="modal-title" style="color: #ffb700;">
                        <i class="fa fa-receipt"></i> Detalles del Pedido
                    </h3>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detalles-pedido">
                    <div class="text-center py-5">
                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                        <p>Cargando detalles...</p>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #333;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times"></i> Cerrar
                    </button>
                    <button type="button" class="btn btn-warning" id="btnImprimirPDF"
                        style="background: #ff7e00; border: none;">
                        <i class="fa fa-print"></i> Imprimir PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let productIndex = 1;

            // ============================================
            // FUNCIÓN PARA RECALCULAR TOTAL
            // ============================================
            function recalcularTotal() {
                let total = 0;
                document.querySelectorAll('.producto-item').forEach(item => {
                    const select = item.querySelector('.producto-select');
                    const cantidad = item.querySelector('.producto-cantidad');
                    const subtotalSpan = item.querySelector('.producto-subtotal');

                    const selectedOption = select.options[select.selectedIndex];
                    const precio = selectedOption ? parseFloat(selectedOption.dataset.price) || 0 : 0;
                    const cantidadVal = parseInt(cantidad.value) || 0;
                    const subtotal = precio * cantidadVal;

                    if (subtotalSpan) subtotalSpan.textContent = '$' + subtotal.toFixed(2);
                    total += subtotal;
                });

                const totalSpan = document.getElementById('total-pedido');
                if (totalSpan) totalSpan.textContent = '$' + total.toFixed(2);
            }

            // ============================================
            // AGREGAR EVENTOS A PRODUCTOS
            // ============================================
            function agregarEventosProductos() {
                document.querySelectorAll('.producto-select').forEach(select => {
                    select.removeEventListener('change', recalcularTotal);
                    select.addEventListener('change', recalcularTotal);
                });
                document.querySelectorAll('.producto-cantidad').forEach(input => {
                    input.removeEventListener('input', recalcularTotal);
                    input.addEventListener('input', recalcularTotal);
                });
                document.querySelectorAll('.eliminar-producto').forEach(btn => {
                    btn.removeEventListener('click', function () { });
                    btn.addEventListener('click', function () {
                        this.closest('.producto-item').remove();
                        recalcularTotal();
                    });
                });
            }

            // ============================================
            // ABRIR MODAL CREAR PEDIDO
            // ============================================
            const btnNuevoPedido = document.getElementById('btnNuevoPedido');
            if (btnNuevoPedido) {
                btnNuevoPedido.addEventListener('click', function () {
                    document.getElementById('branch_id').value = '';
                    document.getElementById('client_id').value = '';

                    document.getElementById('productos-container').innerHTML = `
                            <div class="row mb-2 producto-item">
                                <div class="col-md-6">
                                    <select class="form-control producto-select" name="products[0][id_product]" style="background: #333; color: white;" required>
                                        <option value="">Seleccionar producto</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                                {{ $product->name }} - ${{ number_format($product->price, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control producto-cantidad" name="products[0][quantity]" placeholder="Cantidad" min="1" value="1" style="background: #333; color: white;" required>
                                </div>
                                <div class="col-md-2">
                                    <span class="producto-subtotal form-control-plaintext" style="color: white;">$0.00</span>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger btn-sm eliminar-producto" style="display: none;">×</button>
                                </div>
                            </div>
                        `;

                    document.getElementById('total-pedido').textContent = '$0.00';
                    productIndex = 1;
                    recalcularTotal();
                    agregarEventosProductos();

                    $('#modalCrearPedido').modal('show');
                });
            }

            // ============================================
            // AGREGAR PRODUCTO
            // ============================================
            const agregarProductoBtn = document.getElementById('agregar-producto');
            if (agregarProductoBtn) {
                agregarProductoBtn.addEventListener('click', function () {
                    const container = document.getElementById('productos-container');
                    const newRow = document.createElement('div');
                    newRow.className = 'row mb-2 producto-item';
                    newRow.innerHTML = `
                            <div class="col-md-6">
                                <select class="form-control producto-select" name="products[${productIndex}][id_product]" style="background: #333; color: white;" required>
                                    <option value="">Seleccionar producto</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                            {{ $product->name }} - ${{ number_format($product->price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="number" class="form-control producto-cantidad" name="products[${productIndex}][quantity]" placeholder="Cantidad" min="1" value="1" style="background: #333; color: white;" required>
                            </div>
                            <div class="col-md-2">
                                <span class="producto-subtotal form-control-plaintext" style="color: white;">$0.00</span>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-sm eliminar-producto">×</button>
                            </div>
                        `;
                    container.appendChild(newRow);
                    productIndex++;

                    newRow.querySelector('.producto-select').addEventListener('change', recalcularTotal);
                    newRow.querySelector('.producto-cantidad').addEventListener('input', recalcularTotal);
                    newRow.querySelector('.eliminar-producto').addEventListener('click', function () {
                        this.closest('.producto-item').remove();
                        recalcularTotal();
                    });

                    recalcularTotal();
                });
            }

            // ============================================
            // GUARDAR PEDIDO
            // ============================================
            const btnGuardarPedido = document.getElementById('btnGuardarPedido');
            if (btnGuardarPedido) {
                btnGuardarPedido.addEventListener('click', function () {
                    const branchId = document.getElementById('branch_id').value;
                    const clientId = document.getElementById('client_id').value;

                    if (!branchId || !clientId) return;

                    const products = [];
                    let hasProducts = false;

                    document.querySelectorAll('.producto-item').forEach((item, idx) => {
                        const productId = item.querySelector('.producto-select').value;
                        const quantity = item.querySelector('.producto-cantidad').value;
                        if (productId && quantity > 0) {
                            hasProducts = true;
                            products.push({
                                id_product: productId,
                                quantity: quantity
                            });
                        }
                    });

                    if (!hasProducts) return;

                    const formData = new FormData();
                    formData.append('id_branch', branchId);
                    formData.append('id_client', clientId);
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                    products.forEach((product, index) => {
                        formData.append(`products[${index}][id_product]`, product.id_product);
                        formData.append(`products[${index}][quantity]`, product.quantity);
                    });

                    btnGuardarPedido.disabled = true;
                    btnGuardarPedido.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Guardando...';

                    fetch("{{ route('pedidos.guardar') }}", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                $('#modalCrearPedido').modal('hide');
                                location.reload();
                            } else {
                                btnGuardarPedido.disabled = false;
                                btnGuardarPedido.innerHTML = 'Guardar Pedido';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            btnGuardarPedido.disabled = false;
                            btnGuardarPedido.innerHTML = 'Guardar Pedido';
                        });
                });
            }

            // ============================================
            // VER DETALLES DEL PEDIDO
            // ============================================
            function verDetallesPedido(id) {
                const detallesDiv = document.getElementById('detalles-pedido');

                detallesDiv.innerHTML = `
                        <div class="text-center py-5">
                            <i class="fa fa-spinner fa-spin fa-2x" style="color: #ffb700;"></i>
                            <p class="mt-3">Cargando detalles del pedido...</p>
                        </div>
                    `;

                fetch(`/pedidos/ver/${id}`)
                    .then(response => response.json())
                    .then(response => {
                        if (response.success) {
                            const order = response.data;
                            const fecha = new Date(order.order_date).toLocaleString('es-MX');

                            let estadoTexto = '';
                            switch (order.status) {
                                case 'pending': estadoTexto = 'Pendiente'; break;
                                case 'completed': estadoTexto = 'Completado'; break;
                                case 'cancelled': estadoTexto = 'Cancelado'; break;
                                default: estadoTexto = order.status;
                            }

                            let html = `
                                    <div id="pedido-para-imprimir">
                                        <div style="text-align: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #ffb700;">
                                            <h2 style="color: #ffb700; margin: 0;">TostaTech</h2>
                                            <p style="color: #888; margin: 0;">Av. Reforma #45, Col. Centro, CDMX</p>
                                            <p style="color: #888; margin: 0;">Tel: (55) 1234-5678</p>
                                        </div>

                                        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                                            <div>
                                                <p><strong style="color: #ffb700;">Pedido #:</strong> ${order.id}</p>
                                                <p><strong style="color: #ffb700;">Fecha:</strong> ${fecha}</p>
                                                <p><strong style="color: #ffb700;">Estado:</strong> <span class="badge-status badge-${order.status}">${estadoTexto}</span></p>
                                            </div>
                                            <div>
                                                <p><strong style="color: #ffb700;">Cliente:</strong> ${order.client?.name || 'N/A'}</p>
                                                <p><strong style="color: #ffb700;">Teléfono:</strong> ${order.client?.phone || 'N/A'}</p>
                                                <p><strong style="color: #ffb700;">Email:</strong> ${order.client?.email || 'N/A'}</p>
                                            </div>
                                        </div>

                                        <div style="margin-bottom: 20px;">
                                            <p><strong style="color: #ffb700;">Sucursal:</strong> ${order.branch?.name || 'N/A'}</p>
                                            <p><strong style="color: #ffb700;">Dirección:</strong> ${order.branch?.address || 'N/A'}</p>
                                            <p><strong style="color: #ffb700;">Atendido por:</strong> ${order.user?.name || 'N/A'}</p>
                                        </div>

                                        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                                            <thead>
                                                <tr style="background: #333; color: #ffb700;">
                                                    <th style="padding: 10px; text-align: left; border: 1px solid #444;">Producto</th>
                                                    <th style="padding: 10px; text-align: center; border: 1px solid #444;">Cantidad</th>
                                                    <th style="padding: 10px; text-align: right; border: 1px solid #444;">Precio Unit.</th>
                                                    <th style="padding: 10px; text-align: right; border: 1px solid #444;">Subtotal</th>
                                                  </tr>
                                            </thead>
                                            <tbody>
                                `;

                            order.details.forEach(detail => {
                                html += `
                                        <tr>
                                            <td style="padding: 8px; text-align: left; border: 1px solid #444;">${detail.product?.name || 'N/A'}</td>
                                            <td style="padding: 8px; text-align: center; border: 1px solid #444;">${detail.quantity}</td>
                                            <td style="padding: 8px; text-align: right; border: 1px solid #444;">$${parseFloat(detail.unit_price).toFixed(2)}</td>
                                            <td style="padding: 8px; text-align: right; border: 1px solid #444;">$${parseFloat(detail.subtotal).toFixed(2)}</td>
                                        </tr>
                                    `;
                            });

                            html += `
                                            </tbody>
                                            <tfoot>
                                                <tr style="background: #333;">
                                                    <td colspan="3" style="padding: 10px; text-align: right; border: 1px solid #444;">
                                                        <strong style="color: #ffb700;">TOTAL:</strong>
                                                    </td>
                                                    <td style="padding: 10px; text-align: right; border: 1px solid #444;">
                                                        <strong style="color: #ffb700; font-size: 18px;">$${parseFloat(order.total).toFixed(2)}</strong>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>

                                        <div style="margin-top: 30px; text-align: center; font-size: 12px; color: #888; border-top: 1px solid #333; padding-top: 15px;">
                                            <p>¡Gracias por tu compra! | TostaTech - Sabor que tosta el corazón</p>
                                            <p>Este documento es un comprobante de compra válido</p>
                                        </div>
                                    </div>
                                `;

                            detallesDiv.innerHTML = html;
                            $('#modalVerPedido').modal('show');
                            window.currentOrderHTML = html;
                        }
                    });
            }

            // ============================================
            // EVENTOS DE BOTONES EXISTENTES
            // ============================================
            document.querySelectorAll('.btnVer').forEach(btn => {
                btn.addEventListener('click', function () {
                    verDetallesPedido(this.getAttribute('data-id'));
                });
            });

            document.querySelectorAll('.btnCambiarEstado').forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const status = this.getAttribute('data-status');
                    document.getElementById('order_id').value = id;
                    document.getElementById('order_status').value = status;
                    $('#modalCambiarEstado').modal('show');
                });
            });

            // ============================================
            // ACTUALIZAR ESTADO
            // ============================================
            const btnActualizarEstado = document.getElementById('btnActualizarEstado');
            if (btnActualizarEstado) {
                btnActualizarEstado.addEventListener('click', function () {
                    const id = document.getElementById('order_id').value;
                    let status = document.getElementById('order_status').value;
                    status = status.toLowerCase().trim();

                    fetch(`/pedidos/actualizar/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ status: status })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                $('#modalCambiarEstado').modal('hide');
                                location.reload();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                });
            }

            // ============================================
            // IMPRIMIR PDF
            // ============================================
            const btnImprimirPDF = document.getElementById('btnImprimirPDF');
            if (btnImprimirPDF) {
                btnImprimirPDF.addEventListener('click', function () {
                    if (!window.currentOrderHTML) return;

                    const ventana = window.open('', '_blank');
                    ventana.document.write(`
                            <html>
                            <head><title>Pedido TostaTech</title></head>
                            <body style="padding: 40px; font-family: Arial;">
                                ${window.currentOrderHTML}
                                <script>window.onload = function() { window.print(); setTimeout(window.close, 500); }<\/script>
                            </body>
                            </html>
                        `);
                    ventana.document.close();
                });
            }

            // Inicializar
            agregarEventosProductos();
            recalcularTotal();
        });
    </script>
@endsection