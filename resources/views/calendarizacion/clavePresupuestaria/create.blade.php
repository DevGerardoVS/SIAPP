@extends('layouts.app')
@include('calendarizacion.clavePresupuestaria.modalDetalle')
@include('calendarizacion.clavePresupuestaria.modalImgClave')
@include('panels.datatable')
@section('content')
<div class="container">
    <section id="widget-grid" class="conteiner">
        <div class="row">
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div color="darken" class="jarviswidget" id="wid-id-1" data-widget-editbutton="false"
                    data-widget-colorbutton="false" data-widget-deletebutton="false">
                    <header>
                        <h2>Crear Clave Presupuestaria</h2>
                    </header>
                    <div>
                        <div class="jarviswidget-editbox">
                        </div>        
                        <div class="row">
                            <div class="col-md-2" style="background-color: brown"><p style="color: aliceblue">Centro Gestor</p> </div>
                            <div class="col-md-2" style="background-color: dimgrey"><p style="color: aliceblue">Área Funcional</p></div>
                            <div class="col-md-2" style="background-color: plum"><p style="color: aliceblue">Período Presupuestal</p></div>
                            <div class="col-md-2" style="background-color: grey"><p style="color: aliceblue">Clasificación Económica</p></div>
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
                        <div id="primeraParte">
                            <div class="row">
                                <div class="col-md-8"></div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-light" data-toggle="modal"
                                        data-target="#imgClave" data-backdrop="static" data-keyboard="false" id="imgClave">¿Cómo está construida la clave presupuestaria?
                                </button>
                                </div>
                            </div>
                            <br>
                            <form class="form-horizontal" id="frm_create_clave">
                                @csrf
                                <div class="row">
                                    <div class="col-md-1"></div>
                                    <div class="col-md-3"><label class="control-label">Region*</label></div>
                                        <div class="col-md-8">
                                            <select class="form-control select2" name="sel_region" id="sel_region" data-live-search="true"></select>
                                        </div>      
                                                             
                                    <div style="clear:both"></div>
                                    <div class="col-md-1"></div>
                                    <div class="col-md-3"><label class="control-label">Municipio*</label></div>
                                        <div class="col-md-8">
                                            
                                            <select class="form-control select2" name="sel_municipio" id="sel_municipio"></select>
                                        </div>
                                    <div style="clear:both"></div>
                                    <div class="col-md-1"></div>
                                    <div class="col-md-3"><label class="control-label">Localidad*</label></div>
                                        <div class="col-md-8">
                                            
                                            <select class="form-control select2" name="sel_localidad" id="sel_localidad"></select>
                                        </div>                                                               
                                    <div style="clear:both"></div>
                                    <div class="col-md-1"></div>
                                    <div class="col-md-3"><label class="control-label">Unidad Programática Presupuestal*</label></div>
                                        <div class="col-md-8">
                                            
                                            <select class="form-control select2" name="sel_upp" id="sel_upp"></select>
                                        </div>                                
                                    <div style="clear:both"></div>
                                    <div class="col-md-1"></div>
                                    <div class="col-md-3"><label class="control-label">Unidad Responsable*</label></div>
                                        <div class="col-md-8">
                                            
                                            <select class="form-control select2" name="sel_unidad_res" id="sel_unidad_res"></select>
                                        </div>                                                                
                                    <div style="clear:both"></div>
                                    <div class="col-md-1"></div>
                                    <div class="col-md-3"><label class="control-label">Programa Presupuestario*</label></div>
                                        <div class="col-md-8">
                                            
                                            <select class="form-control select2" name="sel_programa" id="sel_programa"></select>
                                        </div>                                                                
                                    <div style="clear:both"></div>
                                    <div class="col-md-1"></div>
                                    <div class="col-md-3"><label class="control-label">Subprograma Presupuestario*</label></div>
                                        <div class="col-md-8">
                                            
                                            <select class="form-control select2" name="sel_sub_programa" id="sel_sub_programa"></select>
                                        </div>                                
                                    <div style="clear:both"></div>
                                    <div class="col-md-1"></div>
                                    <div class="col-md-3"><label class="control-label">Proyecto*</label></div>
                                        <div class="col-md-8">
                                            
                                            <select class="form-control select2" name="sel_proyecto" id="sel_proyecto"></select>
                                        </div>                                
                                    <div style="clear:both"></div>
                                    <div class="col-md-1"></div>
                                    <div class="col-md-3"><label class="control-label">Linia de Acción*</label></div>
                                        <div class="col-md-8">
                                            
                                            <select class="form-control select2" name="sel_linea" id="sel_linea"></select>
                                        </div>                                
                                    <div style="clear:both"></div>
                                    <div class="col-md-1"></div>
                                    <div class="col-md-3"><label class="control-label">Periodo Presupuestario*</label></div>
                                        <div class="col-md-8">
                                            
                                            <select class="form-control select2" name="sel_periodo" id="sel_periodo">
                                                <option value="">-- Seleccione Periodo Presupuestal --</option>
                                                <option value="01-ENE">1-ENE-DEC</option>
                                            </select>
                                        </div>
                                    <div style="clear:both"></div>
                                    <div class="col-md-1"></div>
                                    <div class="col-md-3"><label class="control-label">Partida*</label></div>
                                        <div class="col-md-8">
                                            
                                            <select class="form-control select2" name="sel_partida" id="sel_partida" data-live-search="true"></select>
                                        </div>                                
                                    <div style="clear:both"></div>
                                    <div class="col-md-1"></div>
                                    <div class="col-md-3"><label class="control-label">Fondo*</label></div>
                                        <div class="col-md-8">
                                            
                                            <select class="form-control select2" name="sel_fondo" id="sel_fondo"></select>
                                        </div>        
                                        <input type="hidden" id="tipo" name="tipo">    
                                        <input type="hidden" id="anio" name="anio" value={{$ejercicio}}>                      
                                    <div style="clear:both"></div>
                                    <div class="col-md-1"></div>
                                </div>
                                <div class="row" id="obras" style="display: none;">
                                    <div class="col-md-1"></div>
                                    <div class="col-md-3"><label class="control-label">Proyecto Obra*</label></div>
                                        <div class="col-md-8">
                                            <select class="form-control select2" name="sel_obra" id="sel_obra" data-live-search="true" style="width: 600px;">
                                            </select>
                                        </div>
                                </div>
                            </form>
                            <div class="row">
                                <div class="col-md-6"></div>
                                <div class="col-md-2">
                                    <button  id="btnCancelar" type="button" class="btn btn-secondary" id="btnCancelar">Cancelar</button>
                                    <button type="button" class="btn btn-primary" id="btnSaveClave">Siguente</button>
                                </div>
                            </div>
                        </div>
                        <div id="segundaParte">
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
                                        <input type="text" id="preFondo" class="form-control" disabled>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="control-label">Presupuesto disponible Fondo</label>
                                        <input type="text" id="preDisFondo" class="form-control" disabled>
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
                                            <td><input id="enero" name="enero" type="text" class="form-control monto" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();" value=0></td>
                                            <td><input id="febrero" name="febrero" type="text" class="form-control monto" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();" value=0></td>
                                            <td><input id="marzo" name="marzo" type="text" class="form-control monto" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();" value=0></td>
                                            <td><input id="abril" name="abril" type="text" class="form-control monto" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();" value=0></td>
                                            <td><input id="mayo" name="mayo" type="text" class="form-control monto" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();" value=0></td>
                                            <td><input id="junio" name="junio" type="text" class="form-control monto" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();" value=0></td>
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
                                            <td><input id="julio" name="julio" type="text"
                                                    class="form-control monto" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();" value=0></td>
                                            <td><input id="agosto" name="agosto" type="text"
                                                    class="form-control monto" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();" value=0></td>
                                            <td><input id="septiembre" name="septiembre" type="text"
                                                    class="form-control monto" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();" value=0></td>
                                            <td><input id="octubre" name="octubre" type="text"
                                                    class="form-control monto" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();" value=0></td>
                                            <td><input id="noviembre" name="noviembre" type="text"
                                                    class="form-control monto" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();"value=0></td>
                                            <td><input id="diciembre" name="diciembre" type="text"
                                                    class="form-control monto" onkeypress="return valideKey(event);" onkeyup="calucalarCalendario();" value=0></td>
                                            <tr style="border-style: none;">
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>
                                                    <h6><b>Total Calendarizado</b></h6>
                                                </td>
                                                <td><input id="totalCalendarizado" name="totalCalendarizado" type="text" class="form-control" readonly>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                            <div class="row">
                                <div class="col-md-6"></div>
                                <div class="col-md-2">
                                    <button  id="btnRegresar" type="button" class="btn btn-secondary " >Regresar</button>
                                    <button type="button" class="btn btn-primary" id="btnSaveAll">Guardar</button>
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
        dao.getRegiones("");
        dao.getUpp("");
    </script>
    
@endsection