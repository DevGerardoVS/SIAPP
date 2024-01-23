<div class="modal-body">
    <form id="actividad">
        <input type="hidden" id="0" name="0" value="00">
        <div class="row">
            <div  class="fondodiv form-group col-md-6">
                <label class="control-label ">Fondo &nbsp&nbsp&nbsp&nbsp</label>
                <select class="form-control" placeholder="Selecciona una actividad" id="sel_fondo"
                    data-live-search="true" name="sel_fondo" autocomplete="sel_fondo" disabled>
                    <option value="">Fondo</option>
                </select>
            </div>
                <div class=" form-group col-md-6 actividaddiv">
                    <label class="control-label">Nombre de la actividad</label>
                    <select class="form-control" aria-placeholder="Selecciona una actividad" id="actividad_id"
                        data-live-search="true" name="actividad_id" disabled>
                        <option value="">Actividad</option>
                    </select>
                </div>
                <div class="form-group col-md-4 inputAc" style="display: none">
                    <label class="control-label">Nueva Actividad</label>
                    <input type="text" class="form-control" id="inputAc" name="inputAc" disabled>
                    <span id="inputAc-error"></span>
                </div>
            <div class="form-group col-md-3">
                <label class="control-label">Tipo de calendario</label>
                <select class="form-control" aria-placeholder="Selecciona una actividad" id="tipo_Ac"
                    data-live-search="true" name="tipo_Ac" disabled>
                    <option value="">Tipo de Calendario</option>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label class="control-label">No. Beneficiarios</label>
                <input type="text" class="form-control" id="beneficiario" name="beneficiario"
                    onkeypress="return valideKey(event)">
            </div>
            <div class="form-group col-md-3" style="text-align:center;">
                <label class="control-label">Beneficiarios &nbsp; &nbsp; &nbsp; &nbsp;</label>
                <select class="form-control" id="tipo_Be" data-live-search="true" name="tipo_Be">
                </select>
            </div>
            <div class="form-group col-md-3" style="text-align:center;">
                <label class="control-label">Unidad de medida</label>
                <select class="form-control" aria-placeholder="Selecciona una Medida" data-live-search="true"
                    id="medida" name="medida">
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
                    <td><input onkeypress="return valideKey(event)" id="1" name="1" type="text"
                            class="form-control meses" onkeyup="dao.sumar();" disabled>
                        <span id="1-error"></span>
                    </td>
                    <td><input onkeypress="return valideKey(event)" id="2" name="2" type="text"
                            class="form-control meses" onkeyup="dao.sumar();" disabled>
                        <span id="2-error"></span>
                    </td>
                    <td><input onkeypress="return valideKey(event)" id="3" name="3" type="text"
                            class="form-control meses" onkeyup="dao.sumar();" disabled>
                        <span id="3-error"></span>
                    </td>
                    <td><input onkeypress="return valideKey(event)" id="4" name="4" type="text"
                            class="form-control meses" onkeyup="dao.sumar();" disabled>
                        <span id="4-error"></span>
                    </td>
                    <td><input onkeypress="return valideKey(event)" id="5" name="5" type="text"
                            class="form-control meses" onkeyup="dao.sumar();" disabled>
                        <span id="5-error"></span>
                    </td>
                    <td><input onkeypress="return valideKey(event)" id="6" name="6" type="text"
                            class="form-control meses" onkeyup="dao.sumar();" disabled>
                        <span id="6-error"></span>
                    </td>
                </tbody>
            </table>
        </div>
        <label id="meses-error" class="d-flex justify-content-center"></label>
        <div class="table-responsive ">
            <table id="meses2" class="table table-hover table-striped" style="border-bottom-style: none;">
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
                    <td><input onkeypress="return valideKey(event)" onkeyup="dao.sumar();" id="7"
                            name="7" type="text" class="form-control  meses" disabled>
                        <span id="7-error"></span>
                    </td>
                    <td><input onkeypress="return valideKey(event)" onkeyup="dao.sumar();" id="8"
                            name="8" type="text" class="form-control  meses" disabled>
                        <span id="8-error"></span>
                    </td>
                    <td><input onkeypress="return valideKey(event)" onkeyup="dao.sumar();" id="9"
                            name="9" type="text" class="form-control  meses" disabled>
                        <span id="9-error"></span>
                    </td>
                    <td><input onkeypress="return valideKey(event)" onkeyup="dao.sumar();" id="10"
                            name="10" type="text" class="form-control  meses" disabled>
                        <span id="10-error"></span>
                    </td>
                    <td><input onkeypress="return valideKey(event)" onkeyup="dao.sumar();" id="11"
                            name="11" type="text" class="form-control  meses" disabled>
                        <span id="11-error"></span>
                    </td>
                    <td><input onkeypress="return valideKey(event)" onkeyup="dao.sumar();" id="12"
                            name="12" type="text" class="form-control  meses" disabled>
                        <span id="12-error"></span>
                    </td>
                    <tr style="border-style: none;">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <h6><b>Metas Calendarizadas</b></h6>
                        </td>
                        <td><input onkeypress="return valideKeySum(event)" id="sumMetas" name="sumMetas"
                                type="text" class="form-control form-group">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </form>
</div>
