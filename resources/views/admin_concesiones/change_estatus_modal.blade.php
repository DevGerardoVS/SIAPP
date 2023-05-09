<div class="modal fade bd-example-modal-md" tabindex="-1" role="dialog" id="modal_change_estatus" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <form method="POST" id="form_modal_change_estatus" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="id_poliza" name="id_poliza" class="form-control">
                <div class="modal-header colorMorado">
                    <h5 class="modal-title" id="title_modal">{{ __('messages.cambio_estatus_poliza') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="estatus" class="col-form-label text-md-end">{{ __('messages.estatus') }}:</label>
                            <select name="estatus" id="estatus" class="form-control" required>
                                <option value="">{{ __('messages.seleccionar_estatus') }}</option>
                                <option value="1">{{ __('messages.inconsistente') }}</option>
                                <option value="2">{{ __('messages.revisada') }}</option>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row" id="div_obs" style="display: none;">
                        <div class="col-md-12">
                            <label for="observaciones" class="form-label">{{__('messages.observaciones')}}: </label>
                            <textarea tabindex="20" id="observaciones" name="observaciones" class="form-control" autocomplete="observaciones"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button tabindex="22" type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('messages.cancelar')}}</button>
                    <button tabindex="21" type="submit" id="btn_guardar" class="btn colorMorado" disabled="disabled">{{__('messages.guardar')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>