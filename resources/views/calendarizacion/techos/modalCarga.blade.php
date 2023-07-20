<div class="modal fade carga-masiva" id="carga" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style=" border-bottom: 5px solid #17a2b8;">
                <h5 class="modal-title col-11 text-center font-weight-bold">Carga masiva techos financieros</h5>
                <button type="button" class="close closeModal" data-dismiss="modal" aria-label="Close" id="btnClose">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="importPlantilla">
                    <br>
                    <div class="wrap1">
                        <label><b>Lea las instrucciones para asegurar el funcionamiento correcto del proceso:</b></label>
                        <ul style="width:75%; float:left;">
                            <li>1.- <b>Descargue y utilice la plantilla</b> para el correcto funcionamiento de la carga masiva.</li>
                            <li>2.- Llenar la plantilla con el presupuesto <b>Operativo</b> y <b>Recursos Humanos</b>.</li>
                            <li>3.- <b>Agregar las filas necesarias</b> para los fondos diferentes a la plantilla.</li>
                            <li>4.- Los valores no especificados, en cero o con celdas vacías <b>no se guardarán</b>.</li>
                        </ul>
                        <a class="btn-primary text-center" style="float:left; text-decoration:none; width:20%;"
                            href="{{ route('exportPlantilla') }}">
                            <i class="fa fa-download" aria-hidden="true"></i>
                            Descargar plantilla
                        </a>
                        <label ><b style="color:red;">Nota:</b> Si ya existe presupuestos para el ejercicio a registrar estos se <b>reemplazarán</b>.</label>
                        <input name="cmFile" type="file" id="cmFile" name="cmFile" accept=".xlsx"
                            class="border border-secondary rounded" placeholder="Archivo" required
                            style="margin-top:5%; width : 100%;">
                    </div>
                    <hr style="width: 98%; border: 1px solid gray; opacity:0.1;">
                    <div class="buttonContainer">
                        <button class="btn-primary" type="button" id="btnSaveM" style="width:25%;float:right;margin-right:22%;">
                            <i class="fa fa-save" aria-hidden="true"></i>
                            Guardar
                        </button>
                        <button type="button" class="btn-secondary colorMorado" id="btnCancelar" data-dismiss="modal"
                            style="width:25%;float:left;margin-left:22%;">
                            <i class="fa fa-times" aria-hidden="true"></i>
                            Cancelar
                        </button>
                    </div>
                    <br>
                </form>
            </div>
        </div>
    </div>
</div>
