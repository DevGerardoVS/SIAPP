<div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
	<div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">New message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="frm_create">
                    <input type="hidden" value="0" id="id_user">

                    <div class="form-group col-md-12">
                        <label class="control-label col-md-4">Nombre de usuario</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="in_username" name="username"
                                placeholder="nombreUsuario...">
                        </div>
                    </div>

                    <div class="form-group col-md-12">
                        <label class="control-label col-md-4">Contraseña</label>
                        <div class="col-md-8">
                            <input type="password" class="form-control" id="in_pass" name="password">
                        </div>
                    </div>

                    <div style="clear:both"></div>

                    <div class="form-group col-md-6">
                        <label class="control-label col-md-4">Confirmar Contraseña</label>
                        <div class="col-md-8">
                            <input type="password" class="form-control" id="in_pass_conf" name="password_confirmation">
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label class="control-label col-md-4">Nombre</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="in_nombre" name="nombre"
                                placeholder="Alberto...">
                        </div>
                    </div>

                    <div style="clear:both"></div>

                    <div class="form-group col-md-6">
                        <label class="control-label col-md-4">Primer Apellido</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="in_p_apellido" name="p_apellido"
                                placeholder="Sánchez...">
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label class="control-label col-md-4">Segundo Apellido</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="in_s_apellido" name="s_apellido"
                                placeholder="López...">
                        </div>
                    </div>

                    <div style="clear:both"></div>

                    <div class="form-group col-md-6">
                        <label class="control-label col-md-4">Correo Electrónico</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="in_email" name="email"
                                placeholder="correo@dominio.com">
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label class="control-label col-md-4">Celular</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="in_celular" name="celular"
                                placeholder="44-30-29-02-22">
                        </div>
                    </div>

                    <div style="clear:both"></div>



                    <div class="form-group col-md-6">
                        <label class="control-label col-md-4">Perfil</label>
                        <div class="col-md-8">
                            <select name="id_grupo" id="slc_perfil" class="form-control select2"></select>
                        </div>
                    </div>



                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-12">
                                <a class="btn btn-labeled btn-danger btnCancel" id="btnCancel" href="/adm-usuarios">
                                    <span class="btn-label"><i class="glyphicon glyphicon-arrow-left"></i></span>
                                    Regresar
                                </a>
                                <button class="btn btn-labeled btn-primary" type="button" id="btnSave">
                                    <span class="btn-label"><i class="glyphicon glyphicon-ok"></i></span>
                                    Guardar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Send message</button>
            </div>
        </div>
    </div>
</div>
<script src="/js/administracion/usuarios/init.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>

<script>
    //En las vistas solo se llaman las funciones del archivo init
    dao.getPerfil("");
    init.validateCreate($('#frm_create'));
</script>
