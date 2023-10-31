@extends('layouts.app')
@include('calendarizacion.clavePresupuestaria.modalDetalle')
@include('panels.datatable')
@section('content')

<div class="container">
    <section id="widget-grid" class="conteiner">
        <div class="row">
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div color="darken" class="jarviswidget" id="wid-id-1" data-widget-editbutton="false"
                    data-widget-colorbutton="false" data-widget-deletebutton="false">
                    <header>
                        <h2>Editar Calendarizado</h2>
                    </header>
                    <div>
                        <div class="jarviswidget-editbox">
                        </div>        
                        <div class="row">
                            <div class="col-md-2" style="background-color: brown"><p style="color: aliceblue">Centro Gestor</p> </div>
                            <div class="col-md-2" style="background-color: dimgrey"><p style="color: aliceblue">Área Funcional</p></div>
                            <div class="col-md-2" style="background-color: plum"><p style="color: aliceblue">Período Presupuestal</p></div>
                            <div class="col-md-2" style="background-color: grey"><p style="color: aliceblue">Clacificación Económica</p></div>
                            <div class="col-md-2" style="background-color: orangered"><p style="color: aliceblue">Fondo</p></div>
                            <div class="col-md-2" style="background-color: darksalmon"><p style="color: aliceblue">Inversión Pública</p></div>
                        </div>
                        <div class="table-responsive">
                            <table id="newClave" class="table able-bordered" style="width: 100%">
                                <tbody>
                                    <tr class="">
                                        <td class="centro-gestor" id="clasificacion"></td>
                                        <td class="centro-gestor" id="entidadFederativa">16</td>
                                        <td class="centro-gestor" id="region">&nbsp;&nbsp;</td>
                                        <td class="centro-gestor" id="municipio">&nbsp;&nbsp;</td>
                                        <td class="centro-gestor" id="localidad">&nbsp;&nbsp;</td>
                                        <td class="centro-gestor" id="upp"></td>
                                        <td class="centro-gestor" id="subsecretaria">&nbsp;&nbsp;</td>
                                        <td class="centro-gestor" id="ur">&nbsp;&nbsp;</td>
                                        <td class="area-funcional" id="finalidad">&nbsp;&nbsp;</td>
                                        <td class="area-funcional" id="funcion">&nbsp;&nbsp;</td>
                                        <td class="area-funcional" id="subfuncion">&nbsp;&nbsp;</td>
                                        <td class="area-funcional" id="eje">&nbsp;&nbsp;</td>
                                        <td class="area-funcional" id="lineaAccion">&nbsp;&nbsp;</td>
                                        <td class="area-funcional" id="programaSectorial">&nbsp;&nbsp;</td>
                                        <td class="area-funcional" id="conac">&nbsp;&nbsp;</td>
                                        <td class="area-funcional" id="programaPre">&nbsp;&nbsp;</td>
                                        <td class="area-funcional" id="subPrograma">&nbsp;&nbsp;</td>
                                        <td class="area-funcional" id="proyectoPre">&nbsp;&nbsp;</td>
                                        <td class="periodo-presupuestal" id="mesAfectacion">&nbsp;&nbsp;</td>
                                        <td class="clasificacion-economica" id="capitulo">&nbsp;&nbsp;</td>
                                        <td class="clasificacion-economica" id="concepto">&nbsp;&nbsp;</td>
                                        <td class="clasificacion-economica" id="partidaGen">&nbsp;&nbsp;</td>
                                        <td class="clasificacion-economica" id="partidaEpecifica">&nbsp;&nbsp;</td>
                                        <td class="clasificacion-economica" id="tipoGasto">&nbsp;&nbsp;</td>
                                        <td class="fondo" id="anioFondo">&nbsp;&nbsp;</td>
                                        <td class="fondo" id="etiquetado">&nbsp;&nbsp;</td>
                                        <td class="fondo" id="fuenteFinanciamiento">&nbsp;&nbsp;</td>
                                        <td class="fondo" id="ramo">&nbsp;&nbsp;</td>
                                        <td class="fondo" id="fondoRamo">&nbsp;&nbsp;</td>
                                        <td class="fondo" id="capital">&nbsp;&nbsp;</td>
                                        <td class="fondo" id="proyectoObra">000000</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>   
                        <div id="segundaParteUpdate">
                            <form id="actividad">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <p id="lbl_ur"></p>
                                            <div style="clear:both"></div>
                                            <p id="lbl_fondo"></p>
                                        </div>
                                        
                                    </div>
                                    <div class="col-md-3">
                                        <p id="lbl_sector"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="button" class="btn btn-light" data-toggle="modal"
                                            data-target="#detalle"data-backdrop="static" data-keyboard="false" id="verDetalle">Ver detalle clave presupuestaria
                                    </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label class="control-label ">Presupuesto asignado Fondo</label>
                                        <input type="text" id="preFondo" class="form-control montosR" disabled>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="control-label">Presupuesto disponible Fondo</label>
                                        <input type="text" id="preDisFondo" class="form-control montosR" disabled>
                                        <input id="idClave" name="idClave" type="hidden" value={{$clave->id}}>
                                    </div>
                                </div>
                                <div class="table-responsive ">
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
                                            <td><input id="enero" name="enero" type="text" maxlength ="20" class="form-control monto montosR" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();" value={{$clave->enero}}></td>
                                            <td><input id="febrero" name="febrero" type="text" maxlength ="20" class="form-control monto montosR" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();" value={{$clave->febrero}}></td>
                                            <td><input id="marzo" name="marzo" type="text" maxlength ="20" class="form-control monto montosR" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();" value={{$clave->marzo}}></td>
                                            <td><input id="abril" name="abril" type="text" maxlength ="20" class="form-control monto montosR" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();" value={{$clave->abril}}></td>
                                            <td><input id="mayo" name="mayo" type="text" maxlength ="20" class="form-control monto montosR" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();" value={{$clave->mayo}}></td>
                                            <td><input id="junio" name="junio" type="text" maxlength ="20" class="form-control monto montosR" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();" value={{$clave->junio}}></td>
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
                                            <td><input id="julio" name="julio" type="text" maxlength ="20"
                                                    class="form-control monto montosR" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();" value={{$clave->julio}}></td>
                                            <td><input id="agosto" name="agosto" type="text" maxlength ="20"
                                                    class="form-control monto montosR" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();" value={{$clave->agosto}}></td>
                                            <td><input id="septiembre" name="septiembre" type="text" maxlength ="20"
                                                    class="form-control monto montosR" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();" value={{$clave->septiembre}}></td>
                                            <td><input id="octubre" name="octubre" type="text" maxlength ="20"
                                                    class="form-control monto montosR" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();" value={{$clave->octubre}}></td>
                                            <td><input id="noviembre" name="noviembre" type="text" maxlength ="20"
                                                    class="form-control monto montosR" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();"value={{$clave->noviembre}}></td>
                                            <td><input id="diciembre" name="diciembre" type="text" maxlength ="20"
                                                    class="form-control monto montosR" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();" value={{$clave->diciembre}}></td>
                                            <tr style="border-style: none;">
                                                <td><input type="hidden" name="ejercicio" id="ejercicio" value={{$clave->ejercicio}}></td>
                                                <td><input type="hidden" name="clvUpp" id="clvUpp" value={{$clave->upp}}></td>
                                                <td></td>
                                                <td></td>
                                                <td>
                                                    <h6><b>Total Calendarizado</b></h6>
                                                </td>
                                                <td><input id="totalCalendarizado" name="totalCalendarizado" type="text" class="form-control montosR" value={{$clave->total}} readonly>
                                                    <input type="hidden" name="calendarizado" id="calendarizado">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                            <div class="row">
                                <div class="col-md-6"></div>
                                <div class="col-md-2">
                                    <button  id="btnCancelarUpdate" type="button" class="btn btn-secondary " >Cancelar</button>
                                    <button type="button" class="btn btn-primary" id="btnUpdateClv">Guardar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </section>
