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
            <label id="validMetas"></label>
        </div>
        <br>
        <ul class="nav nav-pills nav-fill BorderPink" id="tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link BorderNavPink active" id="metas-tab" data-bs-toggle="tab" data-bs-target="#metas"
                    type="button" role="tab" aria-controls="metas" aria-selected="false">Carga masiva metas</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link BorderNavPink" id="capturadas-tab" data-bs-toggle="tab" data-bs-target="#capturadas"
                    type="button" role="tab" aria-controls="capturadas" aria-selected="false">Metas capturadas</button>
            </li>        
        </ul>
        &nbsp;
        <div class="row">
            <div class="col-md-12">
                @if (Auth::user()->id_grupo == 4 || Auth::user()->id_grupo == 5)
                    <button type="button" class="btn btn-outline-primary float-right confirmacion botones_exportar"
                        onclick="dao.ConfirmarMetas()" style="display: none"><i class="fa fa-check-square-o"
                            aria-hidden="true"></i>&nbsp;Confirmar Metas</button>
                @endif
                <button type="button" onclick="dao.exportPdf()"class="btn btn-outline-danger botones_exportar"
                    style="display: none"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Exportar
                    PDF</button>&nbsp
                <button type="button" onclick="dao.exportExcel()" class="btn btn-outline-success botones_exportar"
                    style="display: none"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Exportar
                    Excel</button> &nbsp
                {{--  <button type="button" class="btn btn-outline-primary float-right" onclick="dao.DesconfirmarMetas()" ><i class="fa fa-check-square-o" aria-hidden="true"></i>&nbsp;Confirmar Metas</button>  --}}
            </div>
        </div>
        &nbsp;
        <div class="row">
            <div class="col-md-2">
                <label class="control-label">AÑO</label>
                <select class="form-control select2" id="anio_filter" name="anio_filter" autocomplete="anio_filter"
                    placeholder="Seleccione un año">getEjercicios
                    <option value="" disabled selected>Seleccione un año</option>
                    @foreach (getEjercicios() as $a)
                        <option value="{{ $a->ejercicio }}" selected>{{ $a->ejercicio }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="control-label">UPP</label>
                <select class="form-control select2" id="upp_filter" name="upp_filter" autocomplete="upp_filter"
                    placeholder="Seleccione una UR">
                    <option value="0" disabled selected>Seleccione una UPP</option>
                </select>
            </div>
            {{--
            <div class="col-md-4">
                <label class="control-label">UR</label>
                <select class="form-control select2" id="ur_filter" name="ur_filter" autocomplete="ur_filter"
                    placeholder="Seleccione una UR" disabled>
                    <option value="0" selected>Seleccione una UR</option>
                </select>
            </div>
        </div> --}}
            <div class="tab-content">
                <!--ss Metas Carga Masiva-->
                <div class="tab-pane active" id="metas" role="tabpanel" aria-labelledby="metas-tab"
                    style="min-width: 100%">
                    <div class="row">

                        <div class="d-inline-flex p-2">
                            <div>
                                <form id="formFile">
                                    @csrf
                                    <br>
                                    <div class="wrap1">
                                        <label><b>Lea las instrucciones para asegurar el funcionamiento correcto del
                                                proceso:</b></label>
                                        <ul style="width:75%; float:left;">
                                            <li><b>Asegúrese de utilizar la plantilla</b> para el correcto funcionamiento de
                                                la carga
                                                masiva.</li>
                                            <li>Debe llenar <b>todos</b> las columnas, para esto puede apoyarse con los
                                                catalogos que se
                                                encuentran en las otras pestañas.</li>
                                            <li>El numero de beneficiarios debe ser <b>mayor a cero</b>.</li>
                                            <li><b>Agregar las filas necesarias</b>.</li>
                                            <li>Solo se pueden llenar los meses que estan registrados en <b>calendarización
                                                    de
                                                    claves</b>.</li>
                                            <li>Para el subprograma <b>UUU</b> se registran automaticamnete en el sistema el
                                                total y los
                                                meses predeterminados.</li>
                                        </ul>
                                        <button id="CargaMasiva" type="button" class="btn btn-outline-primary text-center"
                                            style="float:left;text-decoration:none; width:20%;"
                                            onclick="dao.getPlantillaCmUpp()"><i class="fa fa-check-square-o"
                                                aria-hidden="true"></i>&nbsp; Descargar plantilla</button>
                                        <input name="cmFile" type="file" id="cmFile" name="cmFile"
                                            accept=".xlsx,.xlsm" class="border border-secondary rounded"
                                            placeholder="Archivo" required style="margin-top:5%; width : 100%;">
                                        <span id="cmFile-error"></span>
                                        <br>
                                    </div>
                                    <br>
                                </form>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center">
                            <button id='boton_cargaMasiva' type="button" onclick="dao.save()" class="btn btn-primary boton_cargaMasiva" style="display: none"><i class="fa fa-save"
                                    aria-hidden="true"></i> &nbsp;Guardar</button>
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
        <script src="/js/calendarizacion/metas/initDel.js"></script>
        <script src="/js/utilerias.js"></script>
        <script>
            init.validateFile($('#formFile'));
            init.validateCreate($('#actividad'));
        </script>
        <script src="/js/calendarizacion/metas/validationES.js"></script>
    @endsection
