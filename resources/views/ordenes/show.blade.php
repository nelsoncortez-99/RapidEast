@extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-4">Listado de Órdenes</h2>

    <input type="text" id="searchInput" class="form-control mb-4" placeholder="Buscar orden...">

    <div class="row" id="cardsContainer">
        @foreach($ordenes as $orden)
            <div class="col-md-4 mb-4 card-item">
                <div class="card shadow-sm">
                    <div class="card-body bg-primary text-white">
                        <h5 class="card-title">Orden #{{ $orden->codigo }}</h5>
                        <p class="card-text"><strong>Fecha:</strong> {{ $orden->fecha }}</p>
                        <p class="card-text"><strong>Mesa:</strong> {{ $orden->numeromesa }}</p>
                        <p class="card-text"><strong>Cliente:</strong> {{ $orden->client }}</p>
                        <p class="card-text"><strong>Total:</strong> ${{ number_format($orden->total, 2) }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>

<script>
    // Filtrar cards al escribir en input
    document.getElementById('searchInput').addEventListener('input', function() {
        const filtro = this.value.toLowerCase();
        document.querySelectorAll('.card-item').forEach(card => {
            const textoCard = card.innerText.toLowerCase();
            if(textoCard.includes(filtro)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>
@endsection
