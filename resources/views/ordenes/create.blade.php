@extends('layouts.app')

@section('title', isset($orden) ? 'Editar Orden' : 'Nueva Orden')

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
                <div class="col-md-4 menu-item" 
                     data-nombre="{{ strtolower($item->nombre) }}" 
                     data-precio="{{ $item->precio }}" 
                     data-categoria="{{ $item->categoria->codigo ?? '' }}">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5>{{ $item->nombre }}</h5>
                            <p>{{ $item->descripcion }}</p>
                            <p><strong>Precio: ${{ number_format($item->precio, 2) }}</strong></p>
                            <input type="number" min="1" value="1" class="form-control cantidad" id="cantidad-{{ $item->codigo }}">
                            <button class="btn btn-sm btn-success mt-2" 
                                    onclick="agregarAlCarrito({{ $item->codigo }}, '{{ addslashes($item->nombre) }}', {{ $item->precio }})">
                                Agregar
                            </button>
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
        </ul>
        <hr>
        <p><strong>Total: $<span id="total">0.00</span></strong></p>
        
        <form id="frmSaveDataOrden" action="{{ isset($orden) ? route('order.update', $orden->codigo) : route('order.store') }}" method="POST">
            @csrf
            @if(isset($orden))
                @method('PUT')
            @endif
            
            <input type="hidden" name="detalle" id="detalleInput">
            <input type="hidden" name="state" value="{{ isset($orden) ? $orden->state : '1' }}">

            <div class="mb-2">
                <label for="mesa">Número de mesa:</label>
                <input type="number" min="1" name="numeromesa" class="form-control" 
                       value="{{ $orden->numeromesa ?? old('numeromesa') }}" required>
            </div>

            <div class="row mb-2">
                <div class="col-md-8">
                    <label for="busquedaCliente" class="form-label">Buscar cliente</label>
                    <input type="text" class="form-control" id="busquedaCliente" placeholder="Ingrese nombre del cliente">
                </div>
                <div class="col-md-8">
                    <label for="clienteSelect" class="form-label">Seleccionar cliente</label>
                    <select name="client" id="clienteSelect" class="form-select" required>
                        <option value="">Seleccione un cliente</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->codigo }}"
                                {{ (isset($orden) && $orden->client == $cliente->codigo) ? 'selected' : '' }}>
                                {{ $cliente->nombre }} {{ $cliente->apellido }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-outline-success" path="/client/create" id="addForm" data-bs-toggle="modal" data-bs-target="#myModal">
                        <i class="fas fa-plus"></i> Nuevo
                    </button>
                </div>
            </div>

            <div class="mb-2">
                <label for="mpago">Medio de Pago:</label>
                <select name="mpago" class="form-select" required>
                    <option value="">Seleccione un medio de pago</option>
                    @foreach($mediospago as $medio)
                        <option value="{{ $medio->codigo }}"
                            {{ (isset($orden) && $orden->mpago == $medio->codigo) ? 'selected' : '' }}>
                            {{ $medio->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                            <label for="state" class="form-label">Estado</label>
                            <select name="state" id="state" class="form-select" required>
                                <option value="">Seleccione Estado</option>
                                @foreach($estados as $estado)
                                    <option value="{{ $estado->codigo }}" 
                                        {{ (isset($orden) && $orden->state == $estado->codigo) ? 'selected' : '' }}>
                                        {{ $estado->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    {{ isset($orden) ? 'Actualizar Orden' : 'Guardar Orden' }}
                </button>
                
                @if(isset($orden))
                    <a href="{{ route('order.index') }}" class="btn btn-secondary">Cancelar</a>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Inicialización del carrito
    let carrito = {!! 
        isset($orden) 
            ? json_encode($orden->detalles->map(function($detalle) {
                return [
                    'menu_id' => $detalle->menu_id,
                    'nombre' => $detalle->menu->nombre,
                    'precio' => $detalle->menu->precio,
                    'cantidad' => $detalle->cantidad,
                    'subtotal' => $detalle->subtotal
                ];
            }))
            : '[]'
    !!};
    
    let total = {{ isset($orden) ? $orden->total : 0 }};

    // Función para formatear moneda
    function formatMoney(amount) {
        return parseFloat(amount).toFixed(2);
    }

    // Inicializar el carrito al cargar
    document.addEventListener('DOMContentLoaded', function() {
        actualizarCarrito();
        filtrarMenu(); // Aplicar filtros iniciales si hay valores
    });

    // Función para agregar productos al carrito
    function agregarAlCarrito(id, nombre, precio) {
        const cantidadInput = document.getElementById('cantidad-' + id);
        let cantidad = parseInt(cantidadInput.value);
        
        if (cantidad < 1 || isNaN(cantidad)) {
            cantidadInput.value = 1;
            cantidad = 1;
        }

        const itemExistente = carrito.find(item => item.menu_id == id);
        
        if (itemExistente) {
            itemExistente.cantidad += cantidad;
            itemExistente.subtotal = itemExistente.precio * itemExistente.cantidad;
        } else {
            carrito.push({
                menu_id: id,
                nombre: nombre,
                precio: parseFloat(precio),
                cantidad: cantidad,
                subtotal: parseFloat(precio) * cantidad
            });
        }
        
        actualizarCarrito();
        cantidadInput.value = 1; // Resetear cantidad después de agregar
    }

    // Función para eliminar productos del carrito
    function eliminarDelCarrito(index) {
        carrito.splice(index, 1);
        actualizarCarrito();
    }

    // Función para actualizar la visualización del carrito
    function actualizarCarrito() {
        const lista = document.getElementById('carrito');
        lista.innerHTML = '';
        total = 0;

        carrito.forEach((item, index) => {
            total += item.subtotal;
            lista.innerHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        ${item.nombre} x ${item.cantidad}
                        <span class="text-muted">$${formatMoney(item.precio)} c/u</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="me-2">$${formatMoney(item.subtotal)}</span>
                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarDelCarrito(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </li>
            `;
        });

        document.getElementById('total').innerText = formatMoney(total);
        document.getElementById('detalleInput').value = JSON.stringify(carrito);
    }

    // Funciones de filtrado
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

    // Event listeners para filtros
    searchInput.addEventListener('input', filtrarMenu);
    categoriaFilter.addEventListener('change', filtrarMenu);

    // Filtrado de clientes
    document.getElementById('busquedaCliente').addEventListener('input', function() {
        const filtro = this.value.toLowerCase();
        const opciones = document.querySelectorAll('#clienteSelect option');

        opciones.forEach(op => {
            const texto = op.textContent.toLowerCase();
            if (texto.includes(filtro) || op.value === "") {
                op.style.display = 'block';
            } else {
                op.style.display = 'none';
            }
        });
    });

    // Manejo del formulario de nuevo cliente (modal)
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('clienteFormModal');

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const url = form.action;
                const formData = new FormData(form);

                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => {
                    if (!response.ok) throw response;
                    return response.json();
                })
                .then(data => {
                    if (data.code === 200) {
                        // Cerrar el modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('myModal'));
                        modal.hide();

                        // Agregar nuevo cliente al dropdown y seleccionarlo
                        const clienteSelect = document.getElementById('clienteSelect');
                        const nuevaOpcion = document.createElement('option');
                        nuevaOpcion.value = data.cliente.codigo;
                        nuevaOpcion.textContent = `${data.cliente.nombre} ${data.cliente.apellido}`;
                        nuevaOpcion.selected = true;
                        clienteSelect.appendChild(nuevaOpcion);

                        // Mostrar notificación
                        toastr.success(data.message);
                    }
                })
                .catch(async (error) => {
                    let errorMsg = 'Error al guardar el cliente.';
                    try {
                        const errData = await error.json();
                        if (errData.message) {
                            errorMsg = Object.values(errData.message).flat().join('\n');
                        } else if (errData.error) {
                            errorMsg = errData.error;
                        }
                    } catch (_) {}
                    toastr.error(errorMsg);
                });
            });
        }
    });
</script>
@endsection