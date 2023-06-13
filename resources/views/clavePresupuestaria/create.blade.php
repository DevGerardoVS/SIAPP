@extends('layouts.app')
@include('administracion.usuarios.modalCreate')
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
                            <div class="col-md-2" style="background-color: grey"><p style="color: aliceblue">Clacificación Económica</p></div>
                            <div class="col-md-2" style="background-color: orangered"><p style="color: aliceblue">Fondo</p></div>
                            <div class="col-md-2" style="background-color: darksalmon"><p style="color: aliceblue">Inversión Pública</p></div>
                        </div>
                        <div class="table-responsive">
                            <table id="newClave" class="table able-bordered" style="width: 100%">
                                <thead>
                                    <tr class="">
                                        <th class="centro-gestor">21111</th>
                                        <th class="centro-gestor" id="entidadFederativa">16</th>
                                        <th class="centro-gestor" id="region">&nbsp;&nbsp;</th>
                                        <th class="centro-gestor" id="municipio">&nbsp;&nbsp;</th>
                                        <th class="centro-gestor" id="localidad">&nbsp;&nbsp;</th>
                                        <th class="centro-gestor" id="upp">007</th>
                                        <th class="centro-gestor" id="subsecretaria">&nbsp;&nbsp;</th>
                                        <th class="centro-gestor" id="ur">&nbsp;&nbsp;</th>
                                        <th class="area-funcional" id="finalidad">&nbsp;&nbsp;</th>
                                        <th class="area-funcional" id="funcion">&nbsp;&nbsp;</th>
                                        <th class="area-funcional" id="subfuncion">&nbsp;&nbsp;</th>
                                        <th class="area-funcional" id="eje">&nbsp;&nbsp;</th>
                                        <th class="area-funcional" id="lineaAccion">&nbsp;&nbsp;</th>
                                        <th class="area-funcional" id="programaSectorial">&nbsp;&nbsp;</th>
                                        <th class="area-funcional" id="conac">&nbsp;&nbsp;</th>
                                        <th class="area-funcional" id="programaPre">&nbsp;&nbsp;</th>
                                        <th class="area-funcional" id="subPrograma">&nbsp;&nbsp;</th>
                                        <th class="area-funcional" id="proyectoPre">&nbsp;&nbsp;</th>
                                        <th class="periodo-presupuestal" id="mesAfectacion">&nbsp;&nbsp;</th>
                                        <th class="clasificacion-economica" id="capitulo">&nbsp;&nbsp;</th>
                                        <th class="clasificacion-economica" id="concepto">&nbsp;&nbsp;</th>
                                        <th class="clasificacion-economica" id="partidaGen">&nbsp;&nbsp;</th>
                                        <th class="clasificacion-economica" id="partidaEpecifica">&nbsp;&nbsp;</th>
                                        <th class="clasificacion-economica" id="tipoGasto">&nbsp;&nbsp;</th>
                                        <th class="fondo" id="anioFondo">&nbsp;&nbsp;</th>
                                        <th class="fondo" id="etiquetado">&nbsp;&nbsp;</th>
                                        <th class="fondo" id="fuenteFinanciamiento">&nbsp;&nbsp;</th>
                                        <th class="fondo" id="ramo">&nbsp;&nbsp;</th>
                                        <th class="fondo" id="fondoRamo">&nbsp;&nbsp;</th>
                                        <th class="fondo" id="capital">&nbsp;&nbsp;</th>
                                        <th class="fondo" id="proyectoObra">&nbsp;&nbsp;</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>   
                        <form class="form-horizontal" id="frm_create_clave">
                            @csrf
                            <div class="row">
                                <div class="col-md-2"></div>
                                    <div class="col-md-10">
                                        <label class="control-label">Region*</label>
                                        <select class="form-control select2" name="sel_region" id="sel_region"></select>
                                    </div>                            
                                <div style="clear:both"></div>
                                <div class="col-md-2"></div>
                                    <div class="col-md-10">
                                        <label class="control-label">Municipio*</label>
                                        <select class="form-control select2" name="sel_municipio" id="sel_municipio"></select>
                                    </div>
                                <div style="clear:both"></div>
                                <div class="col-md-2"></div>
                                    <div class="col-md-10">
                                        <label class="control-label">Localidad*</label>
                                        <select class="form-control select2" name="sel_localidad" id="sel_localidad"></select>
                                    </div>                                                               
                                <div style="clear:both"></div>
                                <div class="col-md-2"></div>
                                    <div class="col-md-10">
                                        <label class="control-label">Unidad Programática Presupuestal*</label>
                                        <select class="form-control select2" name="sel_upp" id="sel_upp"></select>
                                    </div>                                
                                <div style="clear:both"></div>
                                <div class="col-md-2"></div>
                                    <div class="col-md-10">
                                        <label class="control-label">Unidad Responsable*</label>
                                        <select class="form-control select2" name="sel_unidad_res" id="sel_unidad_res"></select>
                                    </div>                                                                
                                <div style="clear:both"></div>
                                <div class="col-md-2"></div>
                                    <div class="col-md-10">
                                        <label class="control-label">Programa Presupuestario*</label>
                                        <select class="form-control select2" name="sel_programa" id="sel_programa"></select>
                                    </div>                                                                
                                <div style="clear:both"></div>
                                <div class="col-md-2"></div>
                                    <div class="col-md-10">
                                        <label class="control-label">Subprograma Presupuestario*</label>
                                        <select class="form-control select2" name="sel_sub_programa" id="sel_sub_programa"></select>
                                    </div>                                
                                <div style="clear:both"></div>
                                <div class="col-md-2"></div>
                                    <div class="col-md-10">
                                        <label class="control-label">Proyecto*</label>
                                        <select class="form-control select2" name="sel_proyecto" id="sel_proyecto"></select>
                                    </div>                                
                                <div style="clear:both"></div>
                                <div class="col-md-2"></div>
                                    <div class="col-md-10">
                                        <label class="control-label">Linia de Acción</label>
                                        <select class="form-control select2" name="sel_linea" id="sel_linea"></select>
                                    </div>                                
                                <div style="clear:both"></div>
                                <div class="col-md-2"></div>
                                    <div class="col-md-10">
                                        <label class="control-label">Periodo Presupuestario</label>
                                        <select class="form-control select2" name="sel_periodo" id="sel_periodo">
                                            <option value="">-- Seleccione Periodo Presupuestal --</option>
                                            <option value="1-ENE">1-ENE-DEC</option>
                                        </select>
                                    </div>
                                <div style="clear:both"></div>
                                <div class="col-md-2"></div>
                                    <div class="col-md-10">
                                        <label class="control-label">Partida</label>
                                        <select class="form-control select2" name="sel_partida" id="sel_partida"></select>
                                    </div>                                
                                <div style="clear:both"></div>
                                <div class="col-md-2"></div>
                                    <div class="col-md-10">
                                        <label class="control-label">Fondo</label>
                                        <select class="form-control select2" name="sel_fondo" id="sel_fondo"></select>
                                    </div>                                
                                <div style="clear:both"></div>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-md-5"></div>
                            <div class="col-md-2">
                                <button  id="cerrar" type="button" class="btn btn-secondary " data-dismiss="modal" aria-label="Close" onclick="dao.limpiarFormularioCrear()">Cancelar</button>
                                <button type="button" class="btn btn-primary" id="btnSave">Guardar</button>
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
        //dao.getData();
        dao.getRegiones("");
        dao.getUpp("");
    </script>
    
@endsection