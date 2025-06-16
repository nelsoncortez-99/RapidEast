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
    
    <!-- Carga archivos adicionales -->
    <link rel="stylesheet" href="{{asset('recursos/datatables/datatables.min.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Estilos adicionales para mejor visualización */
        .sidebar {
            min-width: 250px; 
            height: 100vh;
            transition: all 0.3s;
        }
        .nav-link {
            transition: all 0.2s;
        }
        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            padding-left: 10px;
        }
        .invalid-feedback {
            display: none;
        }
        .sidebar {
        min-width: 250px; 
        height: 100vh;
        transition: all 0.3s;
        position: fixed; /* Fija el sidebar */
        overflow-y: auto; /* Permite scroll interno si el contenido es muy largo */
        z-index: 1000; /* Asegura que esté por encima de otros elementos */
        }
        
        /* Contenedor principal debe tener margen izquierdo igual al ancho del sidebar */
        .main-content {
            margin-left: 250px; /* Igual al min-width del sidebar */
            width: calc(100% - 250px);
        }
        
        .nav-link {
            transition: all 0.2s;
        }
        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            padding-left: 10px;
        }
        .invalid-feedback {
            display: none;
        }
    </style>
</head>
<body>
    <div id="app" class="d-flex">
        {{-- Sidebar vertical personalizado --}}
        <nav class="bg-dark text-white border-end shadow-sm sidebar">
            <div class="p-3">
                {{-- Logo --}}
                <div class="text-center mb-4">
                    {{--<img src=".../img/logo.jpg" alt="Logo" class="img-fluid" style="max-height: 80px;">--}}
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
    @else
        <!-- Opción de Inicio para todos los roles -->
        <li class="nav-item">
            <a class="nav-link fw-bold fs-5 text-white" href="/home">
                <i class="bi bi-house-door me-2"></i>Inicio
            </a>
        </li>

        <!-- Sección de Administración solo para admin -->
        @if(Auth::user()->rol === 1)
        <li class="nav-item">
            <a class="nav-link fw-bold fs-5 text-white" data-bs-toggle="collapse" href="#adminCollapse" role="button" aria-expanded="false">
                <i class="bi bi-gear me-2"></i>Administrar
            </a>
            <div class="collapse" id="adminCollapse">
                <ul class="list-unstyled ps-3">
                    <li><a class="nav-link text-white" href="/category"><i class="bi bi-tag me-2"></i>Categoría</a></li>
                </ul>
            </div>
        </li>
        @endif

        <!-- Sección de Empleados solo para admin -->
        @if(Auth::user()->rol === 1)
        <li class="nav-item">
            <a class="nav-link fw-bold fs-5 text-white" data-bs-toggle="collapse" href="#empleadoCollapse" role="button" aria-expanded="false">
                <i class="bi bi-people me-2"></i>Empleados
            </a>
            <div class="collapse" id="empleadoCollapse">
                <ul class="list-unstyled ps-3">
                    <li><a class="nav-link text-white" href="/employee"><i class="bi bi-person-vcard me-2"></i>Empleados</a></li>
                    <li><a class="nav-link text-white" href="/user"><i class="bi bi-person me-2"></i>Usuarios</a></li>
                </ul>
            </div>
        </li>
        @endif

        <!-- Clientes visible para admin y cajero -->
        @if(in_array(Auth::user()->rol, [1,2]))
        <li class="nav-item">
            <a class="nav-link fw-bold fs-5 text-white" href="/client">
                <i class="bi bi-person-lines-fill me-2"></i>Clientes
            </a>
        </li>
        @endif

        <!-- Menú visible para admin -->
        @if(Auth::user()->rol === 1)
        <li class="nav-item">
            <a class="nav-link fw-bold fs-5 text-white" href="/menu">
                <i class="bi bi-menu-button-wide me-2"></i>Menu 
            </a>
        </li>
        @endif

        <!-- Perfil y logout para todos -->
        <li class="nav-item mt-4">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-person-circle me-2"></i>
                <span class="fw-bold">{{ Auth::user()->name }}</span>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-outline-light w-100">
                    <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                </button>
            </form>
        </li>
    @endguest
</ul>
            </div>
        </nav>

        {{-- Contenido principal --}}
        <div class="main-content flex-grow-1 p-4">
            <main class="py-4">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h1 class="modal-title fs-5" id="modalLabel">Modal title</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script para manejar modales y actualización de clientes -->
    <script>
        $(document).ready(function() {
            // Manejar el cierre del modal para limpiar el formulario
            $('#myModal').on('hidden.bs.modal', function () {
                $('#loadForm').html('...');
                $('.invalid-feedback').hide();
            });
            
            // Función global para actualizar dropdown de clientes
            window.updateClientDropdown = function(clienteData) {
                const select = $('#clienteSelect');
                if (select.length) {
                    // Verificar si el cliente ya existe
                    if (!select.find(`option[value="${clienteData.codigo}"]`).length) {
                        // Agregar nueva opción y seleccionarla
                        select.append(new Option(
                            `${clienteData.nombre} ${clienteData.apellido}`,
                            clienteData.codigo,
                            true,
                            true
                        ));
                    } else {
                        // Si ya existe, seleccionarlo
                        select.val(clienteData.codigo);
                    }
                    select.trigger('change');
                }
            };
        });
    </script>
    
    
    {{-- Fin Archivos JS globales --}}
    
    {{-- cargar scripts de cada vista--}}
    @yield('scripts')
</body>
</html>