
    <form action="/client" method="POST" id="frmSaveData">
        <div class="row">
            <div class="col">
                <label>Nombre:</label>
        <input type="text" name="nombre" class="form-control"><br>
        <span class="invalid-feedback d-block" key="nombre" role="alert">
            <strong class="mensaje"></strong>
        </span>
            </div>
            <div class="col">
                <label>Apellido:</label>
        <input type="text" name="apellido" class="form-control"><br>
        <span class="invalid-feedback d-block" key="apellido" role="alert">
            <strong class="mensaje"></strong>
        </span>
            </div>
            <div class="col">
                <label>Correo:</label>
        <input type="text" name="correo" class="form-control"><br>
        <span class="invalid-feedback d-block" key="correo" role="alert">
            <strong class="mensaje"></strong>
        </span>
            </div>
        </div>
        <hr>
        <div class="row text-center">
            <div class="col">
                <button type="submit" class="btn btn-lg btn-success">
                    Guardar
                </button>
                <button type="button" class="btn btn-lg btn-danger" data-bs-dismiss="modal">
                    Cancelar
                </button>
            </div>
            <div class="col">

            </div>
        </div>
    </form>