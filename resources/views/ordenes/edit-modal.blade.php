<div class="container-fluid">
    <form id="frmUpdateOrder" action="{{ route('order.update', $orden->codigo) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Número de Mesa</label>
                    <input type="number" name="numeromesa" class="form-control" 
                           value="{{ $orden->numeromesa }}" required min="1">
                </div>

                <div class="mb-3">
                    <label class="form-label">Cliente</label>
                    <select name="client" class="form-select" required>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->codigo }}" 
                                {{ $orden->client == $cliente->codigo ? 'selected' : '' }}>
                                {{ $cliente->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Método de Pago</label>
                    <select name="mpago" class="form-select" required>
                        @foreach($mediospago as $metodo)
                            <option value="{{ $metodo->codigo }}" 
                                {{ $orden->mpago == $metodo->codigo ? 'selected' : '' }}>
                                {{ $metodo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <h5>Productos</h5>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Subtotal</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orden->detalles as $detalle)
                            <tr>
                                <td>{{ $detalle->menu->nombre }}</td>
                                <td>
                                    <input type="number" name="cantidad[{{ $detalle->menu_id }}]" 
                                           value="{{ $detalle->cantidad }}" min="1" class="form-control form-control-sm">
                                </td>
                                <td>${{ number_format($detalle->menu->precio, 2) }}</td>
                                <td>${{ number_format($detalle->subtotal, 2) }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger btn-remove-item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-3">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Manejar eliminación de items
    $(document).on('click', '.btn-remove-item', function() {
        $(this).closest('tr').remove();
    });

    // Manejar envío del formulario
    $('#frmUpdateOrder').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        title: 'Éxito',
                        text: response.message,
                        icon: 'success'
                    }).then(() => {
                        $('#formModal').modal('hide');
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Error al actualizar la orden',
                    icon: 'error'
                });
            }
        });
    });
});
</script>