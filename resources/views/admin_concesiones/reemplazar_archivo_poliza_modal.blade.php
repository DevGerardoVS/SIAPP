<div class="modal fade" id="modalReplaceFilePoliza" tabindex="-1" role="dialog" aria-labelledby="modalReplaceFilePoliza" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header colorMorado">
                <h5 class="modal-title" id="staticBackdropLabel">{{ __('messages.replace_file_poliza') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formReplaceFilePoliza" class="justify-content-md-center" action="{{ route('reemplazar_archivo_poliza') }}" method="POST">
                    @csrf
                    @method('POST')
                    <input type="hidden" id="id_poliza" name="id_poliza" class="form-control">
                    <div class="row" style="padding-bottom: 1%;">
                        <div class="col-sm-12" style="text-align: left;">
                            <input type="file" class="dropify form-control" name="archivo" id="archivo" data-height="100" data-max-file-size="6M" required />
                            <span id="archivo_error" class="invalid-feedback" role="alert"></span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close_modal" data-dismiss="modal">{{ __('messages.cancelar') }}</button>
                <button type="submit" id="btn_guardar" class="btn colorMorado">{{ __('messages.guardar') }}</button>
            </div>
        </div>
    </div>
</div>