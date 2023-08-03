@extends('layouts.app')
@include('calendarizacion.metas.modalCarga')
@section('content')
    <div class="container">
        <input id='upp' type="text" style="display: none" value="{{ Auth::user()->clv_upp }}">
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
                                    @if (Auth::user()->id_grupo!= 4)
                                        <div class="col-md-4">
                                            <label class="control-label">UPP</label>
                                            <select class="form-control filters select2" id="upp_filter" name="upp_filter"
                                                autocomplete="upp_filter" placeholder="Seleccione una UR">
                                                <option value="" disabled selected>Seleccione una UPP</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="control-label">UR</label>
                                            <select class="form-control filters select2" id="ur_filter" name="ur_filter"
                                                autocomplete="ur_filter" placeholder="Seleccione una UR" disabled>
                                                <option value=""  selected>Seleccione una UR</option>
                                            </select>
                                        </div>
                                    @else
                                        <div class="col-md-4">
                                            <label class="control-label">UR</label>
                                            <select class="form-control filters select2" id="ur_filter" name="ur_filter"
                                                autocomplete="ur_filter" placeholder="Seleccione una UR">
                                                <option value="" disabled selected>Seleccione una UR</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4"></div>
                                    @endif
                                    @if (Auth::user()->id_grupo == 1 && Auth::user()->id_grupo == 4)
                                        @if (check_assignFront(1))
                                            <div class="col-md-4 d-flex " style="justify-content: flex-end">
                                                <div>
                                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                                        data-target="#carga" data-backdrop="static"
                                                        data-keyboard="false">Carga-Masiva</button>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        <br>
                        <div id="metasVista" class="row">
                            <div class="container">
                                <div class=" table table-responsive-lg d-flex justify-content-center">
                                    <table id="catalogo">
                                        <thead>
                                            <tr class="colorMorado">
                                                <th class="vertical sorting">Finalidad</th>
                                                <th class="vertical sorting">Función</th>
                                                <th class="vertical sorting">Subfunción</th>
                                                <th class="vertical sorting">Eje</th>
                                                <th class="vertical sorting">Linea de Accion</th>
                                                <th class="vertical sorting">Programa sectorial</th>
                                                <th class="vertical sorting">Tipologia CONAC</th>
                                                <th class="vertical sorting">Programas</th>
                                                <th class="vertical sorting">SubProgramas</th>
                                                <th class="vertical sorting">Proyecto</th>
                                                <th class="vertical sorting">Seleccion</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <br>
                            @include('calendarizacion.metas.tableMetas')
                            <div class="d-flex justify-content-center">
                                <a type="button" class="btn btn-secondary" href="/calendarizacion/proyecto"
                                    onclick="dao.limpiar()">Actividades capturadas</a>
                                &nbsp &nbsp
                                @if (Auth::user()->id_grupo == 1 && Auth::user()->id_grupo == 4)
                                    <button id="btnSave" type="button" class="btn btn-primary">Guardar</button>
                                @endif
                            </div>
                        </div>
                        <div id="incomplete" class="d-flex justify-content-center" style="display: none">
                            <div class="row">
                                    <i id="icono" aria-hidden="true" ></i>
                                    <div class="col-md-12">
                                        <h1 id="texto" class="d-flex justify-content-center"></h1>
                                    </div>
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
        init.validateCreate($('#actividad'));
        init.validateFile($('#formFile'));
    </script>
@endsection
