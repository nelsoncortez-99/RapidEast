{{-- Heredamos la estructura del archivo app.blade.php --}}
@extends('layouts.app')
{{-- Definir un título --}}
@section('title','Inicio')

{{-- Definir contenido --}}
@section('content')
    <hr>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">Home</li>
    </ol>
</nav>
<hr>
<h1>Pantalla de Inicio</h1>
<hr>
    <div class="container my-5">
    <div class="container my-5">
    <div class="row">
        @if(in_array(Auth::user()->rol, [1,2]))
        <div class="col-md-6 mb-4 d-flex justify-content-center">
            <a href="/order/create"
                class="btn btn-primary text-white fw-bold text-center d-flex align-items-center justify-content-center"
                style="width: 100%; height: 150px; font-size: 2rem; max-width: 500px;">
                Nueva Orden
            </a>
            
        </div>
        @endif
        @if(in_array(Auth::user()->rol, [1,3]))
        <div class="col-md-6 mb-4 d-flex justify-content-center">
            <a href="{{ route('order.index') }}"
                class="btn btn-secondary text-white fw-bold text-center d-flex align-items-center justify-content-center"
                style="width: 100%; height: 150px; font-size: 2rem; max-width: 500px;">
                Órdenes
            </a>
        </div>
        @endif
    </div>
</div>

</div>
@if(Auth::user()->rol === 1)
<div class="row">
    <!-- Cards con resumen -->
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Ventas del día</h5>
                <p class="card-text fs-3">$1,250</p>
                <p class="card-text">45 órdenes</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
            <h5 class="card-title">Pedidos pendientes</h5>
            <p class="card-text fs-3">7</p>
            <p class="card-text">En preparación</p>
        </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3">
            <div class="card-body">
                <h5 class="card-title">Stock bajo</h5>
                <p class="card-text fs-3">3 ingredientes</p>
                <p class="card-text">Reponer pronto</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <h5 class="card-title">Productos más vendidos</h5>
                <ul>
                    <li>Hamburguesa Clásica</li>
                    <li>Papas Fritas</li>
                    <li>Refresco Cola</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
    