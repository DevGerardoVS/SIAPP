<!-- Modal -->
<div class="modal fade" id="editar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="editarModal">Editar registro</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
            <input type="number" id="editarID" hidden >
        <div class="modal-body">
            <div class="row">
                <div class="widget-body no-padding ">
                    <div class="table-responsive" id="tableScroll">
                        <table id="editFondo" class="table table-hover table-striped ">
                            <thead>
                                <tr class="colorMorado">
                                    <th>ID UPP</th>
                                    <th>Unidad Programatica Presupuestaria</th>
                                    <th>Tipo</th>
                                    <th>ID Fondo</th>
                                    <th>Fondo</th>
                                    <th>Presupuesto</th>
                                    <th>Ejercicio</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-success" onclick="editarRegistro()" id="editar">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
