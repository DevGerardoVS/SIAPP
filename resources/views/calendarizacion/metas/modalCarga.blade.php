<div class="modal fade carga-masiva" id="carga"tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style=" border-bottom: 5px solid #17a2b8;">
                <h5 class="modal-title col-11 text-center font-weight-bold">Carga masiva metas</h5>
                <button id="cerrar" type="button" class="close closeModal" data-dismiss="modal" aria-label="Close" id="btnClose">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formFile" >
                <br>
                <div class="wrap1">
                    <label><b>Lea las instrucciones para asegurar el funcionamiento correcto del proceso:</b></label>
                    <ul style="width:75%; float:left;">
                        <li ><b>Asegúrese de utilizar la plantilla</b> para el correcto funcionamiento de la carga masiva.</li>
                        <li >Debe llenar <b>todos</b> las columnas, para esto puede apoyarse con los catalogos que se encuentran en las otras pestañas.</li>
                        <li >El numero de beneficiarios debe ser <b>mayor a cero</b>.</li>
                        <li ><b>Agregar las filas necesarias</b>.</li>
                        <li >Solo se pueden llenar los meses que estan registrados en <b>calendarización de claves</b>.</li>
                        <li >Para el subprograma <b>UUU</b> se registran automaticamnete en el sistema el total y los meses predeterminados.</li>
                    </ul>
                    <button type="button" class="btn btn-outline-primary text-center" style="float:left;text-decoration:none; width:20%;" onclick="dao.getPlantillaCmUpp()"><i class="fa fa-check-square-o" aria-hidden="true"></i>&nbsp; Descargar plantilla</button>
                  <p><br><br></p>
                    <a type="button" class="btn btn-outline-success text-center" style="float:left;text-decoration:none; width:20%;" href="{{ route('Manual_Carga_Masiva_metas') }}"><i class="fa fa-check-square-o" aria-hidden="true"></i>&nbsp;Manual</a>

                        <input name="cmFile" type="file" id="cmFile" name="cmFile" accept=".xlsx,.xlsm" class="border border-secondary rounded" placeholder="Archivo" required style="margin-top:5%; width : 100%;">
                    <br>
                </div>
                    <hr style="width: 98%; border: 1px solid gray; opacity:0.1;">
                    <div class="buttonContainer">
                        <button class="btn-primary" id="btnSaveM" style="width:25%;float:right;margin-right:22%;">
                            <i class="fa fa-save" aria-hidden="true"></i>
                            Guardar
                        </button>
                        <button id="cerrar" type="button" class="btn-secondary colorMorado" id="btnCancelar" data-dismiss="modal" style="width:25%;float:left;margin-left:22%;">
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