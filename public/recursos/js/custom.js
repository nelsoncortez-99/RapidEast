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

    

    function saveEmployeeSimple(form) {
        const formData = $(form).serialize();
        
        $.post($(form).attr('action'), formData)
         .done(function(response) {
            $('#myModal').modal('hide');
            if(typeof dt !== 'undefined') dt.ajax.reload();
            Swal.fire('Éxito', 'Empleado guardado', 'success');
         })
         .fail(function(xhr) {
            if(xhr.status === 422) {
                // Mostrar errores de validación
                $.each(xhr.responseJSON.errors, function(key, error) {
                    $(`[name="${key}"]`).addClass('is-invalid');
                    $(`[key="${key}"] strong`).text(error);
                });
            } else {
                Swal.fire('Error', xhr.responseJSON.message || 'Error al guardar', 'error');
            }
         });
    }

    // Asignar evento solo al formulario de empleado
    $(document).on('submit', '#frmEmployee', function(e) {
        e.preventDefault();
        saveEmployeeSimple(this);
    });

    // Evento: Guardar nuevo registro ORDEN Y DETALLE ORDEN
    document.getElementById('frmSaveDataOrden')?.addEventListener('submit', async function (e) {
        e.preventDefault();
        
        if (carrito.length === 0) {
            alert('¡Agrega al menos un producto al carrito!');
            return;
        }

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

        const formData = new FormData(this);
        formData.set('detalle', JSON.stringify(carrito));

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

            Swal.fire('¡Éxito!', 'Orden guardada correctamente', 'success')
                .then(() => {
                    window.location.reload();
                });

        } catch (error) {
            Swal.fire('Error', error.message, 'error');
            console.error('Error:', error);
        }
    });
    const employeeForm = document.getElementById('frmSaveData');
    
    if (employeeForm) {
        employeeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);

            fetch(form.action, {
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
                if (data.success) {
                    // Cierra solo el modal de empleados (sin afectar otros modales)
                    const employeeModal = document.getElementById('employeeModal'); // Ajusta este ID según tu HTML
                    if (employeeModal) {
                        bootstrap.Modal.getInstance(employeeModal).hide();
                    }
                    
                    // Recarga suave (opcional)
                    if (window.tableEmployees) {
                        window.tableEmployees.ajax.reload(); // Si usas DataTables
                    } else {
                        window.location.reload();
                    }
                }
            })
            .catch(async (error) => {
                const errorData = await error.json();
                
                // Limpia solo errores de este formulario
                form.querySelectorAll('.invalid-feedback strong').forEach(el => {
                    el.textContent = '';
                });

                if (errorData.errors) {
                    Object.entries(errorData.errors).forEach(([key, messages]) => {
                        const errorElement = form.querySelector(`.invalid-feedback[key="${key}"] strong`);
                        if (errorElement) {
                            errorElement.textContent = messages.join(' ');
                        }
                    });
                }
            });
        });
    }

$(document).ready(function() {
    // Configuración global de AJAX para enviar el token CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Función para completar órdenes (versión mejorada)
    $(document).on('click', '.complete-order', function(e) {
    e.preventDefault();
    const $btn = $(this);
    const orderId = $btn.data('order-id');
    
    Swal.fire({
        title: '¿Completar orden?',
        text: "¿Estás seguro de marcar esta orden como completada?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, completar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            
            $.ajax({
                url: `/order/${orderId}/complete`,
                type: 'POST',
                data: {
                    _method: 'PUT'
                },
                success: function(data) {
                    if(data.success) {
                        // Actualizar UI
                        const $card = $(`[data-order-id="${orderId}"] .card`);
                        const $status = $(`#estado-${orderId}`);
                        const $badge = $(`#state-badge-${orderId}`);
                        
                        $card.removeClass('bg-light').addClass('bg-success text-white');
                        $status.text(data.new_state);
                        $badge.removeClass('bg-warning').addClass('bg-success').text(data.new_state);
                        $btn.remove();
                        
                        Swal.fire('¡Éxito!', data.message, 'success');
                    } else {
                        Swal.fire('Error', data.message, 'error');
                        $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Completar');
                    }
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON?.message || 'Error en el servidor';
                    Swal.fire('Error', errorMsg, 'error');
                    $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Completar');
                }
            });
        }
    });
});

    // Función para actualizar la interfaz
    function updateOrderUI(orderId) {
        const $card = $(`[data-order-id="${orderId}"] .card`);
        const $statusText = $(`#estado-${orderId}`);
        const $badge = $(`#state-badge-${orderId}`);
        
        // Actualizar UI
        $card.removeClass('bg-light').addClass('bg-success text-white');
        $statusText.text('Completado');
        $badge.removeClass('bg-warning').addClass('bg-success').text('Completado');
        
        // Eliminar botón
        $(`[data-order-id="${orderId}"] .complete-order`).remove();
    }
});

    // Evento: Abrir modal para nueva orden
    $(document).on('click', '#addOrder', function() {
        const url = $(this).data('url') || $(this).attr('path');
        $("#orderModalLabel").text("Nueva Orden");
        $("#orderModalBody").load(url, function() {
            if (typeof initCart === 'function') {
                initCart();
            }
        });
        $("#orderModal").modal('show');
    });

    // Evento: Abrir modal para editar orden
    $(document).on('click', '.edit-order', function(e) {
        e.preventDefault();
        const url = $(this).data('url') || $(this).attr('href');
        const modal = $('#formModal');
        
        $('#formModalBody').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando formulario...</p>
            </div>
        `);
        
        $('#formModalLabel').text('Editar Orden');
        
        $.get(url)
            .done(function(response) {
                $('#formModalBody').html(response);
            })
            .fail(function(xhr) {
                $('#formModalBody').html(`
                    <div class="alert alert-danger">
                        Error al cargar el formulario: ${xhr.status} ${xhr.statusText}
                    </div>
                `);
            });
        
        modal.modal('show');
    });

    // Evento: Eliminar orden
    $(document).on('click', '.delete-order', function(e) {
        e.preventDefault();
        const orderId = $(this).closest('[data-order-id]').data('order-id') || 
                       $(this).data('order-id');
        
        Swal.fire({
            title: '¿Eliminar orden?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const url = $(this).data('url') || `/order/${orderId}`;
                
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        if (data.success) {
                            $(`[data-order-id="${orderId}"]`).closest('.col-md-4').remove();
                            Swal.fire('Éxito', data.message, 'success');
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Error al eliminar', 'error');
                    }
                });
            }
        });
    });

    // Evento: Guardar orden editada
    $(document).on('submit', '#frmUpdateOrder', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalBtnText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Actualizando...
        `);

        const formData = new FormData(this);
        
        if (typeof carrito !== 'undefined') {
            formData.append('detalle', JSON.stringify(carrito));
        }

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .then(response => response.ok ? response.json() : Promise.reject(response))
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: data.message,
                willClose: () => {
                    window.location.href = data.redirect || window.location.href;
                }
            });
        })
        .catch(error => {
            error.json().then(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: err.message || 'Error al actualizar'
                }); 
            });
        })
        .finally(() => {
            submitBtn.prop('disabled', false).html(originalBtnText);
        });
    });
    
});