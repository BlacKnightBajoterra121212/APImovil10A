@extends('layouts.layoutDashboard')

@section('titulo')
Dashboard Admin
@endsection

@section('contenido')

<body>

    <div class="container-fluid p-4">

        <div class="row">


            <!-- PIE -->
            <div class="col-12 col-md-6 mb-4">

                <div class="ibox">

                    <div class="ibox-title">
                        <h5>Pie Chart</h5>
                    </div>

                    <div class="ibox-content">

                        <div id="flot-pie-chart" class="chart-box"></div>

                    </div>

                </div>
            </div>



            <!-- BAR -->
            <div class="col-12 col-md-6 mb-4">

                <div class="ibox">

                    <div class="ibox-title">
                        <h5>Bar Chart</h5>
                    </div>

                    <div class="ibox-content">

                        <div id="flot-bar-chart" class="chart-box"></div>

                    </div>

                </div>
            </div>



            <!-- RADAR 1 -->
            <div class="col-12 col-md-6 mb-4">

                <div class="ibox">

                    <div class="ibox-title">
                        <h5>Radar Chart</h5>
                    </div>

                    <div class="ibox-content">

                        <div id="gauge" class="chart-box"></div>

                    </div>

                </div>
            </div>



            <!-- RADAR 2 -->
            <div class="col-12 col-md-6 mb-4">

                <div class="ibox">

                    <div class="ibox-title">
                        <h5>Radar Chart</h5>
                    </div>

                    <div class="ibox-content">

                        <div id="pie" class="chart-box"></div>

                    </div>

                </div>
            </div>



        </div>
    </div>

</body>

@endsection