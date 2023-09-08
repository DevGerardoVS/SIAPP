<div class="modal fade bd-permisos-modal-lg permisosModal" id="permisosModal" tabindex="-1" role="dialog"
    aria-labelledby="permisosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#6A0F49 !important; color:whitesmoke">
                <h5 class="modal-title" id="permisosModalLabel">Agregar permiso adicional</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <a aria-hidden="true" style="color: whitesmoke" onclick="dao.limpiarFormularioCrear()">&times;</a>
                </button>
            </div>
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <div class="modal-body">
                <form id="frm_permisos">
                    <textarea name="id" id="id" style="display: none" value="0"></textarea>
                    @csrf
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="form-group col-md-4">
                            <label class="control-label ">Nombre de Usuario</label>
                            <select id="id_userP" name='id_userP'class="form-control" data-live-search="true">
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label ">Permiso</label>
                            <select id="id_permiso" name='id_permiso'class="form-control">
                            </select>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div class="form-group col-md-8">
                            <label class="control-label">Descripcion</label>
                            <textarea class="form-control" placeholder="agrerga una descripcion" id="descripcion" name="descripcion"
                                style="height: 100px"></textarea>
                        </div>
                    </div>

            </div>
            </form>
            <div class="modal-footer">
                <button id="cerrar" type="button" class="btn btn-secondary " data-dismiss="modal" aria-label="Close"
                    onclick="dao.limpiarFormularioCrear()">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSaveP">Guardar</button>
            </div>
        </div>

    </div>
</div>
</div>
