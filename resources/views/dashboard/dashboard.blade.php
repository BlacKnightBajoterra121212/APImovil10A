@extends('layouts.layoutDashboard')

@section('titulo')
Dashboard Admin - TostaTech
@endsection

@section('contenido')

<style>
    /* VARIABLES */
    :root {
        --naranja-tosta: #ffb700;
        --negro-card: #000;
        --texto-blanco: #fff;
    }

    /* 1. ELIMINAR EL FONDO NARANJA DEL WRAPPER GLOBAL */
    /* Usamos !important para asegurar que sobreescriba el CSS del layout */
    .wrapper-content, 
    #page-wrapper, 
    .wrapper {
        background-color: #ffffff !important;
        background: #ffffff !important;
        border: none !important;
    }

    /* 2. AJUSTE DEL CONTENEDOR ESPECÍFICO */
    .container-dashboard {
        padding: 40px 5%;
        font-family: 'Poppins', sans-serif;
        background-color: #ffffff !important;
        min-height: 100vh;
        margin: 0;
        width: 100%;
    }

    /* TÍTULOS */
    .dashboard-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: #000;
        margin-bottom: 10px;
        letter-spacing: -1px;
    }

    .dashboard-subtitle {
        color: #555;
        margin-bottom: 40px;
        font-weight: 400;
    }

    /* GRID DE GRÁFICAS */
    .grid-charts {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-bottom: 50px;
    }

    .chart-card {
        background: var(--negro-card);
        padding: 25px;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1); 
        transition: .3s;
    }

    .chart-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }

    .chart-card h3 {
        color: var(--naranja-tosta);
        margin-bottom: 20px;
        font-weight: 600;
    }

    canvas {
        max-height: 220px;
    }

    /* TABLA DE PEDIDOS */
    .orders-section-title {
        font-size: 2rem;
        font-weight: 800;
        color: #000;
        margin-bottom: 25px;
    }

    .orders-table-wrapper {
        background-color: var(--negro-card);
        border-radius: 15px;
        padding: 25px;
        overflow-x: auto;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }

    .orders-table {
        width: 100%;
        border-collapse: collapse;
        color: var(--texto-blanco);
    }

    .orders-table th {
        text-align: left;
        color: var(--naranja-tosta);
        font-weight: 700;
        padding: 15px;
        border-bottom: 2px solid rgba(255,183,0, 0.3);
    }

    .orders-table td {
        padding: 18px 15px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .status-badge {
        display: inline-block;
        padding: 6px 15px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        text-align: center;
        min-width: 110px;
    }

    .status-badge.pendiente { background-color: var(--naranja-tosta); color: #000; }
    .status-badge.completado { background-color: #28a745; color: #fff; }
    .status-badge.cancelado { background-color: #dc3545; color: #fff; }

    .order-total { font-weight: 600; color: var(--naranja-tosta); }
</style>

<div class="container-dashboard">
    <h1 class="dashboard-title">Panel de Administración</h1>
    <p class="dashboard-subtitle">Bienvenido al control central de TostaTech.</p>

    <div class="grid-charts">
        <div class="chart-card">
            <h3>Estado de Pedidos</h3>
            <canvas id="pedidosChart"></canvas>
        </div>

        <div class="chart-card">
            <h3>Personal</h3>
            <canvas id="personalChart"></canvas>
        </div>

        <div class="chart-card">
            <h3>Inventario Crítico</h3>
            <canvas id="inventarioChart"></canvas>
        </div>
    </div>

    <h2 class="orders-section-title">Gestión de Pedidos</h2>
    <div class="orders-table-wrapper">
        <table class="orders-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Producto</th>
                    <th>Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>001</td>
                    <td>Juan Pérez</td>
                    <td>30 Tostadas</td>
                    <td class="order-total">$240</td>
                    <td><span class="status-badge pendiente">Pendiente</span></td>
                </tr>
                <tr>
                    <td>002</td>
                    <td>Ana López</td>
                    <td>15 Tacos Dorados</td>
                    <td class="order-total">$180</td>
                    <td><span class="status-badge completado">Completado</span></td>
                </tr>
                <tr>
                    <td>005</td>
                    <td>Pedro Sánchez</td>
                    <td>20 Chicharrones</td>
                    <td class="order-total">$300</td>
                    <td><span class="status-badge cancelado">Cancelado</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { labels: { color: '#fff', font: { family: 'Poppins' } } }
        }
    };

    // 📊 Gráficas
    new Chart(document.getElementById('pedidosChart'), {
        type: 'bar',
        data: {
            labels: ['Pendientes', 'Completados', 'Cancelados'],
            datasets: [{
                data: [3, 3, 1],
                backgroundColor: ['#ffb700', '#28a745', '#dc3545']
            }]
        },
        options: {
            ...commonOptions,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: '#fff' } },
                y: { ticks: { color: '#fff' }, grid: { color: 'rgba(255,255,255,0.1)' } }
            }
        }
    });

    new Chart(document.getElementById('personalChart'), {
        type: 'pie',
        data: {
            labels: ['Cocina', 'Caja', 'Repartidor', 'Auxiliar'],
            datasets: [{
                data: [2, 1, 1, 1],
                backgroundColor: ['#ffb700', '#ff7b00', '#ffa726', '#ffcc80']
            }]
        },
        options: commonOptions
    });

    new Chart(document.getElementById('inventarioChart'), {
        type: 'doughnut',
        data: {
            labels: ['Tostada', 'Taco Dorado', 'Botana', 'Otros'],
            datasets: [{
                data: [850, 320, 150, 120],
                backgroundColor: ['#ffb700', '#ff7b00', '#ffa726', '#8d6e63']
            }]
        },
        options: commonOptions
    });
</script>

@endsection