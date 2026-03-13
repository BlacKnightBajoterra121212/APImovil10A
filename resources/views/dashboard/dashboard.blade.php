<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <style>
        /* Altura correcta para que flot renderice */

        .chart-box {
            height: 300px;
            width: 100%;
        }

        /* móvil */

        @media (max-width:768px) {

            .chart-box {
                height: 260px;
            }

        }
    </style>

</head>


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


<!-- Scripts -->

<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.js"></script>

<script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Flot -->
<script src="js/plugins/flot/jquery.flot.js"></script>
<script src="js/plugins/flot/jquery.flot.tooltip.min.js"></script>
<script src="js/plugins/flot/jquery.flot.resize.js"></script>
<script src="js/plugins/flot/jquery.flot.pie.js"></script>
<script src="js/plugins/flot/jquery.flot.time.js"></script>

<!-- Morris -->
<script src="js/plugins/morris/raphael-2.1.0.min.js"></script>
<script src="js/plugins/morris/morris.js"></script>

<!-- d3 -->
<script src="js/plugins/d3/d3.min.js"></script>

<!-- c3 -->
<script src="js/plugins/c3/c3.min.js"></script>

<!-- Inspinia -->
<script src="js/inspinia.js"></script>
<script src="js/plugins/pace/pace.min.js"></script>

<!-- Demo -->
<script src="js/demo/flot-demo.js"></script>

<script>

    $(document).ready(function () {

        /* Radar 1 */

        c3.generate({

            bindto: '#gauge',

            data: {
                columns: [
                    ['data', 91.4]
                ],
                type: 'gauge'
            },

            color: {
                pattern: ['#1ab394', '#BABABA']
            }

        });


        /* Radar 2 */

        c3.generate({

            bindto: '#pie',

            data: {
                columns: [
                    ['data1', 30],
                    ['data2', 120]
                ],
                colors: {
                    data1: '#1ab394',
                    data2: '#BABABA'
                },
                type: 'pie'
            }

        });

    });

</script>

</html>