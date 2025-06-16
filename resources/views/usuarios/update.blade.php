
    <form action="/user/{{$user->id}}" method="POST" id="frmUpdateData">
        <div class="row">
            <div class="col">
                <label>Nombre de Usuario</label>
        <input type="text" name="name" value="{{$user->name}}" class="form-control"><br>
        <span class="invalid-feedback d-block" key="name" role="alert">
            <strong class="mensaje"></strong>
        </span>
            </div>
            <div class="col">
                <label>Correo</label>
        <input type="text" name="email" value="{{$user->email}}" class="form-control"><br>
        <span class="invalid-feedback d-block" key="email" role="alert">
            <strong class="mensaje"></strong>
        </span>
            </div>
            <div class="col">
                <label>Contrseña</label>
        <input type="text" name="password" value="{{$user->password}}" class="form-control"><br>
        <span class="invalid-feedback d-block" key="password" role="alert">
            <strong class="mensaje"></strong>
        </span>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <label>Roles</label>
                <select name="rol" id="" class="form-select">
                    <option value="">--Seleccionar Rol--</option>
                    @foreach ($roles as $item)
                        <option value="{{$item->codigo}}" {{ ($item->codigo==$user->rol)?'selected':'' }}>{{$item->nombre}}</option>
                    @endforeach
                </select>
                <span class="invalid-feedback d-block" key="rol" role="alert">
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