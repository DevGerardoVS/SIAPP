<div class="modal fade bd-example-modal-lg" id="ModalCargaMasiva" name="ModalCargaMasiva" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header colorMorado">
                <h5 class="modal-title" id="staticBackdropLabel">{{ __('messages.carga_masiva_title') }}</h5>
                <button type="button" class="btn-close" onclick="limpiarCampos()" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="{{ route('load_data_plantilla') }}" id="Formmodal" method="POST"
                enctype="multipart/form-data">

                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-center">
                            <div class="form-group">
                                <label for="Instrucciones">
                                    <span style="vertical-align: inherit;"><span style="vertical-align: inherit;"><b>Lea
                                                las instrucciones para asegurar el funcionamiento correcto del
                                                proceso:</b> </span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="descripcion">
                                    <span style="vertical-align: inherit;"><span style="vertical-align: inherit;">
                                            <b>Asegurese de usar la plantilla </b> para el correcto funcionamiento de la
                                            carga masiva</span></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <a href="{{ route('getplantilla') }}" type="button" class="btn colorMorado"
                                    id="downloadbtn" name="downloadbtn">
                                    <i class="fa fa-download" style="color: #dfdfdf"></i>
                                    {{ __('messages.descargar_plantilla') }}</a>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="form-group">
                                <label for="instruccion 2">
                                    <span style="vertical-align: inherit;"><span
                                            style="vertical-align: inherit;">Los datos de claves presupuestarias guardados no confirmados <b>seran borrados</b></span></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <a href="{{ route('getManualCMC') }}" type="button" class="btn colorMorado"
                                    id="downloadbtn" name="downloadbtn">
                                    <i class="fa fa-download" style="color: #dfdfdf"></i>
                                    {{ __('messages.descargar_manual') }}</a>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-8">
                            <div class="form-group">
                                <label for="instruccion 3">
                                    <span style="vertical-align: inherit;"><span style="vertical-align: inherit;">Los
                                            valores no especificados o con celdas vacias <b> quedáran en
                                                0</b></span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="instruccion 4">
                                    <span style="vertical-align: inherit;"><span style="vertical-align: inherit;">El
                                        archivo cargado debe ser un documento <b>.xlsx</b></span></span>
                                    </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row d-flex justify-content-center" style="font-weight: bold;">Atención se borraran los registros no confirmados</div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="d-flex justify-content-center">
                                <button style="width: 20%; border: 1px solid #555;" type="button"
                                    class="btn colorMorado"
                                    onclick="document.getElementById('file').click()">Seleccionar archivo</button>
                                <input type="file" id="file" name="file" style="display:none"
                                    accept=" .xlsx ">
                                <input id="file_label" style="width: 70%" type="text" readonly
                                    value="Sin archivos seleccionados">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer-carga">
                    <button type="button" {{-- onclick="limpiarCampos()" --}} class="btn btn-secondary "
                        data-bs-dismiss="modal">{{ __('messages.cancelar') }}</button>
                    <button type="submit" name="aceptar" id="aceptar" class="btn colorMorado">
                        <i class="fa fa-upload" style="color: #dfdfdf"></i>
                        {{ __('messages.cargar_archivo') }}</button>
                </div>
        </div>
        </form>
    </div>
</div>



<script type="text/javascript">
    $(document).on("ajaxStart", function() {
        $("#loading").show();
    });
    //mostrar campos una vez selecionado el municipio
    $('#file').change(function(e) {
        e.preventDefault();
        $("#ModalCargaMasiva").find("#file_label").val($('#file')[0].files[0].name)
    });

    $('#aceptar').click(function(e) {
        e.preventDefault();
        form = $('#ModalCargaMasiva').find("#Formmodal");
        Swal.fire({
            title: '{{ __('messages.msg_cargando_datos') }}',
            html: ' <b></b>',
            allowOutsideClick: false,
            timer: 2000000,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();

            },

        })
        form.submit();

    })
</script>
