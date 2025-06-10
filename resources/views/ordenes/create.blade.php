@extends('layouts.app')

@section('title', 'Nueva Orden')

@section('content')
<div class="row">
    {{-- Filtro --}}
    <div class="col-md-12 mb-3">
        <input type="text" id="search" class="form-control" placeholder="Buscar por nombre o precio">
        <select id="categoriaFilter" class="form-select mt-2">
            <option value="">Todas las categorías</option>
            @foreach ($categorias as $cat)
                <option value="{{ $cat->codigo }}">{{ $cat->nombre }}</option>
            @endforeach
        </select>
    </div>

    {{-- Catálogo de productos --}}
    <div class="col-md-8">
        <h4>Menú</h4>
        <div class="row" id="menu-list">
            @foreach ($menu as $item)
                <div class="col-md-4 menu-item" data-nombre="{{ strtolower($item->nombre) }}" data-precio="{{ $item->precio }}" data-categoria="{{ $item->categoria->codigo ?? '' }}">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5>{{ $item->nombre }}</h5>
                            <p>{{ $item->descripcion }}</p>
                            <p><strong>Precio: ${{ $item->precio }}</strong></p>
                            <input type="number" min="1" value="1" class="form-control cantidad" id="cantidad-{{ $item->codigo }}">
                            <button class="btn btn-sm btn-success mt-2" onclick="agregarAlCarrito({{ $item->codigo }}, '{{ $item->nombre }}', {{ $item->precio }})">Agregar</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Carrito --}}
    <div class="col-md-4">
        <h4>Orden actual</h4>
        <ul class="list-group" id="carrito">
            <!-- Aquí se agregan los productos -->
        </ul>
        <hr>
        <p><strong>Total: $<span id="total">0.00</span></strong></p>
        <form action="{{ route('order.store') }}" method="POST">
            @csrf
            <input type="hidden" name="detalle" id="detalleInput">
            <!-- Aquí otros datos como mesa, cliente, etc -->
            <button type="submit" class="btn btn-primary">Guardar Orden</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let carrito = [];
    let total = 0;

    function agregarAlCarrito(id, nombre, precio) {
        let cantidad = parseInt(document.getElementById('cantidad-' + id).value);
        if (cantidad < 1) return;

        let item = {
            menu_id: id,
            nombre: nombre,
            precio: precio,
            cantidad: cantidad,
            subtotal: precio * cantidad
        };

        carrito.push(item);
        actualizarCarrito();
    }

    function actualizarCarrito() {
        let lista = document.getElementById('carrito');
        lista.innerHTML = '';
        total = 0;

        carrito.forEach((item) => {
            total += item.subtotal;
            lista.innerHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    ${item.nombre} x ${item.cantidad}
                    <span>$${item.subtotal.toFixed(2)}</span>
                </li>
            `;
        });

        document.getElementById('total').innerText = total.toFixed(2);
        document.getElementById('detalleInput').value = JSON.stringify(carrito);
    }

    // FILTRADO
    const searchInput = document.getElementById('search');
    const categoriaFilter = document.getElementById('categoriaFilter');
    const menuItems = document.querySelectorAll('.menu-item');

    function filtrarMenu() {
        const textoBusqueda = searchInput.value.toLowerCase();
        const categoriaSeleccionada = categoriaFilter.value;

        menuItems.forEach(item => {
            const nombre = item.getAttribute('data-nombre');
            const precio = item.getAttribute('data-precio');
            const categoria = item.getAttribute('data-categoria');

            const coincideTexto = nombre.includes(textoBusqueda) || precio.includes(textoBusqueda);
            const coincideCategoria = !categoriaSeleccionada || categoria === categoriaSeleccionada;

            if (coincideTexto && coincideCategoria) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filtrarMenu);
    categoriaFilter.addEventListener('change', filtrarMenu);
</script>
@endsection
