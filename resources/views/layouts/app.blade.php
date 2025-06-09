<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <!--Carga archivos adicionales-->
    <link rel="stylesheet" href="{{asset('recursos/datatables/datatables.min.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-warning border-bottom border-body shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                                {{-- Agregar opciones de menú --}}
                            <li class="nav-item">
                            <a class="nav-link active fw-bold fs-5" aria-current="page" href="/home">Inicio</a>
                            </li>
                            <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-bold fs-5" href="#" id="administrarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Administrar
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="administrarDropdown">
                                <li><a class="dropdown-item" href="/category">Categoría</a></li>
                                <li><a class="dropdown-item" href="#">Métodos de Pago</a></li>
                                <li><a class="dropdown-item" href="#">Estados</a></li>
                                <li><a class="dropdown-item" href="#">Roles</a></li>
                            </ul>
                            </li>
                            <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-bold fs-5" href="#" id="administrarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Empleados
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="administrarDropdown">
                                <li><a class="dropdown-item fw-bold fs-5" href="#">Empleados</a></li>
                                <li><a class="dropdown-item fw-bold fs-5" href="#">Usuarios</a></li>
                            </ul>
                            </li>

                            <li class="nav-item">
                            <a class="nav-link fw-bold fs-5" href="/client">Clientes</a>
                            </li>
                            <li class="nav-item">
                            <a class="nav-link fw-bold fs-5" href="/menu">Menu</a>
                            </li>
                            {{--li para mostrar lo de usuario--}}

                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    {{-- Modal --}}
<div class="modal fade" id="myModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalLabel">Modal title</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="loadForm">
                ...
            </div>
        </div>
    </div>
</div>
{{-- Inicio Archivos JS globales --}}
<script src="{{ asset('recursos/jquery.min.js') }}"></script>
<script src="{{ asset('recursos/sweetalert/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('recursos/datatables/datatables.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js"></script>
<script src="{{ asset('recursos/js/functions.js') }}"></script>
<script src="{{ asset('recursos/js/custom.js') }}"></script>

{{-- Fin Archivos JS globales --}}
{{-- cargar scripts de cada vista--}}
@yield('scripts')

</body>
</html>
