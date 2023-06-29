@extends('layouts.app')
@include('panels.datatable')
@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>
    <script src="{{ asset('vendors/js/tables/datatable/datatable-responsive/datatables.responsive.min.js') }}"></script>

<div class="container">
    <section id="widget-grid" class="conteiner">
        <div class="row">
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div color="darken" class="jarviswidget" id="wid-id-1" data-widget-editbutton="false"
                    data-widget-colorbutton="false" data-widget-deletebutton="false">
                    <header>
                        <h2>Editar Clave Presupuestaria</h2>
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
                        <div id="primeraParte">
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
                                                <option value="01-ENE" selected>1-ENE-DEC</option>
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
                                    <div style="clear:both"></div>
                                </div>
                            </form>
                            <div class="row">
                                <div class="col-md-6"></div>
                                <div class="col-md-2">
                                    <button  id="btnCancelarUpdate" type="button" class="btn btn-secondary" name="btnCancelarUpdate">Cancelar</button>
                                    <button type="button" class="btn btn-primary" id="btnUpdateSave">Guardar</button>
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
        let fondo_ramo = "{{$clave->ejercicio}}"+"{{$clave->etiquetado}}"+"{{$clave->fuente_financiamiento}}"+"{{$clave->ramo}}"+"{{$clave->fondo_ramo}}"+"{{$clave->capital}}";
        let partida = "{{$clave->posicion_presupuestaria}}"+"{{$clave->tipo_gasto}}"
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
            dao.getUpp(upp);
            dao.getFondosByUpp(upp,fondo_ramo);
        }
        if (ur != '') {
            dao.getUninadResponsableByUpp(upp,ur);
            dao.getSubSecretaria(upp,ur);
            dao.getAreaFuncional(upp,ur);
            dao.getClasificacionAdmin(upp,ur);
        }
        if (programa_presupuestario != '') {
            dao.getProgramaPresupuestarioByur(upp,ur,programa_presupuestario);
        }
        if (subprograma_presupuestario != '') {
            dao.getSubProgramaByProgramaId(ur,programa_presupuestario,subprograma_presupuestario);
        }
        if (proyecto_presupuestario != '') {
            dao.getProyectoBySubPrograma(programa_presupuestario,subprograma_presupuestario,proyecto_presupuestario);
        }
        if (linea_accion != '') {
            dao.getLineaDeAccionByUpp(upp,ur,linea_accion);
            dao.getPartidaByUpp(partida);
        }
        
        
    </script>
    
@endsection