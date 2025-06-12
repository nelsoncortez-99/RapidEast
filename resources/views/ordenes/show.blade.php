@extends('layouts.app')

@section('title', 'Listado de Órdenes')

@section('content')
<div class="row">
    @foreach ($ordenes as $orden)
        <div class="col-md-4 mb-3">
            <div class="card {{ $orden->estado == 'completado' ? 'bg-success text-white' : 'bg-light' }}">
                <div class="card-body">
                    <h5 class="card-title">Mesa #{{ $orden->numeromesa }}</h5>
                    <p class="card-text">Cliente: {{ $orden->cliente->nombre ?? 'N/A' }} {{ $orden->cliente->apellido ?? '' }}</p>
                    <p class="card-text">Estado: <span id="estado-{{ $orden->id }}">{{ ucfirst($orden->estado) }}</span></p>

                    <h6>Detalle de la orden:</h6>
                    <ul>
                        @foreach ($orden->detalles as $detalle)
                            <li>
                                {{ $detalle->menu->nombre }} x {{ $detalle->cantidad }} - $
                                {{ number_format($detalle->precio * $detalle->cantidad, 2) }}
                            </li>
                        @endforeach
                    </ul>

                    <p><strong>Total: ${{ number_format($orden->detalles->sum(function($d) {
                        return $d->precio * $d->cantidad;
                    }), 2) }}</strong></p>

                    <button class="btn btn-sm btn-secondary" onclick="cambiarEstado({{ $orden->id }})">
                        Cambiar estado
                    </button>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@section('scripts')
<script>
function cambiarEstado(id) {
    fetch(`/order/${id}`, {  // Usa la ruta del resource
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ estado: 'toggle' }) // Envía un campo para identificar la acción
    })
    .then(res => res.json())
    .then(data => {
        const estadoText = document.getElementById(`estado-${id}`);
        const card = estadoText.closest('.card');
        estadoText.innerText = data.estado.charAt(0).toUpperCase() + data.estado.slice(1);

        if (data.estado === 'completado') {
            card.classList.remove('bg-light');
            card.classList.add('bg-success', 'text-white');
        } else {
            card.classList.remove('bg-success', 'text-white');
            card.classList.add('bg-light');
        }
    });
}
</script>
@endsection
