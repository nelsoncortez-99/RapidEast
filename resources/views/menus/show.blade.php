@extends('layouts.app')
{{-- Definir un título --}}
@section('title','Menu')

{{-- Definir contenido --}}
@section('content')
    <hr>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item" aria-current="page">Inicio</li>
        <li class="breadcrumb-item active" aria-current="page">Menu</li>
    </ol>
</nav>
<hr>
<div class="card">
    <div class="card-header">
        <div class="row text center">
            <div class="col">
                <h3>Menu</h3>
            </div>
            <div class="col">
                <button class="btn btn-md btn-dark" path="/menu/create" id="addForm" data-bs-toggle="modal" data-bs-target="#myModal">
                    Agregar al Menu
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-hover table-bordered" id="datatables">
            <thead>
                <th>Código</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>Categoria</th>
                <th>Acciones</th>
            </thead>
            
        </table>
    </div>
</div>

@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function () {
        var ruta = "/menu/show";
        var columnas = [
            {data:'codigo'},
            {data:'nombre'},
            {data:'descripcion'},
            {data:'precio'},
            {data:'categoria'},
            {data:'codigo'}//para usar el id a la hora de editar y eliminar
        ]
        dt=generateDataTable(ruta, columnas);
    });
</script>
@endsection