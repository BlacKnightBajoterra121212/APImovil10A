@extends('layouts.layoutDashboard') {{-- O el nombre de tu layout principal --}}

@section('contenido')
<div class="container-fluid p-4">
    {{-- Encabezado de la Tienda --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h1 style="color: #ffb700; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">
                <i class="fa fa-store"></i> Nuestra Tienda
            </h1>
            <p class="text-muted">Selecciona los productos que deseas ordenar.</p>
        </div>
        <div class="col-md-4 text-right">
            <button class="btn-cart-floating">
                <i class="fa fa-shopping-cart"></i> 
                <span class="badge-count">0</span>
                Ver mi orden
            </button>
        </div>
    </div>

    {{-- Grid de Productos --}}
<div class="container-fluid">
    <div class="row">
        
        {{-- Producto 1: Tostada --}}
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="client-product-card shadow-lg">
                <div class="img-container">
                    <img src="data:image/jpeg;base64,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" alt="Producto TostaTech" class="product-img">
                    <span class="price-tag">$25.00</span>
                </div>
                <div class="card-body text-center">
                    <h4 class="product-title">Tostada Clásica</h4>
                    <p class="category-text">Categoría: Tostadas</p>
                    <div class="stock-info mb-3">
                        <small>Disponible: <strong>400</strong></small>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <input type="number" class="form-control qty-input" value="1" min="1">
                        <button class="btn btn-add"><i class="fa fa-plus"></i> Agregar</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Producto 2: Papa Dorada --}}
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="client-product-card shadow-lg">
                <div class="img-container">
                    <img src="data:image/jpeg;base64,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" alt="Papa Dorada" class="product-img">
                    <span class="price-tag">$12.50</span>
                </div>
                <div class="card-body text-center">
                    <h4 class="product-title">Papa Dorada</h4>
                    <p class="category-text">Categoría: Complementos</p>
                    <div class="stock-info mb-3">
                        <small>Disponible: <strong>120</strong></small>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <input type="number" class="form-control qty-input" value="1" min="1">
                        <button class="btn btn-add"><i class="fa fa-plus"></i> Agregar</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Producto 3: Taco Dorado --}}
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="client-product-card shadow-lg">
                <div class="img-container">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRHcZ0ipUP9mifdUTL4YPR1OvRqDWEV5kStsw&s" alt="Taco Dorado">
                    <span class="price-tag">$3.00</span>
                </div>
                <div class="card-body text-center">
                    <h4 class="product-title">Taco Dorado</h4>
                    <p class="category-text">Categoría: Tacos</p>
                    <div class="stock-info mb-3">
                        <small>Stock: <strong>39</strong></small>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <input type="number" class="form-control qty-input" value="1" min="1" max="39">
                        <button class="btn btn-add"><i class="fa fa-plus"></i> Agregar</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Producto 4: Mix de Botanas --}}
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="client-product-card shadow-lg">
                <div class="img-container">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTzbN14PmHz-pXlf5xRtp5TBzEVVUKAS5HF1STRuOEAjQ&s" alt="Mix de Botanas">
                    <span class="price-tag">$15.00</span>
                </div>
                <div class="card-body text-center">
                    <h4 class="product-title">Mix de Botanas</h4>
                    <p class="category-text">Categoría: Snacks</p>
                    <div class="stock-info mb-3">
                        <small>Stock: <strong>58</strong></small>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <input type="number" class="form-control qty-input" value="1" min="1" max="58">
                        <button class="btn btn-add"><i class="fa fa-plus"></i> Agregar</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Producto 5: Churros de Maíz --}}
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="client-product-card shadow-lg">
                <div class="img-container">
                    <img src="data:image/jpeg;base64,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" alt="Producto TostaTech" class="product-img">
                    <span class="price-tag">$10.00</span>
                </div>
                <div class="card-body text-center">
                    <h4 class="product-title">Churros de Maíz</h4>
                    <p class="category-text">Categoría: Postres</p>
                    <div class="stock-info mb-3">
                        <small>Stock: <strong>40</strong></small>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <input type="number" class="form-control qty-input" value="1" min="1" max="40">
                        <button class="btn btn-add"><i class="fa fa-plus"></i> Agregar</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Producto 6: Chicharrón Cuadrado --}}
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="client-product-card shadow-lg">
                <div class="img-container">
                    <img src="https://http2.mlstatic.com/D_909510-MLM70421160260_072023-C.jpg" alt="Chicharrón">
                    <span class="price-tag">$3.00</span>
                </div>
                <div class="card-body text-center">
                    <h4 class="product-title">Chicharrón Cuadrado</h4>
                    <p class="category-text">Categoría: Snacks</p>
                    <div class="stock-info mb-3">
                        <small>Stock: <strong>85</strong></small>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <input type="number" class="form-control qty-input" value="1" min="1" max="85">
                        <button class="btn btn-add"><i class="fa fa-plus"></i> Agregar</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Producto 7: Chile Especial --}}
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="client-product-card shadow-lg">
                <div class="img-container">
                    <img src="data:image/jpeg;base64,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" alt="Chile Especial" class="product-img">
                    <span class="price-tag">$20.00</span>
                </div>
                <div class="card-body text-center">
                    <h4 class="product-title">Chile Especial</h4>
                    <p class="category-text">Categoría: Salsas</p>
                    <div class="stock-info mb-3">
                        <small style="color: #fb3a3a;">⚠️ Bajo Stock: <strong>18</strong></small>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <input type="number" class="form-control qty-input" value="1" min="1" max="18">
                        <button class="btn btn-add"><i class="fa fa-plus"></i> Agregar</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Producto 8: Chile Martajada --}}
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="client-product-card shadow-lg">
                <div class="img-container">
                    <img src="https://http2.mlstatic.com/D_NQ_NP_642298-MLM49761883350_042022-O.webp" alt="Chile Martajado">
                    <span class="price-tag">$20.00</span>
                </div>
                <div class="card-body text-center">
                    <h4 class="product-title">Chile Martajada</h4>
                    <p class="category-text">Categoría: Salsas</p>
                    <div class="stock-info mb-3">
                        <small>Stock: <strong>24</strong></small>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <input type="number" class="form-control qty-input" value="1" min="1" max="24">
                        <button class="btn btn-add"><i class="fa fa-plus"></i> Agregar</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Producto 9: Chicharrón de Cerdo --}}
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="client-product-card shadow-lg">
                <div class="img-container">
                    <img src="https://clickabasto.com/cdn/shop/products/istockphoto-185217418-612x612_612x407.jpg?v=1655778504" alt="Chicharrón de Cerdo">
                    <span class="price-tag">$27.00</span>
                </div>
                <div class="card-body text-center">
                    <h4 class="product-title">Chicharrón de Cerdo</h4>
                    <p class="category-text">Categoría: Carnes</p>
                    <div class="stock-info mb-3">
                        <small>Stock: <strong>15</strong></small>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <input type="number" class="form-control qty-input" value="1" min="1" max="15">
                        <button class="btn btn-add"><i class="fa fa-plus"></i> Agregar</button>
                    </div>
                </div>
            </div>
        </div>

    </div> {{-- Cierre de Row --}}
