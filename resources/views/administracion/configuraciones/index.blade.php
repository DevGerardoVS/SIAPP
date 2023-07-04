@extends('layouts.app')
@section('content')
@include('panels.datatable')
    <div class="container">
        <form action="{{ route('configuraciones') }}" id="buscarForm" method="Post">
            @csrf
        </form>
        <section id="widget-grid">
            <div class="row">
                <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                    <div class="jarviswidget" id="wid-id-1" data-widget-editbutton="false" data-widget-colorbutton="false"
                        data-widget-deletebutton="false">
                        <header>
                            <h2>Configuraciones</h2>
                        </header>
                        <div>
                            <div class="jarviswidget-editbox">
                            </div>
                            <div class="widget-body-toolbar">
                                <div class="row">
                                    <div class="col-xs-9 col-sm-5 col-md-5 col-lg-5">
                                    </div>
                                    <div class="col-xs-3 col-sm-7 col-md-7 col-lg-7 text-right">
                                        <select class="form-select">
                                            <option>Todas las UPP's</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <br>
                            <table id="catalogo" class="table table-striped table-bordered text-center "
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
                    </div>
                </article>
            </div>
        </section>
    </div>
    <script src="/js/administracion/grupos/init.js"></script>
    <script src="/js/utilerias.js"></script>
    <script>
        //En las vistas solo se llaman las funciones del archivo init
        init.validateCreate($('#frmCreate'));
    </script>
@endsection