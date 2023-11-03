<div class="modal fade bd-example-modal-xl" id="modalPresupuesto" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
          <div class="modal-header" style=" border-bottom: 5px solid #17a2b8;">
              <h5 class="modal-title col-11 text-center font-weight-bold">Detalle presupuesto por fondo</h5>
              <button type="button" class="close closeModal" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
          <h6 id='titleModalpresupuesto' class="modal-title col-11 text-center font-weight-bold text-secondary"></h6>
          <br>
            <div class="table-responsive" style='overflow:auto; width:100%;position:relative;'>
                <table id="tblPresupuestos" class="table table-hover table-striped" style='overflow:auto; width:100%;position:relative; display:none;'>
                    <thead>
                        <tr>
                            <th class="colorMorado">ID Fondo</th>
                            <th class="colorMorado">Fondo</th>
                            <th class="colorMorado">Operativo</th>
                            <th class="colorMorado">Recursos Humanos</th>
                            <th class="colorMorado">Techo presupuestal</th>
                            <th class="colorMorado">Calendarizado</th>
                            <th class="colorMorado">Disponible</th>
                            <th class="colorMorado">Ejercicio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tbody>
                        </tbody>
                        <tfoot >
                            <tr>
                                <th class="colorMorado" colspan="2"> Total:</th>
                                <th class="colorMorado"></th>
                                <th class="colorMorado"></th>
                                <th class="colorMorado"></th>
                                <th class="colorMorado"></th>
                                <th class="colorMorado"></th>
                                <th class="colorMorado"></th>
                            </tr>
                        </tfoot>
                    </tbody>
                </table>
                <table id="tablaUpps" class="table table-hover table-striped" style='overflow:auto; width:100%;position:relative; display:none;'>
                    <thead>
                        <tr>
                            <th class="colorMorado">ID Fondo</th>
                            <th class="colorMorado">Fondo</th>
                            <th class="colorMorado">Operativo</th>
                            <th class="colorMorado">Techo presupuestal</th>
                            <th class="colorMorado">Calendarizado</th>
                            <th class="colorMorado">Disponible</th>
                            <th class="colorMorado">Ejercicio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tbody>
                        </tbody>
                        <tfoot >
                            <tr>
                                <th class="colorMorado" colspan="2"> Total:</th>
                                <th class="colorMorado"></th>
                                <th class="colorMorado"></th>
                                <th class="colorMorado"></th>
                                <th class="colorMorado"></th>
                                <th class="colorMorado"></th>
                            </tr>
                        </tfoot>
                    </tbody>
                </table>
                <table id="tablaDelegacion" class="table table-hover table-striped" style='overflow:auto; width:100%;position:relative; display:none;'>
                    <thead>
                        <tr>
                            <th class="colorMorado">ID Fondo</th>
                            <th class="colorMorado">Fondo</th>
                            <th class="colorMorado">Recursos Humanos</th>
                            <th class="colorMorado">Techo presupuestal</th>
                            <th class="colorMorado">Calendarizado</th>
                            <th class="colorMorado">Disponible</th>
                            <th class="colorMorado">Ejercicio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tbody>
                        </tbody>
                        <tfoot >
                            <tr>
                                <th class="colorMorado" colspan="2"> Total:</th>
                                <th class="colorMorado"></th>
                                <th class="colorMorado"></th>
                                <th class="colorMorado"></th>
                                <th class="colorMorado"></th>
                                <th class="colorMorado"></th>
                            </tr>
                        </tfoot>
                    </tbody>
                </table>
            </div>
          </div>
      </div>
    </div>
  </div>

  <script src="/js/clavesP/init.js"></script>