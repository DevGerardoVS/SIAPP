<div class="modal fade continua" id="continua" tabindex="-1" role="dialog" aria-labelledby="continuaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header colorMorado">
          <h5 class="modal-title " id="continuaLabel">Actividad Continua</h5>
          <button type="button" class="close" data-dismiss="continua" aria-label="Close" onclick="dao.clearCont()">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formContinua">
            <div class="form-group">
              <label for="recipient-name" class="col-form-label">Cifra Continua</label>
              <input  onkeypress="return valideKey(event)" type="text" class="form-control" id="nContinua">
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="continua" onclick="dao.clearCont()">cerrar</button>
          <button type="button" class="btn btn-primary" onclick="dao.nCont()">Aceptar</button>
        </div>
      </div>
    </div>
  </div>