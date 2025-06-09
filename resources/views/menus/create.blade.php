
    <form action="/menu" method="POST" id="frmSaveData">
        <div class="row">
            <div class="col">
                <label>Nombre</label>
        <input type="text" name="nombre" class="form-control"><br>
        <span class="invalid-feedback d-block" key="nombre" role="alert">
            <strong class="mensaje"></strong>
        </span>
            </div>
            <div class="col">
                <label>Descripcion</label>
        <input type="text" name="descripcion" class="form-control"><br>
        <span class="invalid-feedback d-block" key="descripcion" role="alert">
            <strong class="mensaje"></strong>
        </span>
            </div>
            <div class="col">
                <label>Precio</label>
        <input type="text" name="precio" class="form-control"><br>
        <span class="invalid-feedback d-block" key="precio" role="alert">
            <strong class="mensaje"></strong>
        </span>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <label>Categoria</label>
                <select name="category" id="" class="form-select">
                    <option value="">--Seleccionar Categoria--</option>
                    @foreach ($categoria as $item)
                        <option value="{{$item->codigo}}">{{$item->nombre}}</option>
                    @endforeach
                </select>
                <span class="invalid-feedback d-block" key="category" role="alert">
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