<div class="modal fade" id="createGroup" tabindex="-1" role="dialog" aria-labelledby="createGroupLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header colorMorado">
                <h5 class="modal-title " id="createGroupLabel">Agregar Grupo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="dao.limpiar()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="frmCreate">
                    @csrf
                    <div class="form-group d-flex justify-content-center">
                        <div class="col-md-8">
                            <label class="control-label">Nombre Grupo</label>
                            <textarea type="text" id="id_user" name="id_user" style="display: none"></textarea>
                            <input type="text" class="form-control" id="nombre" name="nombre"
                                placeholder="Nombre de Grupo">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="cerrar" type="button" class="btn btn-secondary " data-dismiss="modal" aria-label="Close"  onclick="dao.limpiar()">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnSave" >Guardar</button>
            </div>
        </div>
    </div>
</div>
