@push('styles')

    <link rel="stylesheet" href="{{ asset('css/CargaMasiva.css') }}">

@endpush
<div class="modal fade bd-example-modal-lg" id="ModalCargaMasiva" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div  class="modal-header colorMorado">
          <h5  class="modal-title" id="staticBackdropLabel">{{ __('messages.carga_masiva_title') }}</h5>
          <button type="button" class="btn-close" onclick="limpiarCampos()" data-bs-dismiss="modal"
                  aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12 d-flex justify-content-center" >
              <div class="form-group">
                <label for="Instrucciones">
                  <span style="vertical-align: inherit;"><span style="vertical-align: inherit;"><b>Lea las instrucciones para asegurar el funcionamiento correcto del proceso:</b> </span></span>
                </label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-8">
              <div class="form-group">
                <label for="descripcion">
                  <span style="vertical-align: inherit;"><span style="vertical-align: inherit;"> <b>Asegurese de usar la plantilla </b> para el correcto funcionamiento de la carga masiva</span></span>
                </label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-3">
              <div class="form-group">
                <label for="instruccion 2">
                  <span style="vertical-align: inherit;"><span style="vertical-align: inherit;">Instruccion 2</span></span>
                  </label>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-sm-3">
              <div class="form-group">
                <label for="instruccion 2">
                  <span style="vertical-align: inherit;"><span style="vertical-align: inherit;">Instruccion 3</span></span>
                  </label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-3">
              <div class="form-group">
                <label for="instruccion 2">
                  <span style="vertical-align: inherit;"><span style="vertical-align: inherit;">Instruccion 2</span></span>
                  </label>
              </div>
            </div>
          </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <div class="d-flex justify-content-center">
                  <button style="width: 20%; border: 1px solid #555;" type="button" class="btn colorMorado" onclick="document.getElementById('excel').click()">Seleccionar archivo</button>
                  <input type="file"  id="excel" name="excel" style="display:none"
                   accept="text/plain, .csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, .xlsx, .xls, .csv">
                   <input style="width: 70%" type="text" readonly value="Sin archivos seleccionados">
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
         <button type="button" {{-- onclick="limpiarCampos()" --}} class="btn btn-secondary" data-bs-dismiss="modal">{{__("messages.cancelar")}}</button>
          <button type="button" name="aceptar" id="aceptar" class="btn colorMorado">{{__("messages.cargar_archivo")}}</button>
          </div>
        </div>
      </div>
  </div>



<script type="text/javascript">

</script>
