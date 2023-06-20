@extends('layouts.app')
@include('panels.datatable')
@section('content')
<link href="https://cdn.datatables.net/v/dt/dt-1.13.4/rg-1.3.1/datatables.min.css" rel="stylesheet"/>
<script src="https://cdn.datatables.net/v/dt/dt-1.13.4/rg-1.3.1/datatables.min.js"></script>
    <div class="container">
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
                                        <label for="asignadoUpp">Asignado:</label>
                                        <input type="text" id="asignadoUpp" name="asignadoUpp" class="form-control" disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="asignadoUpp">Calendarizado:</label>
                                        <input type="text" id="calendarizado" name="calendarizado" class="form-control" disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="asignadoUpp">Disponible:</label>
                                        <input type="text" id="disponibleUpp" name="disponibleUpp" class="form-control" disabled>
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <label for="buttonPresupuesto">&nbsp;</label>
                                            <a type="button" class="btn btn-success form-control"  href="/calendarizacion-claves-presupuesto-fondo" ><i class="fa fa-eye">Presupuesto por Fondo</i></a>
                                    </div>
                                    <div class="col-md-2"></div>
                                    <div class="col-md-2 text-right">
                                        <div class="row">
                                            <button type="button" class="btn colorMorado"
                                            name="button_modal_carga" id="button_modal_carga">
                                            <i class="fas fa-plus">{{__("messages.carga_masiva")}} </i>
                                    
                                        </div>
                                        
                                        <div class="row">
                                            <label for="buttonBtnNew">&nbsp;</label>
                                                <a type="button" class="btn btn-success form-control"  href="/calendarizacion-claves-create" ><i class="fa fa-plus">Nueva Clave</i></a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <br><br>
                            
                                <div class="table-responsive">
                                    <table id="claves" class="table table-hover table-striped" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th class="centro-gestor">Clasificacion</th>
                                                <th class="centro-gestor">Centro Gestor</th>
                                                <th class="area-funcional">Area Funcional</th>
                                                <th class="periodo-presupuestal">Periodo</th>
                                                <th class="clasificacion-economica">Posicion</th>
                                                <th class="fondo">Fondo</th>
                                                <th class="inversion">Proyecto</th>
                                                <th class="colorMorado">Total</th>
                                                <th class="colorMorado">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="centro-gestor"></td>
                                                <td class="centro-gestor"></td>
                                                <td class="area-funcional"></td>
                                                <td class="periodo-presupuestal"></td>
                                                <td class="clasificacion-economica"></td>
                                                <td class="fondo"></td>
                                                <td class="inversion"></td>
                                                <td class="colorMorado"></td>
                                                <td class="colorMorado"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            
                        </div>
                    </div>
                </article>
            </div>
        </section>
    </div>
     @include('clavePresupuestaria.CargamasivaModal')
    <script src="/js/clavesP/init.js"></script>
    <script src="/js/utilerias.js"></script>
    <script src="/js/clavesP/cargamasiva.js"></script>

    <script>
        dao.getData();
        dao.getRegiones("");
        dao.getUpp("");
        dao.getPresupuesAsignado();
    </script>
@endsection


