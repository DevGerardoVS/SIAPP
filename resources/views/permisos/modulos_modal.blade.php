<!--modal store Datos adicionales /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
<div class="modal fade bd-example-modal-lg" id="modalNuevoM" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header colorMorado">
                <h5 class="modal-title" id="staticBackdropLabel">{{ __('messages.alta_modulos') }}</h5>
                <button type="button" class="btn-close" onclick="limpiarCampos()" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formRegistro" class="justify-content-md-center" action="{{ route('agregar_modulos') }}"
                    method="POST">
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="modulo" class="form-label">{{ __('messages.modulo') }}: </label>
                            <input type="text" title="El campo módulo es requerido" id="modulo" name="modulo"
                                class="form-control" autocomplete="modulo" required>
                            <span id="modulo_error" class="invalid-feedback" role="alert"></span>
                        </div>
                        <div class="col-sm-4">
                            <label for="tipo" class="form-label">{{ __('messages.tipo') }}: </label>
                            <select class="form-control" id="tipo" name="tipo" autocomplete="tipo" required>
                                <option value="">{{ __('messages.seleccionar_tipo') }}</option>
                                <option value="mod">{{ __('messages.modulo') }}</option>
                                <option value="sub">{{ __('messages.submodulo') }}</option>
                            </select>
                            <span id="tipo_error" class="invalid-feedback" role="alert"></span>
                        </div>
                        <div class="col-sm-4">
                            <label for="modulo_id" class="form-label" id="lbl_modulo_id"
                                style="display:none;">{{ __('messages.modulo_padre') }}: </label>
                            <select class="form-control" style="display:none;" id="modulo_id" name="modulo_id"
                                autocomplete="modulo_id">
                                <option value="">{{ __('messages.seleccionar_modulo_padre') }}</option>
                            </select>
                            <span id="modulo_id_error" class="invalid-feedback" role="alert"></span>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="ruta" class="form-label">{{ __('messages.ruta') }}: </label>
                            <input type="text" id="ruta" name="ruta" class="form-control"
                                autocomplete="ruta">
                            <span id="ruta_error" class="invalid-feedback" role="alert"></span>
                        </div>
                        <div class="col-sm-4">
                            <label for="icono" class="form-label">{{ __('messages.icono') }}: </label>
                            <input type="text" id="icono" name="icono" class="form-control"
                                autocomplete="icono">
                            <span id="icono_error" class="invalid-feedback" role="alert"></span>
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
