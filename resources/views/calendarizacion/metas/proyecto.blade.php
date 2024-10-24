@extends('layouts.app')
@include('calendarizacion.metas.addActividad')
@include('calendarizacion.metas.modalFirmaElectronica')
@include('calendarizacion.metas.actividadContinua')
@section('content')
    <div class="container">
       
        <input id='upp' type="text" style="display: none" value="{{ Auth::user()->clv_upp }}">
        <input id='ar' type="text" style="display: none">
        <input id='fondo' type="text" style="display: none">
        <input id='subp' type="text" style="display: none">
        <section id="widget-grid" class="conteiner">
            <div class="row">
                <div>
                    <header class="d-flex justify-content-center"
                        style=" border-bottom: 5px solid #17a2b8; margin-bottom: 5px;">
                        <h2>Proyectos con actividades</h2>
                    </header>
                </div>
                    <label id="validMetas"  ></label>
                <div class="row">

                    @if (Auth::user()->id_grupo != 4)
                    <div class="col-md-4">
                        <label class="control-label">UPP</label>
                        <select class="form-control filters select2" id="upp_filter" name="upp_filter"
                            autocomplete="upp_filter" placeholder="Seleccione una UR">
                            <option value="" disabled selected>Seleccione una UPP</option>
                        </select>
                    </div>
                    <div class="col-md-2 ">
                        <label class="control-label">AÑO</label>
                        <select class="form-control filters select2" id="anio_filter" name="anio_filter"
                            autocomplete="anio_filter" placeholder="Seleccione un año">
                            <option value="" disabled selected>Seleccione un año</option>
                        </select>
                    </div>
                    <div class="col-md-2"></div>
                    @else
                    <div class="col-md-2">
                        <label class="control-label">AÑO</label>
                        <select class="form-control filters select2" id="anio_filter" name="anio_filter"
                            autocomplete="anio_filter" placeholder="Seleccione un año">
                            <option value="" disabled selected>Seleccione un año</option>
                        </select>
                    </div>
                    <div class="col-md-6 "></div>
                    @endif    

                    <div class="col-md-4 text-right">
                        <br>
                        <a type="button" class="btn btn-success col-md-5 ml-auto cierreMetas" href="{{ route('index_metas') }}" style="justify-content: float-right;">
                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Agregar Actividad
                        </a>
                    </div>
                </div>
                <div class="row">
                    @if(Auth::user()->id_grupo == 1 || Auth::user()->id_grupo == 4)
                        <div class="col-md-12">
                            <br>
                                @if (Auth::user()->id_grupo == 4)
                                <button  type="button" class="btn btn-outline-primary col-md-2 cmupp" onclick="dao.exportJasperMetas()">Formato Metas</button>&nbsp;
                                <button  type="button" class="btn btn-outline-primary col-md-2 cmupp" onclick="dao.exportJasper()">Formato claves</button>&nbsp;
                                @endif
                                
                                <button type="button" style="justify-content: flex-end; " onclick="dao.exportPdf()"class="btn btn-outline-danger col-md-2"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Exportar PDF</button>&nbsp
                                <button type="button" style="justify-content: float-right;" onclick="dao.exportExcel()" class="btn btn-outline-success col-md-2"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Exportar Excel</button> &nbsp
                                @if ( Auth::user()->id_grupo == 4 || Auth::user()->id_grupo == 5 )
                                <button type="button" class="btn btn-outline-primary float-right confirmacion" onclick="dao.ConfirmarMetas()" style="display: none"><i class="fa fa-check-square-o" aria-hidden="true"></i>&nbsp;Confirmar Metas</button>
                                @endif
                               {{--  <button type="button" class="btn btn-outline-primary float-right" onclick="dao.DesconfirmarMetas()" ><i class="fa fa-check-square-o" aria-hidden="true"></i>&nbsp;Confirmar Metas</button>  --}}

                            </div>   
                    @endif
                </div>
                &nbsp
                <div class="widget-body no-padding ">
                    <div class="table-responsive ">
                        &nbsp
                        <table id="proyectoM" class="table table-hover table-striped">
                            <thead style="visibility: visible !important" >
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
        </section>
    </div>
    <div id="containerFile">
    </div>
    <script src="/js/calendarizacion/metas/initActiv.js"></script>
    <script src="/js/utilerias.js"></script>
    <script>
        //En las vistas solo se llaman las funciones del archivo init

        init.validateCreate($('#actividad'));
    </script>
@endsection
