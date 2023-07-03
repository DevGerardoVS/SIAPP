<div class="modal fade createPermiso" id="createPermiso" tabindex="-1" role="dialog" aria-labelledby="createPermisoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header colorMorado">
                <h5 class="modal-title " id="createPermisoLabel">Agregar permiso adicional</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="dao.limpiar()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="permisos_frm">
                    @csrf
                    <div class="form-group d-flex justify-content-center">
                        <div class="col-md-8">
                            <label class="control-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre"
                                placeholder="Nombre">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="cerrar" type="button" class="btn btn-secondary " data-dismiss="modal" aria-label="Close"  onclick="dao.limpiar()">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnSaveCreate" >Guardar</button>
            </div>
        </div>
    </div>
</div>
