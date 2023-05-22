@extends('layouts.app')
@section('content')
    <div class="container">
        <h2 class="d-flex justify-content-center">Datos para crear usuario</h2>
        <br>
        <br>
        <form id="frm_create">
            <input type="hidden" value="0" id="id_user">
            @csrf
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <label class="d-flex justify-content-center">Nombre de usuario</label>
                    <input type="text" class="form-control form-group" style="width: 100%" id="in_username"
                        name="username" placeholder="nombreUsuario...">
                </div>
                <div class="col-md-2"></div>
                <div class="col-md-2"></div>
                <div class="form-group col-md-4">
                    <label class="control-label">Nombre</label>
                    <input type="text" class="form-control" id="in_nombre" name="nombre" placeholder="Alberto...">
                </div>
                <div class="form-group col-md-4">
                    <label class="control-label ">Primer Apellido</label>
                    <input type="text" class="form-control" id="in_p_apellido" name="p_apellido"
                        placeholder="Sánchez...">
                </div>
                <div class="col-md-2"></div>
                <div class="col-md-2"></div>
                <div class="form-group col-md-4">
                    <label class="control-label">Segundo Apellido</label>
                    <input type="text" class="form-control" id="in_s_apellido" name="s_apellido" placeholder="López...">
                </div>
                <div class="form-group col-md-4">
                    <label class="control-label">Correo Electrónico</label>
                    <input type="text" class="form-control" id="in_email" name="email"
                        placeholder="correo@dominio.com">
                </div>
                <div class="col-md-2"></div>
                <div class="col-md-2"></div>
                <div class="form-group col-md-4">
                    <label class="control-label">Contraseña</label>
                    <input type="password" class="form-control" id="in_pass" name="password">
                </div>
                <div class="form-group col-md-4">
                    <label class="control-label">Confirmar
                        Contraseña</label>
                    <input type="password" class="form-control" id="in_pass_conf" name="password_confirmation">
                </div>
                <div class="col-md-2"></div>
                <div class="col-md-2"></div>
                <div class="form-group col-md-4">
                    <label class="control-label">Celular</label>
                    <input type="text" class="form-control" id="in_celular" name="celular" placeholder="44-30-29-02-22">
                </div>
                <div class="form-group col-md-4">
                    <label class="control-label">Perfil</label>
                    <select name="id_grupo" id="slc_perfil" class="form-control"></select>
                </div>
                <div class="col-md-2"></div>
                <div class="col-md-8"></div>
                <div class="col-md-4">
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

            
        </form>
    </div>
@endsection
<script src="/js/administracion/usuarios/init.js"></script>
<script>
    //En las vistas solo se llaman las funciones del archivo init
    dao.getPerfil("");
    init.validateCreate($('#frm_create'));
</script>
