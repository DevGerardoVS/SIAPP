@extends('layouts.app')
@include('calendarización.metas.addActividad')
@isset($dataSet)
    @include('panels.datatable')
@endisset
@section('content')
    <div class="container">
        <form action="{{ route('proyecto') }}" id="buscarForm" method="GET">
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
                        <header class="d-flex justify-content-center" style=" border-bottom: 5px solid #17a2b8;">
                            <h2>Metas por proyecto</h2>
                        </header>
                        <br>
                        <div>
                            <div class="widget-body-toolbar">
                                <ul style="">
                                    <li><b>UR:</b></li>
                                    <li><b>Programa:</b></li>
                                    <li><b>Subprograma:</b></li>
                                    <li><b>Proyecto:</b></li>
                                </ul>
                            </div>
                        </div>
                        <br>
                        <div class="widget-body no-padding ">
                            <div class="table-responsive ">
                                <table id="catalogo" class="table table-hover table-striped ">
                                    <thead>
                                        <tr class="colorMorado">
                                            <th>Actividad</th>
                                            <th>Metas</th>
                                            <th>Tipo Actividad</th>
                                            <th>No. Beneficiarios</th>
                                            <th>Beneficiarios</th>
                                            <th>U. de medida</th>
                                            <th>Fondo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <br>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                data-target="#addActividad"data-backdrop="static" data-keyboard="false">Agregar
                                Actividad</button>
                        </div>
                    </div>
            </div>
    </div>
    </article>
    <a class="btn btn-primary btn-lg" href="/calendarizacion/metas" role="button">Regresar</a>
    <button type="button" class="btn btn-secondary btn-lg">Guardar</button>
    </div>
    </section>
    </div>
@endsection
