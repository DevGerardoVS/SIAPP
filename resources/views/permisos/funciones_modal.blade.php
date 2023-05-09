<!--modal store Datos adicionales /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
<div class="modal fade bd-example-modal-xl" id="modalNuevoF" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header colorMorado">
                <h5 class="modal-title" id="staticBackdropLabel">{{ __('messages.alta_funciones') }}</h5>
                <button type="button" class="btn-close" onclick="limpiarCampos()" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formRegistro" class="justify-content-md-center" action="{{ route('agregar_funciones') }}"
                    method="POST">
                    @csrf
                    @method('POST')
                    <!--Seleccion de trimestre y año ///////////////////////////////////////////////////////////////////////////-->
                    <div class="row">
                        <div class="col-sm-3">
                            <label for="funcion" class="form-label">{{ __('messages.funcion') }}: </label>
                            <input type="text" title="El campo función es requerido" id="funcion" name="funcion"
                                class="form-control" autocomplete="funcion" required>
                            <span id="funcion_error" class="invalid-feedback" role="alert"></span>
                        </div>
                        <div class="col-sm-3">
                            <label for="ruta" class="form-label">{{ __('messages.ruta') }}: </label>
                            <input type="text" id="ruta" name="ruta" class="form-control"
                                autocomplete="ruta">
                            <span id="ruta_error" class="invalid-feedback" role="alert"></span>
                        </div>
                        <div class="col-sm-3">
                            <label for="icono" class="form-label">{{ __('messages.icono') }}: </label>
                            <input type="text" id="icono" name="icono" class="form-control"
                                autocomplete="icono">
                            <span id="icono_error" class="invalid-feedback" role="alert"></span>
                        </div>
                        <div class="col-sm-3">
                            <label for="modulo_id" class="form-label" id="lbl_modulo_id">{{ __('messages.modulo') }}:
                            </label>
                            <select class="form-control" id="modulo_id" name="modulo_id" autocomplete="modulo_id"
                                required>
                                <option value="">{{ __('messages.seleccionar_modulo') }}</option>
                            </select>
                            <span id="modulo_id_error" class="invalid-feedback" role="alert"></span>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-3">
                            <label for="orden" class="form-label">{{ __('messages.orden') }}: </label>
                            <input type="text" tabindex="-1" style="text-align:right;" value="0"
                                pattern="[0-9]+" title="El campo orden no debe ser mayor a 2 digitos enteros"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');"
                                id="orden" name="orden" class="form-control" maxlength="2" autocomplete="orden">
                            <span id="orden_error" class="invalid-feedback" role="alert"></span>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <h5 class="modal-title" id="lbl_acciones">{{ __('messages.acciones') }}</h5>
                        <div class="col-sm-1">
                            <input type="hidden" id="acciones" name="acciones" class="form-control">
                            <input type="hidden" id="acciones_delete" name="acciones_delete" class="form-control">
                        </div>
                        <div class="col-sm-3">
                            <label for="accion" class="form-label">{{ __('messages.accion') }}: </label>
                            <input type="text" id="accion" name="accion" class="form-control permisos"
                                autocomplete="accion">
                            <span id="accion_error" class="invalid-feedback" role="alert"></span>
                        </div>
                        <div class="col-sm-3">
                            <label for="descripcion" class="form-label">{{ __('messages.descripcion') }}: </label>
                            <input type="text" id="descripcion" name="descripcion" class="form-control permisos"
                                autocomplete="descripcion">
                            <span id="descripcion_error" class="invalid-feedback" role="alert"></span>
                        </div>
                        <div class="col-sm-3" style="padding-top: 2%;">
                            <button type="button" id="btn_agregar" class="btn btn-sm colorMorado" disabled><i
                                    class="fas fa-plus"></i></button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-1"></div>
                        <div class="col-sm-10">
                            <table id="datatable" class="table table-bordered table-striped text-center"
                                width="100%">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.accion') }}</th>
                                        <th>{{ __('messages.descripcion') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="dt_body"></tbody>
                            </table>
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
