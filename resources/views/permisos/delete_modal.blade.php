<div class="modal fade bd-example-modal-md" tabindex="-1" role="dialog" id="modal_delete"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header colorMorado">
                <h5 class="modal-title" id="staticBackdropLabel">{{ __('messages.confirm_datos') }}</h5>
                <button type="button" class="close_modal btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" id="form_modal_delete" enctype="multipart/form-data">
                    @csrf
                    <div class="row" id="modal_message">
                        {{-- Aqui va el cuerpo del mensaje --}}
                    </div>
                    <br>
                    <button type="button" class="btn btn-secondary close_modal"
                        data-bs-dismiss="modal">{{ __('messages.cancelar') }}</button>
                    <button type="submit" id="confirmDelete"
                        class="btn colorMorado">{{ __('messages.confirm') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
