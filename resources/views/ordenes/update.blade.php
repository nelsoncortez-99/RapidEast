@extends('layouts.app')

@section('title', 'Editar Orden #'.$orden->codigo)

@section('content')
<div class="container">
    <h1 class="mb-4">Editar Orden #{{ $orden->codigo }}</h1>
    
    <div class="row">
        <!-- Filtro de productos -->
        <div class="col-md-12 mb-3">
            <input type="text" id="search" class="form-control" placeholder="Buscar por nombre o precio">
            <select id="categoriaFilter" class="form-select mt-2">
                <option value="">Todas las categorías</option>
                @foreach ($categorias as $cat)
                    <option value="{{ $cat->codigo }}">{{ $cat->nombre }}</option>
                @endforeach
            </select>
        </div>

        <!-- Catálogo de productos -->
        <div class="col-md-8">
            <h4>Menú</h4>
            <div class="row" id="menu-list">
                @foreach ($menu as $item)
                    <div class="col-md-4 menu-item mb-3" 
                         data-nombre="{{ strtolower($item->nombre) }}" 
                         data-precio="{{ $item->precio }}" 
                         data-categoria="{{ $item->categoria->codigo ?? '' }}">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5>{{ $item->nombre }}</h5>
                                <p class="text-muted">{{ $item->descripcion }}</p>
                                <p><strong>Precio: ${{ number_format($item->precio, 2) }}</strong></p>
                                <div class="input-group">
                                    <input type="number" min="1" value="1" 
                                           class="form-control cantidad" id="cantidad-{{ $item->codigo }}">
                                    <button class="btn btn-success" 
                                            onclick="agregarAlCarrito({{ $item->codigo }}, '{{ $item->nombre }}', {{ $item->precio }})">
                                        <i class="fas fa-plus"></i> Agregar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Formulario de orden -->
        <div class="col-md-4">
            <form id="frmUpdateOrder" action="{{ route('order.update', $orden->codigo) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="detalle" id="detalleInput">
                
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Detalles de la Orden</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="numeromesa" class="form-label">Número de Mesa</label>
                            <input type="number" name="numeromesa" class="form-control" 
                                   value="{{ old('numeromesa', $orden->numeromesa) }}" required min="1">
                        </div>
                        
                        <div class="mb-3">
                            <label for="client" class="form-label">Cliente</label>
                            <select name="client" id="client" class="form-select" required>
                                <option value="">Seleccione un cliente</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->codigo }}" 
                                        {{ old('client', $orden->client) == $cliente->codigo ? 'selected' : '' }}>
                                        {{ $cliente->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="mpago" class="form-label">Método de Pago</label>
                            <select name="mpago" id="mpago" class="form-select" required>
                                <option value="">Seleccione método de pago</option>
                                @foreach($mediospago as $metodo)
                                    <option value="{{ $metodo->codigo }}" 
                                        {{ old('mpago', $orden->mpago) == $metodo->codigo ? 'selected' : '' }}>
                                        {{ $metodo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Productos Seleccionados</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group mb-3" id="carrito">
                            @foreach($orden->detalles as $detalle)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    {{ $detalle->menu->nombre }} x {{ $detalle->cantidad }}
                                    <span class="text-muted">(${{ number_format($detalle->menu->precio, 2) }} c/u)</span>
                                </div>
                                <span class="badge bg-primary rounded-pill">
                                    ${{ number_format($detalle->subtotal, 2) }}
                                </span>
                            </li>
                            @endforeach
                        </ul>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Total:</h5>
                            <h5 id="total">${{ number_format($orden->total, 2) }}</h5>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('order.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
 
@section('scripts')
<script>
// Variables globales
let carrito = @json($orden->detalles->map(function($detalle) {
    return [
        'menu_id' => $detalle->menu_id,
        'nombre' => $detalle->menu->nombre,
        'precio' => $detalle->menu->precio,
        'cantidad' => $detalle->cantidad,
        'subtotal' => $detalle->subtotal
    ];
}));

// Función para actualizar el carrito
function actualizarCarrito() {
    const lista = $('#carrito');
    const totalElement = $('#total');
    const detalleInput = $('#detalleInput');
    
    lista.empty();
    let total = 0;
    
    carrito.forEach((item, index) => {
        total += item.subtotal;
        lista.append(`
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    ${item.nombre} x ${item.cantidad}
                    <span class="text-muted">($${item.precio.toFixed(2)} c/u)</span>
                </div>
                <div>
                    <span class="badge bg-primary rounded-pill me-2">
                        $${item.subtotal.toFixed(2)}
                    </span>
                    <button class="btn btn-sm btn-danger" onclick="eliminarDelCarrito(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </li>
        `);
    });
    
    totalElement.text('$' + total.toFixed(2));
    detalleInput.val(JSON.stringify(carrito));
}

// Función para agregar producto al carrito
function agregarAlCarrito(id, nombre, precio) {
    const cantidad = parseInt($('#cantidad-' + id).val());
    
    if (cantidad < 1) {
        Swal.fire('Error', 'La cantidad debe ser al menos 1', 'error');
        return;
    }

    const itemExistente = carrito.find(item => item.menu_id == id);
    
    if (itemExistente) {
        itemExistente.cantidad += cantidad;
        itemExistente.subtotal = itemExistente.precio * itemExistente.cantidad;
    } else {
        carrito.push({
            menu_id: id,
            nombre: nombre,
            precio: precio,
            cantidad: cantidad,
            subtotal: precio * cantidad
        });
    }
    
    actualizarCarrito();
    $('#cantidad-' + id).val(1);
}

// Función para eliminar producto del carrito
function eliminarDelCarrito(index) {
    carrito.splice(index, 1);
    actualizarCarrito();
}

// Filtrar productos
function filtrarMenu() {
    const texto = $('#search').val().toLowerCase();
    const categoria = $('#categoriaFilter').val();
    
    $('.menu-item').each(function() {
        const nombre = $(this).data('nombre');
        const precio = $(this).data('precio').toString();
        const cat = $(this).data('categoria');
        
        const coincideTexto = nombre.includes(texto) || precio.includes(texto);
        const coincideCategoria = !categoria || cat == categoria;
        
        $(this).toggle(coincideTexto && coincideCategoria);
    });
}

// Eventos
$(document).ready(function() {
    $('#search').on('input', filtrarMenu);
    $('#categoriaFilter').on('change', filtrarMenu);
    
    $('#frmUpdateOrder').on('submit', function() {
        if (carrito.length === 0) {
            Swal.fire('Error', 'Debe agregar al menos un producto', 'error');
            return false;
        }
    });
});
</script>
@endsection