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
                        <form id="actividad">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="control-label ">Presupuesto asignado Fondo</label>
                                    <input type="text" id="preFondo" class="form-control" disabled>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="control-label">Presupuesto disponible Fondo</label>
                                    <input type="text" id="preDisFondo" class="form-control" disabled>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="control-label ">Cantidad de beneficiarios*</label>
                                    <input type="text" id="beneficiarios" class="form-control" disabled>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="control-label">Unidad de medida</label>
                                    <input type="text" id="unidadMedida" class="form-control" disabled>
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
                                        <td><input id="enero" name="enero" type="text" class="form-control"></td>
                                        <td><input id="febrero" name="febrero" type="text" class="form-control"></td>
                                        <td><input id="marzo" name="marzo" type="text" class="form-control"></td>
                                        <td><input id="abril" name="abril" type="text" class="form-control"></td>
                                        <td><input id="mayo" name="mayo" type="text" class="form-control"></td>
                                        <td><input id="junio" name="junio" type="text" class="form-control"></td>
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
                                                class="form-control "></td>
                                        <td><input id="agosto" name="agosto" type="text"
                                                class="form-control"></td>
                                        <td><input id="sep" name="sep" type="text"
                                                class="form-control"></td>
                                        <td><input id="octubre" name="octubre" type="text"
                                                class="form-control"></td>
                                        <td><input id="nov" name="nov" type="text"
                                                class="form-control"></td>
                                        <td><input id="dic" name="dic" type="text"
                                                class="form-control"></td>
                                        <tr style="border-style: none;">
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>
                                                <h6><b>Total Calendarizado</b></h6>
                                            </td>
                                            <td><input id="metas" name="metas" type="text" class="form-control">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-2">
                                <button  id="cerrar" type="button" class="btn btn-secondary " >Cancelar</button>
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
    </script>
    
@endsection