</div>
 
    <script src="/js/clavesP/init.js"></script>
    <script src="/js/utilerias.js"></script>
    <script src="/js/clavesP/cargamasiva.js"></script>

    <script>
        let id = "{{$clave->id}}";
        let region = "{{$clave->region}}"
        let municipio = "{{$clave->municipio}}"
        let localidad = "{{$clave->localidad}}"
        let upp = "{{$clave->upp}}"
        let ur = "{{$clave->ur}}"
        let programa_presupuestario = "{{$clave->programa_presupuestario}}"
        let subprograma_presupuestario = "{{$clave->subprograma_presupuestario}}"
        let proyecto_presupuestario = "{{$clave->proyecto_presupuestario}}"
        let linea_accion = "{{$clave->linea_accion}}"
        let clv_fondo = "{{$clave->fondo_ramo}}";
        let ejercicio = "{{$clave->ejercicio}}";
        let fondo_ramo = "{{$clave->ejercicio}}"+"{{$clave->etiquetado}}"+"{{$clave->fuente_financiamiento}}"+"{{$clave->ramo}}"+"{{$clave->fondo_ramo}}"+"{{$clave->capital}}";
        let partida = "{{$clave->posicion_presupuestaria}}"+"{{$clave->tipo_gasto}}"
        let clasificacion = "{{$clave->clasificacion_administrativa}}";
        if (region != '') {
            dao.getRegiones(region);
        }
        if (municipio != '') {
            dao.getMunicipiosByRegion(region,municipio);
        }
        if (localidad != '') {
            dao.getLocalidadByMunicipio(municipio,localidad);
        }
        if (upp != '') {
            dao.getUpp(ejercicio,upp);
            dao.getFondosByUpp(upp,subprograma_presupuestario, ejercicio,fondo_ramo);
        }
        if (ur != '') {

            dao.getUninadResponsableByUpp(upp,ejercicio,ur);
            dao.getSubSecretaria(upp,ur,ejercicio);
            dao.getClasificacionAdmin(upp,ur);
        }
        if (programa_presupuestario != '') {
            dao.getProgramaPresupuestarioByur(upp,ur,ejercicio,programa_presupuestario);
        }
        if (subprograma_presupuestario != '') {
            dao.getSubProgramaByProgramaId(ur,programa_presupuestario, upp,ejercicio,subprograma_presupuestario);
            
        }
        if (proyecto_presupuestario != '') {
            dao.getProyectoBySubPrograma(programa_presupuestario,subprograma_presupuestario,upp,ur,ejercicio,proyecto_presupuestario);
        }
        if (linea_accion != '') {
            dao.getLineaDeAccionByUpp(upp,ur,ejercicio,programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario,linea_accion);
            dao.getPartidaByUpp(clasificacion,partida);
            dao.getSector(linea_accion);
            dao.getAreaFuncional(upp,ur,ejercicio,subprograma_presupuestario,linea_accion,programa_presupuestario,proyecto_presupuestario);
        }
        //dao.getPresupuestoPorUpp(upp,clv_fondo,subprograma_presupuestario, ejercicio);
        dao.getPresupuestoPorUppEdit(upp,clv_fondo,subprograma_presupuestario, ejercicio,id);
        $(document).ready(function () {
            soloEnteros();
        });
        
    </script>
    
@endsection