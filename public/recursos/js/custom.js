$(document).ready(function () {
    // Evento: Cargar formulario para crear registro
    $(document).on('click', '#addForm', function () {
        let url = $(this).attr('path');
        $("#modalLabel").text("Creando registros");
        $("#loadForm").load(url);
        $("#myModal").modal('show');
    });
    // Evento: Guardar nuevo registro
    $(document).on('submit','#frmSaveData',function(e){
        e.preventDefault();
        frm=$(this);
        url=frm.attr('action');
        token=$('input[name="_token"]').val();
        save_data(url, frm, token);
    });

    // Evento: Cargar formulario de edición
    $(document).on('click', '.edit', function () {
        let url = $(this).attr('path');
        $("#modalLabel").text("Actualizando registro");
        $("#loadForm").load(url);
        $("#myModal").modal('show');
    });

    // Evento: Guardar cambios de edición
    $(document).on('submit', '#frmUpdateData', function (e) {
        e.preventDefault();
        let frm = $(this);
        let url = frm.attr('action');
        let token = $('input[name="_token"]').val();
        save_update(url, frm, token);
    });

    // Evento: Eliminar registro
    $(document).on('click', '.delete', function (e) {
        e.preventDefault();
        let url = $(this).attr('path');
        let token = $('input[name="_token"]').val();

        Swal.fire({
            title: "¿Estás Seguro?",
            text: "Si continúas, no podrás revertir la acción",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, eliminar"
        }).then((result) => {
            if (result.isConfirmed) {
                delete_data(url, token);
            }
        });
    });
});
// Evento: Guardar nuevo registro ORDEN Y DETALLE ORDEN
    document.getElementById('frmSaveDataOrden').addEventListener('submit', async function (e) {
        e.preventDefault();
        
        // Validar que haya ítems en el carrito
        if (carrito.length === 0) {
            alert('¡Agrega al menos un producto al carrito!');
            return;
        }

        // Validar selección de cliente y método de pago
        const clienteSelect = document.getElementById('clienteSelect');
        const metodoPagoSelect = document.querySelector('[name="mpago"]');
        
        if (!clienteSelect.value) {
            alert('¡Selecciona un cliente!');
            return;
        }
        
        if (!metodoPagoSelect.value) {
            alert('¡Selecciona un método de pago!');
            return;
        }

        // Preparar datos para enviar
        const formData = new FormData(this);
        formData.set('detalle', JSON.stringify(carrito)); // Actualizar carrito

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Error al guardar la orden');
            }

            // Éxito: Mostrar mensaje y recargar
            Swal.fire('¡Éxito!', 'Orden guardada correctamente', 'success')
                .then(() => {
                    window.location.reload(); // Recargar para limpiar el carrito
                });

        } catch (error) {
            Swal.fire('Error', error.message, 'error');
            console.error('Error:', error);
        }
    });

