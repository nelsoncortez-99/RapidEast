@extends('layouts.app')
{{-- Definir un título --}}
@section('title','Clientes')

{{-- Definir contenido --}}
@section('content')
    <hr>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page">Inicio</li>
        <li class="breadcrumb-item active" aria-current="page">Clientes</li>
    </ol>
</nav>
<hr>
<div class="card">
    <div class="card-header">
        <div class="row text center">
            <div class="col">
                <h3>Listado de Clientes</h3>
            </div>
            <div class="col">
                <button class="btn btn-md btn-dark" path="/client/create" id="addForm" data-bs-toggle="modal" data-bs-target="#myModal">
                    Agregar Cliente
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
                <th>Correo</th>
                <th>Acciones</th>
            </thead>
            
        </table>
    </div>
</div>

@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        var ruta = "/client/show";
        var columnas = [
            {data:'codigo'},
            {data:'nombre'},
            {data:'apellido'},
            {data:'correo'},
            {data:'codigo'}//para usar el id a la hora de editar y eliminar
        ]
        dt=generateDataTable(ruta, columnas);
    });
</script>
@endsection