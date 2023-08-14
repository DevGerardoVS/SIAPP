<div class="modal-body">
    <form id="actividad">
        <input type="hidden" id="0" name="0" value="00">
        <div class="row">
            <div class="row">
                <div class="form-group col-md-6">
                    <label class="control-label">Nombre de la actividad</label>
                    <select class="form-control" aria-placeholder="Selecciona una actividad" id="sel_actividad" data-live-search="true"
                    name="sel_actividad" disabled>
                    <option value="">---Actividad---</option>
                </select>
                </div>
                <div class="form-group col-md-6">
                    <label class="control-label ">Fondo &nbsp&nbsp&nbsp&nbsp</label>
                    <select class="form-control" placeholder="Selecciona una actividad" id="sel_fondo" data-live-search="true"
                        name="sel_fondo" autocomplete="anio_filter" placeholder="Seleccione un año"  disabled>
                        <option value="" >---Fondo---</option>
                    </select>
                </div>
            </div>
            <div class="form-group col-md-3">
                <label class="control-label">Tipo de calendario</label>
                <select class="form-control" aria-placeholder="Selecciona una actividad" id="tipo_Ac" data-live-search="true"
                    name="tipo_Ac" disabled >
                    <option value="">---Tipo de Calendario---</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="control-label">No. Beneficiarios</label>
                <input type="text" class="form-control" id="beneficiario" name="beneficiario" onkeypress="return valideKey(event)" >
                <span id="beneficiario-error" class="has-error"></span>
            </div>
            <div class="col-md-3">
                <label class="control-label">Beneficiarios &nbsp; &nbsp; &nbsp; &nbsp;</label>
                <select class="form-control"  id="tipo_Be" data-live-search="true"
                    name="tipo_Be">
                </select>
                <span id="tipo_Be-error" class="has-error" aria-describedby="tipo_Be-erro"></span>
            </div>
            <div class="form-group col-md-3">
                <label class="control-label">Unidad de medida</label>
                <select class="form-control" aria-placeholder="Selecciona una Medida" data-live-search="true" id="medida"
                    name="medida">
                </select>
                <span id="medida-error" class="has-error"></span>

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
                    <td><input onkeypress="return valideKey(event)" id="1" name="1" type="text" class="form-control meses" onkeyup="dao.sumar();" disabled></td>
                    <td><input onkeypress="return valideKey(event)" id="2" name="2" type="text" class="form-control meses" onkeyup="dao.sumar();" disabled></td>
                    <td><input onkeypress="return valideKey(event)" id="3" name="3" type="text" class="form-control meses" onkeyup="dao.sumar();" disabled></td>
                    <td><input onkeypress="return valideKey(event)" id="4" name="4" type="text" class="form-control meses" onkeyup="dao.sumar();" disabled></td>
                    <td><input onkeypress="return valideKey(event)" id="5" name="5" type="text" class="form-control meses" onkeyup="dao.sumar();" disabled></td>
                    <td><input onkeypress="return valideKey(event)" id="6" name="6" type="text" class="form-control meses" onkeyup="dao.sumar();" disabled></td>
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
                    <td><input onkeypress="return valideKey(event)" onkeyup="dao.sumar();" id="7" name="7" type="text"   class="form-control  meses" disabled></td>
                    <td><input onkeypress="return valideKey(event)" onkeyup="dao.sumar();" id="8" name="8" type="text"   class="form-control  meses" disabled></td>
                    <td><input onkeypress="return valideKey(event)" onkeyup="dao.sumar();" id="9" name="9" type="text"   class="form-control  meses" disabled></td>
                    <td><input onkeypress="return valideKey(event)" onkeyup="dao.sumar();" id="10" name="10" type="text" class="form-control  meses" disabled></td>
                    <td><input onkeypress="return valideKey(event)" onkeyup="dao.sumar();" id="11" name="11" type="text" class="form-control  meses" disabled></td>
                    <td><input onkeypress="return valideKey(event)" onkeyup="dao.sumar();" id="12" name="12" type="text" class="form-control  meses" disabled></td>
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