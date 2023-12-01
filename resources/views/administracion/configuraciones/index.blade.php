@extends('layouts.app')
@section('content')
@include('panels.datatable')
    <div class="container">
        <form action="{{ route('configuraciones') }}" id="buscarForm" method="Post">
            @csrf
            <input id="filter" name="filter" value="" style="display: none">
        </form>
        
        <section id="widget-grid">
            <div class="row">
                <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                    <div class="jarviswidget" id="wid-id-1" data-widget-editbutton="false" data-widget-colorbutton="false"
                        data-widget-deletebutton="false">
                        <header class="d-flex justify-content-center" style=" border-bottom: 5px solid #17a2b8;">
                            <h2>Configuraciones</h2>
                        </header>
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Tipo de actividad por UPP</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="autorizadas-tab" data-bs-toggle="tab" data-bs-target="#auto" type="button" role="tab" aria-controls="profile" onclick="adjustTableColumns()" aria-selected="false">UPP's autorizadas para RR HH</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reportesCarga-tab" data-bs-toggle="tab" data-bs-target="#carga" type="button" role="tab" aria-controls="profile" onclick="" aria-selected="false">Archivos de carga</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <br>
                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                
                                <div class="jarviswidget-editbox">
                                </div>
                                <div class="widget-body-toolbar">
                                    <div class="row">
                                        <div class="col-xs-9 col-sm-5 col-md-5 col-lg-5">
                                        </div>
                                        <div class="col-xs-3 col-sm-7 col-md-7 col-lg-7 text-right">
                                            <select id="upps" class="form-select">
                                                <option value="">Todas las UPP's</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <table id="catalogo" class="table table-striped table-bordered text-center table-a"
                                    style="width:100%">
                                    <thead>
                                        <tr class="colorMorado">
                                            <th>Clave UPP</th>
                                            <th>UPP</th>
                                            <th>Acumulativa</th>
                                            <th>Continua</th>
                                            <th>Especial</th>
                                        </tr>
                                    </thead>
                                </table>
                                
                            </div>
                            <div class="tab-pane fade" id="auto" role="tabpanel" aria-labelledby="autorizadas-tab">
                                <div class="widget-body-toolbar">
                                    <div class="row">
                                        <div class="col-xs-9 col-sm-5 col-md-5 col-lg-5">
                                        </div>
                                        <div class="col-xs-3 col-sm-7 col-md-7 col-lg-7 text-right">
                                            <input id="filter_auto" name="filter_auto" value="" style="display: none">
                                            <select id="upps_auto" class="form-select">
                                                <option value="">Todas las UPP's</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <table id="catalogo_b" class="table table-striped table-bordered text-center table-b"
                                    style="width:100%">
                                    <thead>
                                        <tr class="colorMorado">
                                            <th width="20%">Clave UPP</th>
                                            <th width="50%">UPP</th>
                                            <th width="30%">Autorizadas para delegación</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            {{-- se agrega un nuevo tab para los archivos de carga --}}
                            <div class="tab-pane fade" id="carga" role="tabpanel" aria-labelledby="reportesCarga-tab">
                                <div class="widget-body-toolbar">
                                </div>
                                <br>
                                <table id="archivosCarga-table" class="table table-striped table-bordered text-center table-b"
                                    style="width:100%">
                                    <thead>
                                        <tr class="colorMorado">
                                            <th width="20%">#</th>
                                            <th width="50%">Archivo</th>
                                            <th width="30%">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Áreas funcionales</td>
                                            <td><a data-toggle="tooltip" title="Descargar" class="btn btn-sm btn-success" href="/archivo-areas-funcionales" ><i class="fa fa-file-excel-o" style="color: aliceblue"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Fondos</td>
                                            <td><a data-toggle="tooltip" title="Descargar" class="btn btn-sm btn-success" href="" ><i class="fa fa-file-excel-o" style="color: aliceblue"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Centro de costos  / Centro de beneficios </td>
                                            <td><a data-toggle="tooltip" title="Descargar" class="btn btn-sm btn-success" href="" ><i class="fa fa-file-excel-o" style="color: aliceblue"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>Centro gestor</td>
                                            <td><a data-toggle="tooltip" title="Descargar" class="btn btn-sm btn-success" href="" ><i class="fa fa-file-excel-o" style="color: aliceblue"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <td>Pospre</td>
                                            <td><a data-toggle="tooltip" title="Descargar" class="btn btn-sm btn-success" href="" ><i class="fa fa-file-excel-o" style="color: aliceblue"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>6</td>
                                            <td>Claves presupuestales</td>
                                            <td><a data-toggle="tooltip" title="Descargar" class="btn btn-sm btn-success" href="" ><i class="fa fa-file-excel-o" style="color: aliceblue"></i></a></td>
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
    <script src="/js/administracion/configuraciones/init.js"></script>
    <script src="/js/utilerias.js"></script>
    <script>
        //En las vistas solo se llaman las funciones del archivo init
        init.validateCreate($('#frmCreate'));

        
    </script>
@endsection