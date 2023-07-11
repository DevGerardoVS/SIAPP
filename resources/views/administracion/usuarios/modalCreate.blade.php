<div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#6A0F49 !important; color:whitesmoke">
                <h5 class="modal-title" id="exampleModalLabel">Agregar usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <a aria-hidden="true" style="color: whitesmoke" onclick="dao.limpiarFormularioCrear()">&times;</a>
                </button>
            </div>
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <div class="modal-body">
                <form id="frm_create">
                    @csrf
                    <textarea type="text" value="null" id="id_user" name="id_user" style="display: none"></textarea>
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="form-group col-md-4">
                            <label class="control-label ">Nombre de usuario</label>
                            <input type="text" class="form-control" id="username" name="username"
                                placeholder="nombreUsuario...">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre"
                                placeholder="Alberto...">
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div class="form-group col-md-4">
                            <label class="control-label ">Primer apellido</label>
                            <input type="text" class="form-control" id="p_apellido" name="p_apellido"
                                placeholder="Sánchez...">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Segundo apellido</label>
                            <input type="text" class="form-control" id="s_apellido" name="s_apellido"
                                placeholder="López...">
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Correo electrónico</label>
                            <input type="text" class="form-control" id="email" name="email"
                                placeholder="correo@dominio.com" pattern=".+@globex\.com" size="30" required>
                            <span id="email-error"></span>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Celular</label>
                            <input type="text" class="form-control" id="in_celular" name="celular"
                                placeholder="44-30-29-02-22" required autocomplete="off"
                                onkeypress="return (event.charCode >= 48 && event.charCode <= 57)">
                            <span id="in_celular-error"></span>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Confirmar
                                Contraseña</label>
                            <input type="password" class="form-control" id="in_pass_conf" name="in_pass_conf">
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Grupo</label>
                            <select name="id_grupo" id="id_grupo" class="form-control">
                            </select>
                            <h6 id="label_idGrupo"></h6>
                        </div>
                        <div class="form-group col-md-4" id='divUpp' style="display: none">
                            <label class="control-label">UPP</label>
                            <select name="clv_upp" id="clv_upp" class="form-control" required>
                                <span id="clv_upp-error"></span>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="cerrar" type="button" class="btn btn-secondary " data-dismiss="modal"
                    aria-label="Close" onclick="dao.limpiarFormularioCrear()">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSave">Guardar</button>
            </div>
        </div>
    </div>
</div>