</div> {{-- Cierre de Container --}}
</div>

<style>
    /* Estilos personalizados para la vista de Cliente */
    .client-product-card {
        background: #1e1e1e;
        border-radius: 15px;
        overflow: hidden;
        border: 1px solid #333;
        transition: all 0.3s ease;
    }

    .client-product-card:hover {
        transform: translateY(-8px);
        border-color: #ffb700;
        box-shadow: 0 10px 20px rgba(0,0,0,0.5);
    }

    .img-container {
        position: relative;
        height: 180px;
        background: #252525;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .img-container img {
        max-height: 80%;
        max-width: 80%;
        object-fit: contain;
    }

    .price-tag {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #ffb700;
        color: #000;
        padding: 5px 12px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 0.9rem;
    }

    .product-title {
        color: #fff;
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .category-text {
        color: #888;
        font-size: 0.85rem;
    }

    .stock-info {
        color: #00c292;
        background: rgba(0, 194, 146, 0.1);
        padding: 2px 8px;
        border-radius: 5px;
        display: inline-block;
    }

    .qty-input {
        background: #2a2a2a !important;
        border: 1px solid #444 !important;
        color: #fff !important;
        width: 70px;
        text-align: center;
    }

    .btn-add {
        background: #ff7e00;
        color: #fff;
        font-weight: 700;
        flex-grow: 1;
        border-radius: 8px;
        border: none;
        padding: 8px;
        transition: 0.3s;
    }

    .btn-add:hover {
        background: #ffb700;
        color: #000;
    }

    .btn-cart-floating {
        background: #1e1e1e;
        color: #ffb700;
        border: 2px solid #ffb700;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 700;
        position: relative;
    }

    .badge-count {
        background: #ff3e3e;
        color: white;
        padding: 2px 7px;
        border-radius: 50%;
        font-size: 0.7rem;
        position: absolute;
        top: -10px;
        right: -10px;
    }
</style>
@endsection