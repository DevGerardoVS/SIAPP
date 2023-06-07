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
                <form id="actividad">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="control-label ">Nombre de la actividad</label>
                            <select class="form-control" aria-placeholder="Selecciona una actividad" id="actividad" name="actividad">
                                <option value="NULL" disabled>Selecciona una actividad</option>
                                <option value="0" >fut</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="control-label">Fondo</label>
                            <select class="form-control" aria-placeholder="Selecciona una actividad"  id="fondo" name="fondo">
                                <option value="NULL" disabled>Selecciona un fondo</option>
                                <option value="1" >amlove</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label class="control-label ">Tipo de calendario</label>
                            <select class="form-control" aria-placeholder="Selecciona una actividad"  id="tipo_AC" name="tipo_Ac">
                                <option value="NULL" disabled>Tipo actividad</option>
                                <option value="1" >mma</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="control-label">No. Beneficiarios</label>
                            <input type="text" class="form-control" id="beneficiario" name="beneficiario">
                        </div>
                        <div class="form-group col-md-3">
                            <label class="control-label ">Beneficiarios</label>
                            <select class="form-control" aria-placeholder="Selecciona una actividad"  id="tipo_Be" name="tipo_Be">
                                <option value="NULL" disabled>Selecciona un Beneficiario</option>
                                <option value="3" >estudiambres</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="control-label">Unidad de medida</label>
                            <select class="form-control" aria-placeholder="Selecciona una actividad"  id="medida" name="medida">
                                <option value="NULL" disabled>Selecciona una Unidad</option>
                                <option value="11" >qwerty</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive ">
                        <table id="meses1" class="table table-hover table-striped ">
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
                                <td><input  id="enero" name="enero" type="text" class="form-control" ></td>
                                <td><input  id="febrero" name="febrero" type="text" class="form-control" ></td>
                                <td><input  id="marzo" name="marzo" type="text" class="form-control" ></td>
                                <td><input  id="abril" name="abril" type="text" class="form-control" ></td>
                                <td><input  id="mayo" name="mayo" type="text" class="form-control" ></td>
                                <td><input  id="junio" name="junio" type="text" class="form-control" ></td>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive ">
                        <table id="meses2" class="table table-hover table-striped" style="border-bottom-style: none;">
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
                                <td><input  id="julio" name="julio" type="text" class="form-control" ></td>
                                <td><input  id="agosto" name="agosto" type="text" class="form-control" ></td>
                                <td><input  id="sep" name="sep" type="text" class="form-control" ></td>
                                <td><input  id="octubre" name="octubre" type="text" class="form-control" ></td>
                                <td><input  id="nov" name="nov" type="text" class="form-control" ></td>
                                <td><input  id="dic" name="dic" type="text" class="form-control" ></td>
                                <tr style="border-style: none;">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><h6><b>Metas Calendarizadas</b></h6></td>
                                <td><input  id="metas" name="metas" type="text" class="form-control" ></td>
                                </tr>
                            </tbody>
                        </table>                    
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary"  onclick="dao.add_row()">Guardar</button>
            </div>
        </div>
    </div>
</div>
