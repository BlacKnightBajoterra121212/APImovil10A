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
    <link rel="stylesheet" href="{{ asset('css/fontawesome.css') }}">

    <style>
        /* --- AJUSTES DE COLOR TOSTATECH (NEGRO Y NARANJA) --- */
        
        body {
            background: #2f4050; /* Color de fondo base de Inspinia, pero el contenido será degradado */
        }

        /* Sidebar en Negro */
        .navbar-default.navbar-static-side {
            background: #000000 !important;
            border-right: 1px solid rgba(255, 183, 0, 0.2);
        }

        #side-menu li a {
            color: #ffffff !important;
            font-weight: 500;
        }

        /* Hover y Active en Naranja TostaTech */
        #side-menu li.active > a, 
        #side-menu li a:hover {
            background-color: #ffb700 !important;
            color: #000000 !important;
        }

        #side-menu li.active {
            border-left: 4px solid #ff7b00;
        }

        /* Header del Perfil en el Sidebar */
        .nav-header {
            background: #000000 !important;
            border-bottom: 1px solid rgba(255, 183, 0, 0.2);
        }

        .nav-header .font-bold {
            color: #ffb700;
        }

        /* Page Wrapper (Fondo de la aplicación) */
        #page-wrapper {
            background: linear-gradient(135deg, #ff7b00, #ffb700) !important;
            min-height: 100vh;
        }

        /* Top Navbar */
        .navbar-static-top {
            background: #000000 !important;
            border-bottom: 1px solid rgba(255, 183, 0, 0.2) !important;
        }

        .navbar-static-top a {
            color: #ffb700 !important;
        }

        /* Footer */
        .footer {
            background: rgba(0, 0, 0, 0.8) !important;
            border-top: 1px solid #ffb700 !important;
            color: #fff !important;
        }

        .footer a {
            color: #ffb700 !important;
        }

        /* Botón de Hamburguesa */
        .navbar-minimalize {
            background-color: #ffb700 !important;
            border-color: #ffb700 !important;
            color: #000 !important;
        }

        /* Pagination */
        .pagination .page-item.active .page-link {
            background-color: #ffb700 !important;
            border-color: #ffb700 !important;
            color: #000 !important;
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
                        <div class="dropdown profile-element">
                            <img class="rounded-circle" width="40" src="{{ asset('img/user.png') }}">
                            <span class="block m-t-xs font-bold">
                                {{ Auth::user()->name ?? 'Usuario' }}
                            </span>
                        </div>
                        <div class="logo-element" style="color: #ffb700; font-weight: 800;">
                            TT
                        </div>
                    </li>

                    <li>
                        <a href="{{ url('/dashboard') }}">
                            <i class="fa fa-home"></i>
                            <span class="nav-label">Inicio</span>
                        </a>
                    </li>

                    <li>
                        <a href="javascript:void(0)">
                            <i class="fa fa-users"></i>
                            <span class="nav-label">Usuarios</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-second-level collapse" style="background: #111;">
                            <li><a href="{{ url('/usuarios') }}">Lista de usuarios</a></li>
                            <li><a href="{{ url('/usuarios/create') }}">Crear usuario</a></li>
                        </ul>
                    </li>

                    <li>
                        <a href="{{ url('/pedidos') }}">
                            <i class="fa fa-shopping-cart"></i>
                            <span class="nav-label">Pedidos</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/productos') }}">
                            <i class="fa fa-box"></i>
                            <span class="nav-label">Productos</span>
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
                        <a class="navbar-minimalize btn btn-primary"><i class="fa fa-bars"></i></a>
                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="fa fa-user"></i> Mi Cuenta
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/perfil">Perfil</a></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Cerrar sesión
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                        @csrf
                                    </form>
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
                    <a href="">Términos y condiciones</a>
                </div>
                <div>
                    <strong>Copyright</strong> TostaTech &copy; 2026
                </div>
            </div>

        </div>
    </div>

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