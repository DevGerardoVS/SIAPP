<!--modal store Datos adicionales /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
<div class="modal fade bd-example-modal-lg" id="modalNuevoP" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header colorMorado">
                <h5 class="modal-title" id="staticBackdropLabel">{{ __('messages.alta_perfiles') }}</h5>
                <button type="button" class="btn-close" onclick="limpiarCampos()" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formRegistro" class="justify-content-md-center" action="{{ route('agregar_perfiles') }}"
                    method="POST">
                    @csrf
                    @method('POST')
                    <!--Seleccion de trimestre y año ///////////////////////////////////////////////////////////////////////////-->
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="nombre" class="form-label">{{ __('messages.nombre_perfil') }}: </label>
                            <input type="text" title="El campo función es requerido" id="nombre" name="nombre"
                                class="form-control" autocomplete="nombre" required>
                            <span id="nombre_error" class="invalid-feedback" role="alert"></span>
                        </div>
                        <div class="col-sm-4">
                            <label for="tipo_perfil" class="form-label">{{ __('messages.tipo_perfil') }}: </label>
                            <select class="form-control" style="width: 100%" id="tipo_perfil" name="tipo_perfil"
                                autocomplete="tipo_perfil" required readonly>
                                <option value="" class="ocultar">{{ __('messages.seleccionar_tipo') }}</option>
                                <option value="g" class="ocultar" selected>{{ __('messages.general') }}</option>
                            </select>
                            <span id="tipo_perfil_error" class="invalid-feedback" role="alert"></span>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-12">
                            <label for="permisos" id="lbl_modulo_id">{{ __('messages.permisos') }}: </label>
                            <select class="form-control select2-multiple " style="width: 100%" id="permisos"
                                name="permisos[]" autocomplete="permisos" multiple="multiple" required>
                            </select>
                            <span id="permisos_error" class="invalid-feedback" role="alert"></span>
                        </div>
                    </div>
                    <br>
                    <button type="button" onclick="limpiarCampos()" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('messages.cancelar') }}</button>
                    <button type="submit" id="btn_guardar"
                        class="btn colorMorado">{{ __('messages.guardar') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
