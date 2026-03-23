@extends('layouts.layoutDashboard')

@section('titulo')
Dashboard Admin - TostaTech
@endsection

<style>
    .chart-card {
        background: #1a1a1a; /* Un negro ligeramente más suave para resaltar sobre fondos */
        padding: 25px;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .4);
        transition: .3s;
        height: 100%; /* Para que todas midan lo mismo en la fila */
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .chart-card:hover {
        transform: translateY(-5px);
    }

    .chart-card h3 {
        color: #ffb700;
        margin-bottom: 20px;
        font-weight: 800;
        font-size: 1.2rem;
        text-align: center;
    }

    /* Contenedor para limitar la altura de las gráficas */
    .canvas-container {
        position: relative;
        height: 250px; 
        width: 100%;
    }
</style>

@section('contenido')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="font-weight: 800; color: #333;">Dashboard General</h2>
    </div>

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="chart-card">
                <h3>Estado de Pedidos</h3>
                <div class="canvas-container">
                    <canvas id="pedidosChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="chart-card">
                <h3>Distribución de Personal</h3>
                <div class="canvas-container">
                    <canvas id="personalChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-12 mb-4">
            <div class="chart-card">
                <h3>Inventario Actual</h3>
                <div class="canvas-container">
                    <canvas id="inventarioChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Configuración global para que los textos sean blancos en el fondo negro
    Chart.defaults.color = '#fff';
    Chart.defaults.font.family = "'Poppins', sans-serif";

    // 📊 PEDIDOS (Barras)
    new Chart(document.getElementById('pedidosChart'), {
        type: 'bar',
        data: {
            labels: ['Pendientes', 'Completados', 'Cancelados'],
            datasets: [{
                data: [3, 3, 1],
                backgroundColor: ['#ffb700', '#00c853', '#d50000']
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.1)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // 👥 PERSONAL (Pie)
    new Chart(document.getElementById('personalChart'), {
        type: 'pie',
        data: {
            labels: ['Cocina', 'Caja', 'Repartidor', 'Auxiliar'],
            datasets: [{
                data: [2, 1, 1, 1],
                backgroundColor: ['#ffb700', '#ff7b00', '#ff9800', '#ffc107']
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // 📦 INVENTARIO (Doughnut)
    new Chart(document.getElementById('inventarioChart'), {
        type: 'doughnut',
        data: {
            labels: ['Tostada', 'Taco Dorado', 'Botana', 'Papa', 'Chicharrón', 'Chile', 'Churro'],
            datasets: [{
                data: [850, 320, 150, 40, 12, 60, 8],
                backgroundColor: ['#ffb700', '#ff7b00', '#ffa726', '#ffcc80', '#d84315', '#8d6e63', '#ffeb3b']
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10 } }
            }
        }
    });
</script>
@endsection