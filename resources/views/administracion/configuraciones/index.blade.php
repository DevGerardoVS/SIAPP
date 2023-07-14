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
                        <header>
                            <h2>Configuraciones</h2>
                        </header>
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Tipo de actividad por UPP</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="autorizadas-tab" data-bs-toggle="tab" data-bs-target="#auto" type="button" role="tab" aria-controls="profile" onclick="adjustTableColumns()" aria-selected="false">UPP's autorizadas para delegación</button>
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