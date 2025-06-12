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
        </ul>
        <hr>
        <p><strong>Total: $<span id="total">0.00</span></strong></p>
        <form id="frmSaveDataOrden" action="/order" method="POST">
    @csrf
    <input type="hidden" name="detalle" id="detalleInput">
    <input type="hidden" name="state" value="1" id="detalleInput">

    <div class="mb-2">
        <label for="mesa">Número de mesa:</label>
        <input type="number" min="1" name="numeromesa" class="form-control" required>
    </div>

    <div class="col-md-6">
        <label for="busquedaCliente" class="form-label">Buscar cliente</label>
        <input type="text" class="form-control" id="busquedaCliente" placeholder="Ingrese nombre del cliente">
    </div>
    <div class="col-md-4">
        <label for="clienteSelect" class="form-label">Seleccionar cliente</label>
        <select name="client" id="clienteSelect" class="form-select" required>
            <option value="">Seleccione un cliente</option>
            @foreach($clientes as $cliente)
                <option value="{{ $cliente->codigo }}">
                    {{ $cliente->nombre }} {{ $cliente->apellido }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-7 d-flex align-items-end">
        <button class="btn btn-outline-success mt-3 w-10" path="/client/create" id="addForm" data-bs-toggle="modal" data-bs-target="#myModal">Registrar nuevo cliente</button>
    </div>
    <div class="mb-2">
    <label for="mpago">Medio de Pago:</label>
    <select name="mpago" class="form-select" required>
        <option value="">Seleccione un medio de pago</option>
        @foreach($mediospago as $medio)
            <option value="{{ $medio->codigo }}">{{ $medio->nombre }}</option>
        @endforeach
    </select>
</div>

    <button type="submit" class="btn btn-primary mt-3">Guardar Orden</button>
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

    // Verificar si el producto ya está en el carrito
    const itemExistente = carrito.find(item => item.menu_id === id);
    
    if (itemExistente) {
        // Si ya existe, actualizar cantidad y subtotal
        itemExistente.cantidad += cantidad;
        itemExistente.subtotal = itemExistente.precio * itemExistente.cantidad;
    } else {
        // Si no existe, agregar nuevo item
        let item = {
            menu_id: id,
            nombre: nombre,
            precio: precio,
            cantidad: cantidad,
            subtotal: precio * cantidad
        };
        carrito.push(item);
    }
    
    actualizarCarrito();
}

function eliminarDelCarrito(index) {
    carrito.splice(index, 1); // Eliminar el elemento en la posición index
    actualizarCarrito();
}

function actualizarCarrito() {
    let lista = document.getElementById('carrito');
    lista.innerHTML = '';
    total = 0;

    carrito.forEach((item, index) => {
        total += item.subtotal;
        lista.innerHTML += `
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    ${item.nombre} x ${item.cantidad}
                    <span class="text-muted">$${item.precio.toFixed(2)} c/u</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-2">$${item.subtotal.toFixed(2)}</span>
                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarDelCarrito(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
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
<script>
    document.getElementById('busquedaCliente').addEventListener('input', function () {
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
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('clienteFormModal');

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const url = form.action;
            const formData = new FormData(form);

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) throw response;
                return response.json();
            })
            .then(data => {
                if (data.code === 200) {
                    // Cerrar el modal
                    const modalElement = document.getElementById('myModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);
                    modalInstance.hide();

                    // Limpiar formulario del modal
                    form.reset();

                    // Agregar nuevo cliente al dropdown y seleccionarlo
                    const clienteSelect = document.getElementById('clienteSelect');
                    const nuevaOpcion = document.createElement('option');
                    nuevaOpcion.value = data.cliente.codigo;
                    nuevaOpcion.textContent = `${data.cliente.nombre} ${data.cliente.apellido}`;
                    nuevaOpcion.selected = true;
                    clienteSelect.appendChild(nuevaOpcion);
                }
            })
            .catch(async (error) => {
                let errorMsg = 'Error al guardar el cliente.';
                try {
                    const errData = await error.json();
                    if (errData.message) {
                        const mensajes = Object.values(errData.message).flat().join('\n');
                        errorMsg = mensajes;
                    }
                } catch (_) {}
                alert(errorMsg);
            });
        });
    }
});
</script>


@endsection
