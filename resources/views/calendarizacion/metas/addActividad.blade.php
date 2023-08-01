<div class="modal fade" id="addActividad" tabindex="-1" role="dialog" aria-labelledby="addActividadLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header colorMorado">
                <h5 class="modal-title " id="addActividadLabel">Editar Meta</h5>
                <button id="cerrar" type="button" class="close" data-dismiss="modal" aria-label="Close"  onclick="dao.limpiar()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="actividad">
                    <textarea name="id_meta" id="id_meta" style="display: none"></textarea>
                    <input type="hidden" id="0" name="0" value="00">
                    <div class="row">
                        <div class="table-responsive ">
                            &nbsp
                            <table id="proyectoMD" class="table table-hover table-striped">
                            </table>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="control-label">Tipo de calendario</label>
                            <select class="form-control" aria-placeholder="Selecciona una actividad" id="tipo_Ac" data-live-search="true"
                                name="tipo_Ac" >
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="control-label">No. Beneficiarios</label>
                            <input type="text" class="form-control" id="beneficiario" name="beneficiario" onkeypress="return valideKey(event)" >
                        </div>
                        <div class="form-group col-md-3">
                            <label class="control-label">Beneficiarios</label>
                            <select class="form-control select2" aria-placeholder="Selecciona una Beneficiarios" id="tipo_Be" data-live-search="true"
                                name="tipo_Be">
                                <option value="">Beneficiarios</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="control-label">Unidad de medida</label>
                            <select class="form-control select2" aria-placeholder="Selecciona una Medida" data-live-search="true" id="medida"
                                name="medida">
                                <option value="">Unidad de medida</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="meses1" class="table table-hover table-striped ">
                            <thead>
                                <tr class="colorMorado" style="text-align:center;">
                                    <th>Enero</th>
                                    <th>Febrero</th>
                                    <th>Marzo</th>
                                    <th>Abril</th>
                                    <th>Mayo</th>
                                    <th>Junio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <td><input onkeypress="return valideKey(event)" id="1" name="1" type="text" class="form-control meses" onkeyup="dao.sumar();" ></td>
                                <td><input onkeypress="return valideKey(event)" id="2" name="2" type="text" class="form-control meses" onkeyup="dao.sumar();" ></td>
                                <td><input onkeypress="return valideKey(event)" id="3" name="3" type="text" class="form-control meses" onkeyup="dao.sumar();" ></td>
                                <td><input onkeypress="return valideKey(event)" id="4" name="4" type="text" class="form-control meses" onkeyup="dao.sumar();" ></td>
                                <td><input onkeypress="return valideKey(event)" id="5" name="5" type="text" class="form-control meses" onkeyup="dao.sumar();" ></td>
                                <td><input onkeypress="return valideKey(event)" id="6" name="6" type="text" class="form-control meses" onkeyup="dao.sumar();" ></td>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive ">
                        <table id="meses2" class="table table-hover table-striped"
                            style="border-bottom-style: none;">
                            <thead>
                                <tr class="colorMorado" style="text-align:center;">
                                    <th>Julio </th>
                                    <th>Agosto </th>
                                    <th>Septiembre</th>
                                    <th>Octubre </th>
                                    <th>Noviembre </th>
                                    <th>Diciembre </th>
                                </tr>
                            </thead>
                            <tbody>
                                <td><input onkeypress="return valideKey(event)" onkeyup="dao.sumar();" id="7" name="7" type="text"   class="form-control  meses" ></td>
                                <td><input onkeypress="return valideKey(event)" onkeyup="dao.sumar();" id="8" name="8" type="text"   class="form-control  meses" ></td>
                                <td><input onkeypress="return valideKey(event)" onkeyup="dao.sumar();" id="9" name="9" type="text"   class="form-control  meses" ></td>
                                <td><input onkeypress="return valideKey(event)" onkeyup="dao.sumar();" id="10" name="10" type="text" class="form-control  meses" ></td>
                                <td><input onkeypress="return valideKey(event)" onkeyup="dao.sumar();" id="11" name="11" type="text" class="form-control  meses" ></td>
                                <td><input onkeypress="return valideKey(event)" onkeyup="dao.sumar();" id="12" name="12" type="text" class="form-control  meses" ></td>
                                <tr style="border-style: none;">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <h6><b>Metas Calendarizadas</b></h6>
                                    </td>
                                    <td><input onkeypress="return valideKeySum(event)" id="sumMetas" name="sumMetas" type="text" class="form-control" >
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="cancelar"  type="button" class="btn btn-secondary" data-dismiss="modal"onclick="dao.limpiar()">Cancelar</button>
                <button id="btnSave" type="button" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>
