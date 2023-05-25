<div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#6A0F49 !important; color:whitesmoke">
                <h5 class="modal-title" id="exampleModalLabel">Agregar usuario</h5>
                <button id="cerrar" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <a aria-hidden="true" style="color: whitesmoke" onclick="dao.limpiarFormularioCrear()">&times;</a>
                </button>
            </div>
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <div class="modal-body">
                <form id="frm_create">
                    @csrf
                    <textarea type="text" value="0" id="id_user" name="id_user" style="display: none"></textarea>
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="form-group col-md-8">
                            <label class="control-label ">Nombre de usuario</label>
                            <input type="text" class="form-control" style="width: 100%" id="in_username"
                                name="username" placeholder="nombreUsuario...">
                                <span id="error_username" class="has-error"></span>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Nombre</label>
                            <input type="text" class="form-control" id="in_nombre" name="nombre"
                                placeholder="Alberto...">
                                <span id="error_in_nombre" class="has-error"></span>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label ">Primer apellido</label>
                            <input type="text" class="form-control" id="in_p_apellido" name="p_apellido"
                                placeholder="Sánchez...">
                                <span id="error_in_p_apellido" class="has-error"></span>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Segundo apellido</label>
                            <input type="text" class="form-control" id="in_s_apellido" name="s_apellido"
                                placeholder="López...">
                                <span id="error_in_s_apellido" class="has-error"></span>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Correo electrónico</label>
                            <input type="text" class="form-control" id="in_email" name="email"
                                placeholder="correo@dominio.com">
                                <span id="error_in_email" class="has-error"></span>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <span id="error_password" class="has-error"></span>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Confirmar
                                Contraseña</label>
                            <input type="password" class="form-control" id="in_pass_conf" name="in_pass_conf">
                            <span id="error_in_pass_conf" class="has-error"></span>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Celular</label>
                            <input type="text" class="form-control" id="in_celular" name="celular"
                                placeholder="44-30-29-02-22">
                                <span id="error_in_celular" class="has-error"></span>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Perfil</label>
                            <select name="id_grupo" id="id_grupo" class="form-control"></select>
                            <span id="error_id_grupo" class="has-error"></span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="dao.limpiarFormularioCrear()">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSave">Guardar</button>
            </div>
        </div>
    </div>
</div>
