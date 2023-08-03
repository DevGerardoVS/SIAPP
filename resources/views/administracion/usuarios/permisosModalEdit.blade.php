<div class="modal fade bd-permisos-modal-lg permisosModalE" id="permisosModalE" tabindex="-1" role="dialog"
    aria-labelledby="permisosModalELabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#6A0F49 !important; color:whitesmoke">
                <h5 class="modal-title" id="permisosModalELabel">Editar permiso adicional</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <a aria-hidden="true" style="color: whitesmoke" onclick="dao.limpiarFormularioCrear()">&times;</a>
                </button>
            </div>
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <div class="modal-body">
                <form id="frm_permisosE">
                    <textarea name="id" id="id" style="display: none" value="0"></textarea>
                    @csrf
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="form-group col-md-8">
                            <label class="control-label ">Nombre de Usuario</label>
                            <select id="id_userPE" name='id_userPE'class="form-control">
                            </select>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div class="form-group col-md-8">
                            <label class="control-label ">Permisos</label>
                            <div class="col-md-12">
                                <div class="form-check form-check-inline ">
                                    <input class="form-check-input" type="checkbox" id="masiva" name="masiva" value="1">
                                    <label class="control-label " for="masiva">Carga masiva</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="obra" name="obra"  value="2">
                                    <label class="control-label " for="obra">Cargar obra</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="oficio"  name="oficio"  value="3">
                                    <label class="control-label " for="oficio">Descargar oficio</label>
                                </div>
                            </div>
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
                <button id="cerrarE" type="button" class="btn btn-secondary " data-dismiss="modal" aria-label="Close"
                    onclick="dao.limpiarFormularioCrear()">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSavePE">Guardar</button>
            </div>
        </div>

    </div>
</div>
</div>
