@extends('layouts.app')

@section('title', 'Listado de Órdenes')

@section('content')
<div class="container">
    <h1 class="mb-4">Listado de Órdenes</h1>
    
    <div class="row" id="orders-container">
    @foreach ($ordenes as $orden)
    <div class="col-md-4 mb-4" data-order-id="{{ $orden->codigo }}">
        <div class="card {{ $orden->state == 2 ? 'bg-success text-white' : 'bg-light' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <h5 class="card-title">Mesa #{{ $orden->numeromesa }}</h5>
                    <button class="btn btn-sm btn-danger delete-order" 
                            data-url="{{ route('order.destroy', $orden->codigo) }}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="card-text">Cliente: {{ $orden->cliente->nombre ?? 'N/A' }}</p>
                
                <h6>Detalle:</h6>
                <ul class="list-unstyled">
                    @foreach ($orden->detalles as $detalle)
                    <li>
                        {{ $detalle->menu->nombre }} x {{ $detalle->cantidad }}
                        - ${{ number_format($detalle->subtotal, 2) }}
                    </li>
                    @endforeach
                </ul>

                <p class="fw-bold">Total: ${{ number_format($orden->total, 2) }}</p>

                <div class="d-flex gap-2">
                    <span id="state-badge-{{ $orden->codigo }}" 
                          class="badge {{ $orden->state == 1 ? 'bg-warning' : 'bg-success' }}">
                        {{ $orden->state == 1 ? 'Pendiente' : 'Completado' }}
                    </span>
                    
                    <a href="{{ route('order.edit', $orden->codigo) }}" 
                        class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

@section('scripts')
<script>
function deleteOrder(button) {
    const url = button.getAttribute('data-url');
    const token = document.querySelector('meta[name="csrf-token"]').content;
    
    Swal.fire({
        title: '¿Eliminar esta orden?',
        text: "¡No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                throw new Error('Error en la respuesta');
            })
            .then(data => {
                if (data.success) {
                    button.closest('[data-order-id]').remove();
                    Swal.fire('¡Eliminada!', 'La orden ha sido eliminada.', 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'No se pudo eliminar la orden', 'error');
            });
        }
    });
}

// Asignar eventos a los botones
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-order').forEach(button => {
        button.addEventListener('click', function() {
            deleteOrder(this);
        });
    });
    
    // Resto de tus event listeners...
});

// Asignar eventos a los botones
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.complete-order').forEach(button => {
        button.addEventListener('click', function() {
            completeOrder(this);
        });
    });
    
    document.querySelectorAll('.delete-order').forEach(button => {
        button.addEventListener('click', function() {
            deleteOrder(this);
        });
    });
});