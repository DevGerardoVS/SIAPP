@extends('layouts.app')
@include('panels.datatable')
@section('content')
    <div class="container">
        <form action="{{ route('getMetas') }}" id="buscarForm" method="GET">
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
                            <h2>Calendarización de metas</h2>
                        </header>
                        <br>
                        <div>
                            <div class="widget-body-toolbar">
                                <div class="row">
                                        <div class="col-md-2">
                                            <select class="form-control filters" id="anio_filter" name="anio_filter"
                                                autocomplete="anio_filter" placeholder="Seleccione un año">
                                            </select>
                                        </div>
                                            <div class="col-md-2">
                                                <select class="form-control filters" id="ur_filter" name="ur_filter"
                                                    autocomplete="ur_filter" placeholder="Seleccione una UR">
                                                    <option value="" disabled selected>Seleccione una UR</option>
                                                    <option value="2022">002</option>
                                                    <option value="2023">003</option>
                                                    <option value="2024">004</option>
                                                    <option value="2025">005</option>
                                                </select>
                                            </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="widget-body no-padding ">
                            <div class="table-responsive ">
                                <table id="catalogo" class="table table-hover table-striped ">
                                    <thead>
                                        <tr class="colorMorado">
                                            <th>UR</th>
                                            <th>Programas</th>
                                            <th>SubProgramas</th>
                                            <th>Proyecto</th>
                                            <th>Fondo</th>
                                            <th>Presupuesto</th>
                                            <th>Actividad</th>
                                            <th>Meta</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
            </div>
    </div>
    </article>
    </div>
    </section>
    </div>
    <script src="/js/calendarizacion/metas/init.js"></script>
    <script src="/js/utilerias.js"></script>
    <script>
        //En las vistas solo se llaman las funciones del archivo init
        init.validateCreate($('#frm_create'));
    </script>
@endsection
