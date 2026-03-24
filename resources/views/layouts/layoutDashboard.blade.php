<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('titulo')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- VITE --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/animate.css') }}">

    <style>
        /* FONDO GENERAL Y TEXTO */
        body {
            background-color: #000000;
            color: #ffffff;
            font-family: 'Poppins', sans-serif;
        }

        #page-wrapper {
            background-color: #ececec !important; /* Gris casi negro para el contenido */
            min-height: 100vh;
        }

        /* SIDEBAR (Negro con acento Naranja) */
        .navbar-static-side {
            background-color: #000000 !important;
            border-right: 1px solid #222;
            width: 220px;
        }

        .sidebar-collapse {
            background-color: #000000 !important;
        }

        #side-menu li a {
            color: #d1d1d1 !important;
            font-weight: 500;
        }

        #side-menu > li > a:hover, 
        #side-menu > li.active > a {
            background-color: #ff7e00 !important; /* Naranja TostaTech */
            color: white !important;
        }

        .nav-header {
            background-color: #000000 !important;
            border-bottom: 1px solid #222;
        }

        /* NAVBAR SUPERIOR */
        .navbar-static-top {
            background-color: #000000 !important;
            border-bottom: 1px solid #222 !important;
        }

        .navbar-minimalize {
            background-color: #ff7e00 !important;
            color: white !important;
            border: none;
        }

        .navbar-top-links li a {
            color: #ff7e00 !important;
        }

        /* ELEMENTOS COMUNES (FOOTER, PAGINACIÓN) */
        .footer {
            background: #000000 !important;
            border-top: 1px solid #222 !important;
            color: #888;
        }

        .pagination .page-link {
            background-color: #1a1a1a;
            border-color: #333;
            color: #ff7e00;
        }

        .pagination .page-item.active .page-link {
            background-color: #ff7e00;
            border-color: #ff7e00;
            color: white;
        }

        /* DASHBOARD BOXES (ibox) */
        .ibox-title {
            background-color: #1a1a1a !important;
            border-color: #333 !important;
            color: #ffb700 !important;
        }

        .ibox-content {
            background-color: #1a1a1a !important;
            border-color: #333 !important;
            color: #ffffff !important;
        }

        .chart-box {
            height: 300px;
            width: 100%;
        }

        @media (max-width:768px) {
            .chart-box { height: 260px; }
        }
    </style>
</head>

<body>
    <div id="wrapper">

        {{-- SIDEBAR --}}
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element text-center">
                            <img class="rounded-circle" width="50" src="{{ asset('img/user.png') }}" style="border: 2px solid #ff7e00;">
                            <span class="block m-t-xs font-bold" style="color: #ffb700; margin-top: 10px;">
                                {{ Auth::user()->name ?? 'Admin TostaTech' }}
                            </span>
                        </div>
                        <div class="logo-element" style="color: #ff7e00;">TT</div>
                    </li>

                    <li>
                        <a href="{{ url('/dashboard') }}">
                            <i class="fa fa-home" style="color: #ff7e00;"></i>
                            <span class="nav-label">Inicio</span>
                        </a>
                    </li>

                    <li>
                        <a href="javascript:void(0)">
                            <i class="fa fa-users" style="color: #ff7e00;"></i>
                            <span class="nav-label">Directorio</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level collapse" style="background: #0a0a0a;">
                            <li><a href="{{ url('/personal') }}">Lista de usuarios</a></li>
                            <li><a href="{{ url('/usuarios/create') }}">Crear usuario</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="{{ url('/productos') }}">
                            <i class="fa fa-box" style="color: #ff7e00;"></i>
                            <span class="nav-label">Productos</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/pedidos') }}">
                            <i class="fa fa-shopping-cart" style="color: #ff7e00;"></i>
                            <span class="nav-label">Pedidos</span>
                        </a>

                        {{-- Enlace agregado para Sucursales --}}
                    {{-- En el Sidebar del Layout --}}
                <li>
                    <a href="{{ route('sucursales.index') }}">
                        <i class="fa fa-map-marker-alt" style="color: #ff7e00;"></i>
                        <span class="nav-label">Sucursales</span>
                    </a>
                </li>
                </ul>
            </div>
        </nav>

        {{-- CONTENIDO PRINCIPAL --}}
        <div id="page-wrapper">

            {{-- NAVBAR SUPERIOR --}}
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li class="dropdown">
                            <a class="dropdown-toggle count-info" data-bs-toggle="dropdown" href="#">
                                <i class="fa fa-user-circle fa-2x"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" style="background: #1a1a1a; border: 1px solid #333;">
                                <li><a href="/perfil" class="dropdown-item" style="color: white;">Perfil</a></li>
                                <li class="dropdown-divider" style="border-top: 1px solid #333;"></li>
                                <li>
                                    <a href="{{ route('logout') }}" class="dropdown-item" style="color: #ff5555;"
                                        onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                        Cerrar sesión
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST">@csrf</form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>

            {{-- CONTENIDO DE CADA VISTA --}}
            <div class="wrapper wrapper-content">
                @yield('contenido')
            </div>

            {{-- FOOTER --}}
            <div class="footer">
                <div class="float-right">
                    <a href="" style="color: #ff7e00;">Términos y condiciones</a>
                </div>
                <div>
                    <strong>Copyright</strong> TostaTech &copy; 2026
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.js') }}"></script>
    <script src="{{ asset('js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
    <script src="{{ asset('js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('js/inspinia.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('#side-menu').metisMenu();
        });
    </script>
</body>
</html>