<div class="modal fade" id="modalNuevaPoliza" tabindex="-1" role="dialog" aria-labelledby="modalNuevaPoliza" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header colorMorado">
                <h5 class="modal-title" id="staticBackdropLabel">{{ __('messages.alta_polizas') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formRegistro" class="justify-content-md-center" action="{{ route('agregar_poliza') }}" method="POST">
                    @csrf
                    @method('POST')
                    <input type="hidden" id="no_consesion" name="no_consesion" class="form-control">
                    <div class="row" style="padding-bottom: 1%;">
                        <div class="col-sm-5" style="text-align: left;" style="padding-left: 2%; padding-right: 2%">
                            <label for="aseguradora" class="form-label">{{ __('messages.empresa_aseguradora') }}</label>
                            <select name="aseguradora" id="aseguradora" class="form-control"></select>
                            <span id="aseguradora_error" class="invalid-feedback" role="alert"></span>
                        </div>
                        <div class="col-sm-5" style="text-align: left;" class="name_aseg_otro_div" id="name_aseg_otro_div" style="padding-left: 2%; padding-right: 2%" hidden>
                            <label for="name_aseg_otro" class="form-label">{{ __('messages.nombre_aseguradora') }}</label>
                            <input type="text" id="name_aseg_otro" name="name_aseg_otro" class="form-control" placeholder="{{ __('messages.nombre_aseguradora') }}">
                            <span id="name_aseg_otro_error" class="invalid-feedback" role="alert"></span>
                        </div>
                    </div>
                    <br>
                    <div class="row" style="padding-bottom: 1%;">
                        <div class="col-sm-4" style="text-align: left; padding-left: 2%">
                            <label for="no_poliza" class="form-label">{{ __('messages.no_poliza') }}:</label>
                            <input type="text" id="no_poliza" name="no_poliza" class="form-control" placeholder="{{ __('messages.no_poliza') }}" onkeypress="javascript: var code; if (!e) var e = window.event; if (e.keyCode) code = e.keyCode; else if (e.which) code = e.which; var character = String.fromCharCode(code); var AllowRegex = /^[a-zA-Z\-0-9]+$/; if (AllowRegex.test(character)) return true; return false;" pattern=".{3,}" required title="minimo 3 caracteres" maxlength="20">
                            <p id="no_poliza-invalido" class="hiddenElement invalid">El número de la póliza debe ser mayor a 3 caracteres y menor a 20 caracteres</p>
                            <span id="no_poliza_error" class="invalid-feedback" role="alert"></span>
                        </div>
                        <div class="col-sm-8" style="text-align: left; padding-right: 2%;">
                            <label for="fecha_vencimiento" class="form-label">{{ __('messages.fecha_vencimiento_poliza') }}:</label>
                            <input type="date" id="fecha_vencimiento" min="{{ date('Y-m-d') }}" name="fecha_vencimiento" class="form-control" placeholder="{{ __('messages.fecha_vencimiento_poliza') }}">
                            <span id="fecha_vencimiento_error" class="invalid-feedback" role="alert"></span>
                        </div>
                    </div>
                    <br>
                    <div class="row" style="padding-bottom: 1%;">
                        <div class="col-sm-12" style="text-align: left;">
                            <input type="file" class="dropify form-control" name="archivo" id="archivo" data-height="100" data-max-file-size="6M" required />
                            <span id="archivo_error" class="invalid-feedback" role="alert"></span>
                        </div>
                    </div>
                    <div class="row" style="padding-bottom: 1%;">
                        <div class="col-sm-5" style="text-align: left; padding-left: 2%">
                            <label for="email" class="form-label">{{ __('messages.correo') }}: </label>
                            <input type="email" id="email" name="email" class="form-control" maxlength="40" placeholder="Obligatorio" required>
                            <p id="email-invalido" class="hiddenElement invalid">Ingresa una dirección de email válida</p>
                            <span id="email_error" class="invalid-feedback" role="alert"></span>
                        </div>
                        <div class="col-sm-5" style="text-align: left; padding-left: 2%">
                            <label for="telefono" class="form-label">{{ __('messages.telefono') }}: </label>
                            <input type="text" pattern="([0-9]|[0-9]|[0-9]){10}" maxlength="10" placeholder="Opcional"
                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                                id="telefono" name="telefono" class="form-control">
                            <p id="telefono-invalido" class="hiddenElement invalid">El teléfono debe contener 10 números</p>
                            <span id="telefono_error" class="invalid-feedback" role="alert"></span>
                        </div>
                    </div>
                    <div class="row" style="padding-bottom: 1%;">
                        <div class="col-sm-12" style="text-align: left; padding-left: 2%">
                            <label for="observaciones" class="form-label">{{ __('messages.observaciones') }}: </label>
                            <textarea tabindex="20" id="observaciones" name="observaciones" class="form-control" autocomplete="observaciones"></textarea>
                        </div>
                    </div>
                    <div class="row" style="padding-bottom: 1%;">
                        <p><input type="checkbox" id="checkterms" name="checkterms"> En
                            este acto, <u>manifiesto que me hago sabedor que incurrir en
                                falsedad de manifestaciones, declaraciones o el
                                incumplimiento a
                                las disposiciones administrativas puede ser causa para la
                                revocación y/o nulidad de la concesión y/o actos
                                administrativos
                                que se deriven de la falta de cumplimiento, conforme a los
                                supuestos que señala el Código de Justicia Administrativa
                                del
                                Estado de Michoacán de Ocampo.</u></p>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close_modal" data-dismiss="modal">{{ __('messages.cancelar') }}</button>
                <button type="submit" id="btn_guardar" hidden class="btn colorMorado">{{ __('messages.guardar') }}</button>
            </div>
        </div>
    </div>
</div>