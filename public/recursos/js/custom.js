$(document).ready(function () {
    //evento cargar formulario crear registro
    $(document).on('click','#addForm',function(){
        let url=$(this).attr('path');
        $("#modalLabel").text("Creando registros");
        $("#loadForm").load(url);
    });

    //evento crear registro
    $(document).on('submit','#frmSaveData',function(e){
        e.preventDefault();
        frm=$(this);
        url=frm.attr('action');
        token=$('input[name="_token"]').val();
        save_data(url, frm, token);
    });

    //evento cargar formulario editar registro
    $(document).on('click','.edit',function(){
        let url=$(this).attr('path');
        $("#modalLabel").text("Actualizando registro");
        $("#loadForm").load(url);
        $("#myModal").modal('show');
    });

    //evento para guardar editar registros
    $(document).on('submit','#frmUpdateData',function(e){
        e.preventDefault();
        frm=$(this);
        url=frm.attr('action');
        token=$('input[name="_token"]').val();
        save_update(url, frm, token);
    });

    //evento eliminar
    $(document).on('click','.delete',function(){
        event.preventDefault();
        let url=$(this).attr('path');
        token=$('input[name="_token"]').val();
        Swal.fire({
            title: "¿Estás Seguro?",
            text: "Si continúas, no podrás revertir a acción",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Si, eliminar"
            }).then((result) => {
            if (result.isConfirmed) {
                //peticion eliminar registro
                delete_data(url, token);
            }
            });
        
    });
});