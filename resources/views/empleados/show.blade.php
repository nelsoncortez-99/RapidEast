@extends('layouts.app')
{{-- Definir un título --}}
@section('title','Empleados')

{{-- Definir contenido --}}
@section('content')
    <hr>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page">Inicio</li>
        <li class="breadcrumb-item active" aria-current="page">Empleados</li>
    </ol>
</nav>
<hr>
<div class="card">
    <div class="card-header">
        <div class="row text center">
            <div class="col">
                <h3>Listado de Empleados</h3>
            </div>
            <div class="col">
                <button class="btn btn-md btn-dark" path="/employee/create" id="addForm" data-bs-toggle="modal" data-bs-target="#myModal">
                    Agregar Empleado
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-hover table-bordered" id="datatables">
            <thead>
                <th>Código</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Teléfono</th>
                <th>Usuario</th>
                <th>Acciones</th>
            </thead>
            
        </table>
    </div>
</div>

@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('#datatables').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "/employee/show",
                type: "GET",
                data: function(d) {
                    // Asegurar que los parámetros se envíen correctamente
                    return {
                        draw: d.draw,
                        start: d.start,
                        length: d.length,
                        'search[value]': d.search.value,
                        'order[0][column]': d.order[0].column,
                        'order[0][dir]': d.order[0].dir
                    };
                },
                error: function(xhr, error, thrown) {
                    console.error('Error en la solicitud AJAX:', xhr.responseText);
                    // Mostrar mensaje de error al usuario si lo deseas
                }
            },
            columns: [
                { data: 'codigo', name: 'codigo' },
                { data: 'nombre', name: 'nombre' },
                { data: 'apellido', name: 'apellido' },
                { data: 'telefono', name: 'telefono' },
                { 
                    data: 'user', 
                    name: 'user',
                    render: function(data, type, row) {
                        // Mostrar "Sin usuario" si el campo viene vacío
                        return data || 'Sin usuario';
                    }
                },
                { 
                    data: 'codigo', 
                    name: 'actions',
                    render: function(data, type, row) {
                        return `
                            <button class="btn btn-sm btn-primary edit-btn" data-id="${data}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-btn" data-id="${data}">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                    },
                    orderable: false,
                    searchable: false
                }
            ],
            responsive: true
        });
    });
</script>
@endsection