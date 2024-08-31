@extends('layouts.app')
@section('content')
    @include('calendarizacion.metas.modalCarga')
    @include('calendarizacion.metas.addActividad')
    @include('calendarizacion.metas.actividadContinua')
    <div class="container">
        <div class="row">
            <header class="d-flex justify-content-center" style=" border-bottom: 5px solid #17a2b8;">
                <h2>Agregar Actividad</h2>
            </header>
            &nbsp;
            <label id="validMetas"  ></label>    
            <div class="col-md-2">
                <label class="control-label">AÑO</label>
                <select class="form-control filters select2" id="anio_filter" name="anio_filter" autocomplete="anio_filter"
                    placeholder="Seleccione un año">getEjercicios
                    <option value="" disabled selected>Seleccione un año</option>
                    @foreach (getEjercicios() as $a)
                        <option value="{{ $a->ejercicio }}" selected>{{ $a->ejercicio }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="control-label">UPP</label>
                <select class="form-control filters select2" id="upp_filter" name="upp_filter" autocomplete="upp_filter"
                    placeholder="Seleccione una UR">
                    <option value="0" disabled selected>Seleccione una UPP</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="control-label">UR</label>
                <select class="form-control filters select2" id="ur_filter" name="ur_filter" autocomplete="ur_filter"
                    placeholder="Seleccione una UR" disabled>
                    <option value="0" selected>Seleccione una UR</option>
                </select>
            </div>
            {{-- if para no alinear la vista en caso de que este deshabilitada la carga masiva --}}
            @if (!check_assignFrontCM('Actividades'))
                <div class="col-md-2">
                    <br>
                    <button type="button" style="justify-content: float-right;" onclick="dao.exportExcel()"
                        class="btn btn-outline-success"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Exportar
                        Excel</button>
                </div>
            @endif
        </div>
        <br>
        <ul class="nav nav-tabs BorderPink" id="tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link BorderNavPink active" id="metas-tab" data-bs-toggle="tab" data-bs-target="#metas"
                        type="button" role="tab" aria-controls="metas" aria-selected="false">Agregar
                        actividades</button>
                </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link BorderNavPink" id="capturadas-tab" data-bs-toggle="tab" data-bs-target="#capturadas"
                    type="button" role="tab" aria-controls="capturadas" aria-selected="false">Metas capturadas</button>
            </li>
            {{-- if de carga masiva  --}}
            @if (check_assignFrontCM('Actividades'))
                <div class="col-md-4"></div>
                <div class="row">
                    <div class="col-md-4"></div>
                    @if (checkAcces('Carga masiva'))
                        <div class="col-md-4">
                            <button type="button" id="btn_cargaMasiva"
                                class="btn btn-outline-primary float-right CargaMasiva" data-toggle="modal"
                                data-target="#carga" data-backdrop="static" style="display: none">Carga Masiva</button>
                        </div>
                    @else
                        <div class="col-md-4"></div>
                    @endif
                    <div class="col-md-4">
                        <button id="btn_exportMetas" type="button" style="justify-content: float-right;display: none;"
                            onclick="dao.exportExcel()" class="btn btn-outline-success"><i class="fa fa-file-excel-o"
                                aria-hidden="true"></i> Exportar
                            Excel</button>
                    </div>
                </div>
            @endif
        </ul>
        <br>
        <div class="tab-content">
            <!--ss Metas-->
            <div class="tab-pane active" id="metas" role="tabpanel" aria-labelledby="metas-tab" style="min-width: 100%">
                <div class="row">
                    <input id='upp' type="text" style="display: none" value="{{ Auth::user()->clv_upp }}">
                    <input id='area' type="text" style="display: none">
                    <input id='conmir' type="text" style="display: none">
                    <input id='nomir' type="text" style="display: none">
                    <input id='calendar' type="text" style="display: none">
                    <input id='activiMir' type="text" style="display: none">
                    <input id='tipoAct' type="text" style="display: none">
                    <label id="validMetas"></label>

                    <div id="metasVista" class="row">
                        <div class="container">
                            <div class=" table table-responsive-lg d-flex justify-content-center">
                                <table id="entidad">
                                    <thead>
                                        <tr>
                                            <th class="vertical colorMorado sorting">Finalidad</th>
                                            <th class="vertical colorMorado sorting">Función</th>
                                            <th class="vertical colorMorado sorting">Subfunción</th>
                                            <th class="vertical colorMorado sorting">Eje</th>
                                            <th class="vertical colorMorado sorting">Linea de acción</th>
                                            <th class="vertical colorMorado sorting">Programa sectorial</th>
                                            <th class="vertical colorMorado sorting">Tipologia CONAC</th>
                                            <th class="vertical colorMorado sorting">Programas</th>
                                            <th class="vertical colorMorado sorting">SubProgramas</th>
                                            <th class="vertical colorMorado sorting">Proyecto</th>
                                            <th class="vertical colorMorado sorting">Fondo(s)</th>
                                            <th class="vertical colorMorado sorting">Metas</th>
                                            <th class="vertical" style="background-color:#afafaf;">Selección </th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyclaves"></tbody>
                                </table>
                            </div>
                        </div>
                        <br>
                        @include('calendarizacion.metas.tableMetas')
                        <div class="d-flex justify-content-center">
                            @if (Auth::user()->id_grupo == 1 || Auth::user()->id_grupo == 4)
                                <button id="btnSave" type="button"
                                    class="btn btn-primary btn-lg btnSave">Guardar</button>
                            @endif
                        </div>
                    </div>
                    <div id="incomplete" class="d-flex justify-content-center" style="display: none">
                        <div class="row">
                            <i id="icono" aria-hidden="true"></i>
                            <div class="col-md-12">
                                <h1 id="texto" class="d-flex justify-content-center"></h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--ss Capturadas-->
            <div class="tab-pane" id="capturadas" role="tabpanel" aria-labelledby="capturadas-tab"
                style="min-width: 100%">
                <div class="widget-body no-padding ">
                    <div class="table-responsive ">
                        &nbsp
                        <table id="proyectoM" class="table table-hover table-striped">
                            <thead style="visibility: visible !important">
                                <tr class="colorMorado">
                                    <th class="vertical">ID</th>
                                    <th class="vertical">Finalidad</th>
                                    <th class="vertical">Función</th>
                                    <th class="vertical">Subfunción</th>
                                    <th class="vertical">Eje</th>
                                    <th class="vertical">Linea de Accion</th>
                                    <th class="vertical">Programa sectorial</th>
                                    <th class="vertical">Tipologia CONAC</th>
                                    <th class="vertical">UPP</th>
                                    <th class="vertical">UR</th>
                                    <th class="vertical">Programa</th>
                                    <th class="vertical">Subprograma</th>
                                    <th class="vertical">Proyecto</th>
                                    {{--   <th class="vertical">Partida</th> --}}
                                    <th class="vertical">Fondo</th>
                                    <th class="vertical">Actividad</th>
                                    <th class="vertical">Tipo Actividad</th>
                                    <th class="vertical">Meta anual</th>
                                    <th class="vertical"># Beneficiarios</th>
                                    <th class="vertical">Beneficiarios</th>
                                    <th class="vertical">U de medida</th>
                                    <th class="vertical">Acciones</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/js/utilerias.js"></script>
    <script src="/js/calendarizacion/metas/newInit.js"></script>
    <script>
        init.validateFile($('#formFile'));
        init.validateCont($('#formContinua'));
    </script>
    <script src="/js/calendarizacion/metas/validationES.js"></script>
@endsection
