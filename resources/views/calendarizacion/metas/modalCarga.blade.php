<div class="modal fade carga-masiva" id="carga"tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style=" border-bottom: 5px solid #17a2b8;">
                <h5 class="modal-title col-11 text-center font-weight-bold">Carga masiva metas</h5>
                <button type="button" class="close closeModal" data-dismiss="modal" aria-label="Close" id="btnClose">
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
                        <li >Agregar el monto correspondiente en los campos <b>Operativo</b> y <b>Recursos Humanos</b>.</li>
                        <li >Los valores no especificados, en cero o con celdas vacías <b>no se guardaran</b>.</li>
                        <li >Para los fondos que no se muestran en la plantilla <b>agregar las filas necesarias</b>.</li>
                    </ul>
                    <a  class="btn-primary text-center" style="float:left; text-decoration:none; width:20%;"  href="{{route('ProyExcel')}}">
                        <i class="fa fa-download" aria-hidden="true"></i>
                        Descargar plantilla
                    </a>
                        <input name="cmFile" type="file" id="cmFile" name="cmFile" accept=".xlsx,.xlsm" class="border border-secondary rounded" placeholder="Archivo" required style="margin-top:5%; width : 100%;">
                    <br>
                </div>
                    <hr style="width: 98%; border: 1px solid gray; opacity:0.1;">
                    <div class="buttonContainer">
                        <button class="btn-primary" id="btnSaveM" style="width:25%;float:right;margin-right:22%;">
                            <i class="fa fa-save" aria-hidden="true"></i>
                            Guardar
                        </button>
                        <button type="button" class="btn-secondary colorMorado" id="btnCancelar" data-dismiss="modal" style="width:25%;float:left;margin-left:22%;">
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