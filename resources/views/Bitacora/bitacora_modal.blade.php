<!--modal store Descuento (Valores Virtuales) /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
<div class="modal fade bd-example-modal-lg" id="modalNuevoD" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header colorMorado">
                <h5 class="modal-title" id="staticBackdropLabel">{{__('messages.bitacora_accesos')}}</h5>
                <button tabindex="-1" type="button" class="btn-close" onclick="limpiarCampos()" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formRegistro" class="justify-content-md-center"  method="POST">
                    @csrf
                    @method('POST')
                    <!--Seleccion de trimestre y año ///////////////////////////////////////////////////////////////////////////-->
                    <div class="row">
                        <div class="col-sm-2">
                            <label for="anio" class="form-label">{{__('messages.anio')}}: </label>
                            <select class="form-control" tabindex="1" id="anio" name="anio" autocomplete="anio" required readonly >
                            </select>
                            <span id="anio_error" class="invalid-feedback" role="alert"></span>
                        </div>
                    </div> 
                    <div class="row">
    <div class="col-sm-4"></div>
    <div class="col-sm-3">
        <label for="label_anio_act" id="label_anio_act" class="form-label">2021</label>
    </div>
    <div class="col-sm-3">
        <label for="label_anio_ant" id="label_anio_ant" class="form-label">2020</label>
    </div>
</div>                   
                    <hr class="solid">                       
                    <button tabindex="22" type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('messages.cancelar')}}</button>
                    <button tabindex="21" type="submit" id="btn_guardar" class="btn colorMorado" >{{__('messages.guardar')}}</button>
                </form>
            </div>
        </div>
    </div>
</div>