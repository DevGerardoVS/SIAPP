<div class="modal fade" id="addActividad" tabindex="-1" role="dialog" aria-labelledby="addActividadLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header colorMorado">
                <h5 class="modal-title " id="addActividadLabel">Agregar Actividad</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="control-label ">Nombre de la actividad</label>
                            <select class="form-control" aria-placeholder="Selecciona una actividad">
                                <option value="NULL" disabled>Selecciona una actividad</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="control-label">Fondo</label>
                            <select class="form-control" aria-placeholder="Selecciona una actividad">
                                <option value="NULL" disabled>Selecciona un fondo</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label class="control-label ">Tipo de calendario</label>
                            <select class="form-control" aria-placeholder="Selecciona una actividad">
                                <option value="NULL" disabled>Tipo actividad</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="control-label">No. Beneficiarios</label>
                            <input type="text" class="form-control" id="nombre" name="nombre">
                        </div>
                        <div class="form-group col-md-3">
                            <label class="control-label ">Beneficiarios</label>
                            <select class="form-control" aria-placeholder="Selecciona una actividad">
                                <option value="NULL" disabled>Selecciona un Beneficiario</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="control-label">Unidad de medida</label>
                            <select class="form-control" aria-placeholder="Selecciona una actividad">
                                <option value="NULL" disabled>Selecciona una Unidad</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive ">
                        <table id="catalogo" class="table table-hover table-striped ">
                            <thead>
                                <tr class="colorMorado">
                                    <th>Enero</th>
                                    <th>Febrero</th>
                                    <th>Marzo</th>
                                    <th>Abril</th>
                                    <th>Mayo</th>
                                    <th>Junio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <td><input  type="text" class="form-control" ></td>
                                <td><input  type="text" class="form-control" ></td>
                                <td><input  type="text" class="form-control" ></td>
                                <td><input  type="text" class="form-control" ></td>
                                <td><input  type="text" class="form-control" ></td>
                                <td><input  type="text" class="form-control" ></td>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive ">
                        <table id="catalogo" class="table table-hover table-striped" style="border-bottom-style: none;">
                            <thead>
                                <tr class="colorMorado">
                                    <th>Julio</th>
                                    <th>Agosto</th>
                                    <th>Septiembre</th>
                                    <th>Octubre</th>
                                    <th>Noviembre</th>
                                    <th>Diciembre</th>
                                </tr>
                            </thead>
                            <tbody>
                                <td><input  type="text" class="form-control" ></td>
                                <td><input  type="text" class="form-control" ></td>
                                <td><input  type="text" class="form-control" ></td>
                                <td><input  type="text" class="form-control" ></td>
                                <td><input  type="text" class="form-control" ></td>
                                <td><input  type="text" class="form-control" ></td>
                                <tr style="border-style: none;">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><h6><b>Metas Calendarizadas</b></h6></td>
                                <td><input  type="text" class="form-control" ></td>
                                </tr>
                            </tbody>
                        </table>                    
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>
