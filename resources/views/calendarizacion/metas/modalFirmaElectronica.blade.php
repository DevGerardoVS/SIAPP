<div class="modal fade bd-permisos-modal-lg" id="firmaModal" tabindex="-1" role="dialog"
    aria-labelledby="firmaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#6A0F49 !important; color:whitesmoke">
                <h5 class="modal-title" id="firmaTittleModal">Datos para E Firma</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <a aria-hidden="true" style="color: whitesmoke" onclick="dao.limpiarFormFirma()" >&times;</a>
                </button>
            </div>
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <div class="modal-body">
                <form id="frm_eFirma">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-4">
                            <input type="hidden" id="tipoReporte" name="tipoReporte" value=0>
                            <label class="control-label ">Certificado de Sello Digital (.cer)</label>
                            <input type="file" id="cer" name='cer'class="form-control" accept=".cer">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label ">Clave (.key)</label>
                            <input type="file" id="key" name='key'class="form-control" accept=".key">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="control-label">Contraseña FIEL</label>
                            <input type="password" id="pass" name="pass" class="form-control">
                        </div>
                    </div>

            </div>
            </form>
            <div class="modal-footer">
                <button id="cerrarEfirma" type="button" class="btn btn-secondary " data-dismiss="firmaModal" aria-label="Close" onclick="dao.limpiarFormFirma()"
                >Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSaveFirma">Guardar</button>
            </div>
        </div>

    </div>
</div>
</div>