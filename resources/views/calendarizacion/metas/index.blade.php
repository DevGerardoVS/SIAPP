@extends('layouts.app')
@include('panels.datatable')
@include('calendarizacion.metas.modalCarga')
@section('content')
    <div class="container">
        <form action="{{ route('metasP') }}" id="buscarForm" method="POST">
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
                            <h2>Agregar Actividad</h2>
                        </header>
                        <br>
                        <div>
                            <div class="widget-body-toolbar">
                                <div class="row">
                                    <div class="col-md-6">
                                        <select class="form-control filters" id="ur_filter" name="ur_filter"
                                            autocomplete="ur_filter" placeholder="Seleccione una UR"
                                            data-live-search="true">
                                            <option value="" disabled selected>Seleccione una UR</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 d-flex " style="justify-content: flex-end">
                                        <div>
                                            <button type="button" class="btn btn-primary"  data-toggle="modal" data-target="#carga" data-backdrop="static" data-keyboard="false">Carga-Masiva</button>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                           
                        </div>
                        <br>
                        <div class="table table-responsive-lg d-flex justify-content-center">
                            <table id="catalogo">
                                <thead>
                                    <tr class="colorMorado">
                                        <th>Programas</th>
                                        <th>SubProgramas</th>
                                        <th class="subName">Proyecto</th>
                                        <th>Seleccion</th>
                                    </tr>
               
                            </table>
                        </div>
                    </div>
            </div>
            <br>
            @include('calendarizacion.metas.tableMetas')
            <div class="d-flex justify-content-center">
                <a type="button" class="btn btn-secondary" href="/calendarizacion/proyecto" onclick="dao.limpiar()">Actividades capturadas</a>
                &nbsp &nbsp
                <button id="btnSave" type="button" class="btn btn-primary">Guardar</button>
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
        init.validateCreate($('#actividad'));
        init.validateFile($('#formFile'));
    </script>
@endsection
