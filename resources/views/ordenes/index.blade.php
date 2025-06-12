@extends('layouts.app')

@section('title', 'Listado de Órdenes')

@section('content')
<div class="container">
    <h1 class="mb-4">Listado de Órdenes</h1>
    
    <div class="row">
        @foreach ($ordenes as $orden)
        <div class="col-md-4 mb-4">
            <div class="card {{ $orden->state == 'completado' ? 'bg-success text-white' : 'bg-light' }}">
                <div class="card-body">
                    <h5 class="card-title">Mesa #{{ $orden->numeromesa }}</h5>
                    <p class="card-text">Cliente: {{ $orden->cliente->nombre ?? 'N/A' }}</p>
                    <p class="card-text">Estado: <span id="estado-{{ $orden->codigo }}">{{ ucfirst($orden->state) }}</span></p>

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
                        <button class="btn btn-sm btn-secondary" 
                                onclick="cambiarEstado({{ $orden->codigo }})">
                            Cambiar estado
                        </button>
                        
                        <a href="{{ route('order.edit', $orden->codigo) }}" 
                           class="btn btn-sm btn-primary">
                            Editar
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@section('scripts')
<script>
function cambiarEstado(id) {
    fetch(`/order/${id}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ estado: 'toggle' })
    })
    .then(res => res.json())
    .then(data => {
        if(data.code === 200) {
            const estadoText = document.getElementById(`estado-${id}`);
            const card = estadoText.closest('.card');
            
            estadoText.textContent = data.estado.charAt(0).toUpperCase() + data.estado.slice(1);
            
            if(data.estado === 'completado') {
                card.classList.remove('bg-light');
                card.classList.add('bg-success', 'text-white');
            } else {
                card.classList.remove('bg-success', 'text-white');
                card.classList.add('bg-light');
            }
        }
    });
}
</script>
@endsection