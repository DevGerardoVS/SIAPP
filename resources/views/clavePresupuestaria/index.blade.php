@extends('layouts.app')
@include('administracion.usuarios.modalCreate')
@include('panels.datatable')
@section('content')
<link href="https://cdn.datatables.net/v/dt/dt-1.13.4/rg-1.3.1/datatables.min.css" rel="stylesheet"/>
<script src="https://cdn.datatables.net/v/dt/dt-1.13.4/rg-1.3.1/datatables.min.js"></script>
    <div class="container">
        <form action="{{ route('getdata') }}" id="buscarForm" method="GET">
            @csrf
            <div class="row">
                <div class="col-sm-2">
                </div>
        </form>
        <section id="widget-grid" class="conteiner">
            <div class="row">
                <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                    <div color="darken" class="jarviswidget" id="wid-id-1" data-widget-editbutton="false"
                        data-widget-colorbutton="false" data-widget-deletebutton="false">
                        <header>
                            <h2>Programación Presupuestal</h2>
                        </header>
                        <div>
                            <div class="jarviswidget-editbox">
                            </div>
                            <div class="widget-body-toolbar">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="asignadoUpp">Asignado Upp:</label>
                                        <input type="text" id="asignadoUpp" name="asignadoUpp" class="form-control" disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="asignadoUpp">Calendarizado:</label>
                                        <input type="text" id="calendarizado" name="calendarizado" class="form-control" disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="asignadoUpp">Disponible Upp:</label>
                                        <input type="text" id="disponibleUpp" name="disponibleUpp" class="form-control" disabled>
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <label for="buttonPresupuesto">&nbsp;</label>
                                        <button type="button" class="btn btn-success form-control" data-toggle="modal" id="btnPresupuesto"
                                            data-target=".bd-example-modal-lg" data-backdrop="static"
                                            data-keyboard="false">Presupuesto por fondo</button>
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-2 text-right">
                                        <label for="buttonBtnNew">&nbsp;</label>
                                        <button type="button" class="btn btn-success form-control" data-toggle="modal" id="btnNuevaClave"
                                            data-target="#modalNewClave" data-backdrop="static"
                                            data-keyboard="false">Nueva Clave</button>
                                    </div>
                                </div>
                            </div>
                            <br><br>
                            
                                <div class="table-responsive">
                                    <table id="claves" class="table table-hover table-striped ">
                                        <thead>
                                            <tr class="colorMorado">
                                                <th>Clasificacion</th>
                                                <th>Centro Gestor</th>
                                                <th>Area Funcional</th>
                                                <th>Periodo</th>
                                                <th>Posicion</th>
                                                <th>Fondo</th>
                                                <th>Proyecto</th>
                                                <th>Total</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            
                        </div>
                    </div>
                </article>
            </div>
        </section>
    </div>
    {{-- modal para crear una nueva clave --}}
    <div class="modal fade bd-example-modal-lg" id="modalNewClave" tabindex="-1" role="dialog"
    aria-labelledby="modalNewClaveLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#6A0F49 !important; color:whitesmoke">
                <h5 class="modal-title" id="modalNewClaveLabel">Agregar una Clave presupuestaria</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
                    <a aria-hidden="true" style="color: whitesmoke" onclick="">&times;</a>
                </button>
            </div>
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <table>
                            <tr>
                                <th>21111</th>
                                <th>16</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>007</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                                <th>&nbsp;&nbsp;</th>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-2"></div>
                </div>
                <br><br>

                <form id="frm_create_clave">
                    @csrf
                    <div class="row">
                        <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <label class="control-label">Region*</label>
                                <select class="form-control select2" name="sel_region" id="sel_region"></select>
                            </div>
                        <div class="col-md-2"></div>
                        <div style="clear:both"></div>
                        <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <label class="control-label">Municipio*</label>
                                <select class="form-control select2" name="sel_municipio" id="sel_municipio"></select>
                            </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div style="clear:both"></div>
                        <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <label class="control-label">Localidad*</label>
                                <select class="form-control select2" name="sel_localidad" id="sel_localidad"></select>
                            </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div style="clear:both"></div>
                        <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <label class="control-label">Unidad Prograática Presupuestal*</label>
                                <select class="form-control select2" name="sel_upp" id="sel_upp"></select>
                            </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div style="clear:both"></div>
                        <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <label class="control-label">Unidad Responsable*</label>
                                <select class="form-control select2" name="sel_unidad_res" id="sel_unidad_res"></select>
                            </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div style="clear:both"></div>
                        <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <label class="control-label">Programa Presupuestario*</label>
                                <select class="form-control select2" name="sel_programa" id="sel_programa"></select>
                            </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div style="clear:both"></div>
                        <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <label class="control-label">Subprograma Presupuestario*</label>
                                <select class="form-control select2" name="sel_sub_programa" id="sel_sub_programa"></select>
                            </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-2"></div>
                        <div style="clear:both"></div>
                        <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <label class="control-label">Proyecto*</label>
                                <select class="form-control select2" name="sel_proyecto" id="sel_proyecto"></select>
                            </div>
                        <div class="col-md-2"></div>
                        <div style="clear:both"></div>
                        <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <label class="control-label">Linia de Acción</label>
                                <select class="form-control select2" name="sel_linea" id="sel_linea"></select>
                            </div>
                        <div class="col-md-2"></div>
                        <div style="clear:both"></div>
                        <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <label class="control-label">Periodo Presupuestario</label>
                                <select class="form-control select2" name="sel_periodo" id="sel_periodo"></select>
                            </div>
                        <div class="col-md-2"></div>
                        <div style="clear:both"></div>
                        <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <label class="control-label">Partida</label>
                                <select class="form-control select2" name="sel_partida" id="sel_partida"></select>
                            </div>
                        <div class="col-md-2"></div>
                        <div style="clear:both"></div>
                        <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <label class="control-label">sel_fondo</label>
                                <select class="form-control select2" name="sel_fondo" id="sel_fondo"></select>
                            </div>
                        <div class="col-md-2"></div>
                        <div style="clear:both"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button  id="cerrar" type="button" class="btn btn-secondary " data-dismiss="modal" aria-label="Close" onclick="dao.limpiarFormularioCrear()">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSave">Guardar</button>
            </div>
        </div>
    </div>
</div>

    <script src="/js/clavesP/init.js"></script>
    <script src="/js/utilerias.js"></script>
    <script>
        dao.getData();
        dao.getRegiones("");
    </script>
@endsection


