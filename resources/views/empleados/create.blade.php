<form action="/employee" method="POST" id="frmEmployee">
    @csrf
    <div class="row">
        <!-- Datos del Empleado -->
        <div class="col-md-6">
            <h5 class="mb-3">Datos del Empleado</h5>
            <div class="mb-3">
                <label class="form-label">Nombre:</label>
                <input type="text" name="nombre" class="form-control" required>
                <span class="invalid-feedback d-block" key="nombre" role="alert">
                    <strong class="mensaje"></strong>
                </span>
            </div>
            <div class="mb-3">
                <label class="form-label">Apellido:</label>
                <input type="text" name="apellido" class="form-control" required>
                <span class="invalid-feedback d-block" key="apellido" role="alert">
                    <strong class="mensaje"></strong>
                </span>
            </div>
            <div class="mb-3">
                <label class="form-label">Teléfono:</label>
                <input type="text" name="telefono" class="form-control">
                <span class="invalid-feedback d-block" key="telefono" role="alert">
                    <strong class="mensaje"></strong>
                </span>
            </div>
        </div>

        <!-- Datos del Usuario -->
        <input type="hidden" name="create_user" value="1">
        <div class="col-md-6">
            <h5 class="mb-3">Datos del Usuario</h5>
            <div class="mb-3">
                <label class="form-label">Nombre de Usuario:</label>
                <input type="text" name="name" class="form-control" required>
                <span class="invalid-feedback d-block" key="name" role="alert">
                    <strong class="mensaje"></strong>
                </span>
            </div>
            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control" required>
                <span class="invalid-feedback d-block" key="email" role="alert">
                    <strong class="mensaje"></strong>
                </span>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña:</label>
                <input type="password" name="password" class="form-control" required>
                <span class="invalid-feedback d-block" key="password" role="alert">
                    <strong class="mensaje"></strong>
                </span>
            </div>
            <div class="col-6">
                <label>Rol</label>
                <select name="rol" id="" class="form-select">
                    <option value="">--Seleccionar Rol--</option>
                    @foreach ($roles as $item)
                        <option value="{{$item->codigo}}">{{$item->nombre}}</option>
                    @endforeach
                </select>
                <span class="invalid-feedback d-block" key="rol" role="alert">
            <strong class="mensaje"></strong>
        </span>
            </div>
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
    </div>
</form>