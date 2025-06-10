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
    <div id="app" class="d-flex">
    {{-- Sidebar vertical personalizado --}}
    <nav class="bg-dark text-white border-end shadow-sm" style="min-width: 250px; height: 100vh;">
        <div class="p-3">
            {{-- Logo --}}
            <div class="text-center mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="img-fluid" style="max-height: 80px;">
                <h4 class="mt-2">
                    <a class="navbar-brand text-white text-decoration-none" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </h4>
            </div>

            <ul class="nav flex-column">
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @endif

                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item">
                        <a class="nav-link fw-bold fs-5 text-white" href="/home">Inicio</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link fw-bold fs-5 text-white" data-bs-toggle="collapse" href="#adminCollapse" role="button" aria-expanded="false">
                            Administrar
                        </a>
                        <div class="collapse" id="adminCollapse">
                            <ul class="list-unstyled ps-3">
                                <li><a class="nav-link text-white" href="/category">Categoría</a></li>
                                <li><a class="nav-link text-white" href="#">Métodos de Pago</a></li>
                                <li><a class="nav-link text-white" href="#">Estados</a></li>
                                <li><a class="nav-link text-white" href="#">Roles</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link fw-bold fs-5 text-white" data-bs-toggle="collapse" href="#empleadoCollapse" role="button" aria-expanded="false">
                            Empleados
                        </a>
                        <div class="collapse" id="empleadoCollapse">
                            <ul class="list-unstyled ps-3">
                                <li><a class="nav-link text-white" href="#">Empleados</a></li>
                                <li><a class="nav-link text-white" href="#">Usuarios</a></li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link fw-bold fs-5 text-white" href="/client">Clientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold fs-5 text-white" href="/menu">Menu</a>
                    </li>

                    <li class="nav-item mt-4">
                        <span class="d-block mb-2 fw-bold">{{ Auth::user()->name }}</span>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="btn btn-outline-light w-100">Cerrar Sesión</button>
                        </form>
                    </li>
                @endguest
            </ul>
        </div>
    </nav>

    {{-- Contenido principal --}}
    <div class="flex-grow-1 p-4">
        <main class="py-4">
            @yield('content')
        </main>
    </div>
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
