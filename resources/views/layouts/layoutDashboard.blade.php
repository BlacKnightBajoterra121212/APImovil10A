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
        .pagination .page-item {
            width: 40px;
            text-align: center;
        }

        .pagination .page-link {
            display: block;
            width: 100%;
            height: 100%;
            padding: 10px;
            text-align: center;
            font-size: 14px;
        }

        .pagination .page-item.active .page-link {
            background-color: #1ab394;
            color: white;
        }

        .navbar-static-side {
            width: 220px;
            flex: 0 0 220px;
        }

        #side-menu {
            width: 100%;
        }

        .nav-second-level {
            overflow: hidden;
        }

        .nav-second-level {
            overflow: hidden;
        }
    </style>

</head>

<body>

<img src="http://10.0.2.2:8000/css/bootstrap.min.css">

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

                        <div class="logo-element">
                            APP
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

                        <ul class="nav nav-second-level collapse">

                            <li>
                                <a href="{{ url('/usuarios') }}">Lista de usuarios</a>
                            </li>

                            <li>
                                <a href="{{ url('/usuarios/create') }}">Crear usuario</a>
                            </li>

                        </ul>

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
        <div id="page-wrapper" class="gray-bg">

            {{-- NAVBAR SUPERIOR --}}
            <div class="row border-bottom">

                <nav class="navbar navbar-static-top">

                    <div class="navbar-header">

                        <a class="navbar-minimalize btn btn-primary">
                            <i class="fa fa-bars"></i>
                        </a>

                    </div>

                    <ul class="nav navbar-top-links navbar-right">

                        <li class="dropdown">

                            <a class="dropdown-toggle" data-bs-toggle="dropdown">

                                <i class="fa fa-user"></i>

                            </a>

                            <ul class="dropdown-menu dropdown-menu-end">

                                <li>
                                    <a href="/perfil">Perfil</a>
                                </li>

                                <li>

                                    <a href="{{ route('logout') }}"
                                        onclick="event.preventDefault();document.getElementById('logout-form').submit();">

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

                    <a href="">
                        Términos y condiciones
                    </a>

                </div>

                <div>
                    <strong>Copyright</strong> MiApp 2026
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
        document.addEventListener("DOMContentLoaded", function () {

            $(document).ready(function () {
                $('#side-menu').metisMenu();
            });

        });
    </script>
</body>

</html>