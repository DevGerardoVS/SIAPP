<?php
    $titleDesc = "Reportes Administrativos";
    
?>

@extends('layouts.app')
@section('content')

    <div class="mx-auto p-4" style="width:90%;">

        <header class="d-flex justify-content-center" style=" border-bottom: 5px solid #17a2b8; margin-bottom: 5px;">
            <h2>{{ $titleDesc }}</h2>
        </header>
        @if(Auth::user()->id_grupo == 4)
            @foreach ($upps as $upp)
                <h6 class="text-center">@if(Auth::user()->clv_upp == $upp->clave) {{$upp->clave}} {{$upp->descripcion}} @endif</h6>
            @endforeach
        @endif
        <form action="{{route('calendario_fondo_mensual')}}" id="buscarFormA" name="analisis" method="post"> </form>
        <form action="{{route('resumen_capitulo_partida')}}" id="buscarFormB" name="analisis" method="post"><input type="text" id="catalogoB_val"  style="display: none"></form> 
        <form action="{{route('proyecto_avance_general')}}" id="buscarFormC" name="analisis" method="post"><input type="text" id="catalogoC_val"  style="display: none"></form> 
        <form action="{{route('proyecto_calendario_general')}}" id="buscarFormD" name="analisis" method="post"><input type="text" id="catalogoD_val"  style="display: none"></form> 
        <form action="{{route('proyecto_calendario_general_actividad')}}" id="buscarFormE" name="analisis" method="post"><input type="text" id="catalogoE_val"  style="display: none"></form> 
        <form action="{{route('avance_proyecto_actividad_upp')}}" id="buscarFormF" name="analisis" method="post"><input type="text" id="catalogoF_val"  style="display: none"></form> 

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <h4>{{$errors->first()}}</h4>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        
        {{-- Form para descargar el archivo y cambiar los select --}}
        <form id="form" method="POST">
            @csrf
            <div class="col-md-10 col-sm-12 d-md-flex mt-5">
                <div class="col-sm-3 col-md-3 col-lg-2 text-md-end">
                    <label for="anio_filter" class="form-label fw-bold mt-md-1">Año: </label>
                </div>
                <div class="col-sm-12 col-md-3 col-lg-2">
                    <select class="form-control filters filters_anio" id="anio_filter" name="anio_filter" autocomplete="anio_filter">
                        @foreach ($anios as $anio)
                            <option value={{$anio->ejercicio}}>{{$anio->ejercicio}}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-sm-3 col-md-3 col-lg-2 text-md-end">
                    <label for="fechaCorte_filter" class="form-label fw-bold mt-md-1">Fecha de corte:</label>
                </div>
                <div class="col-sm-12 col-md-3 col-lg-2">
                    <select class="form-control filters filters_fechaCorte" id="fechaCorte_filter" name="fechaCorte_filter" autocomplete="fechaCorte_filter">
                    </select>
                </div>
            </div>
            @if(Auth::user()->id_grupo == 1)
                <div class="col-md-10 col-sm-12 d-md-flex mt-2 ">
                    <div class="col-sm-3 col-md-3 col-lg-2 text-md-end d-none div_upp">
                        <label for="upp_filter" class="form-label fw-bold mt-md-1">UPP:</label>
                    </div>
                    <div class="col-md-6 col-sm-12 d-none div_upp">
                        <select class="form-control filters filters_upp" id="upp_filter" name="upp_filter" autocomplete="upp_filter">
                            <option value="">Todos</option>
                            @foreach ($upps as $upp)
                                <option value={{$upp->clave}} {{$upp->descripcion}}>{{$upp->clave}} {{$upp->descripcion}}</option>
                            @endforeach
                        </select>
                    </div>
                        
                </div>
            @endif
            {{-- botones de descarga --}}
            <div class="d-flex flex-wrap justify-content-end mb-5 mt-sm-2 mt-lg-0">
                <button id="btnPDF" type="submit" formtarget="_blank" class="btn btn-light btn-sm btn-labeled me-3 btn_click" style="border-color: #6a0f49;" title="Generar Reporte PDF" name="action" value="pdf">
                    <span class="btn-label"><i class="fa fa-file-pdf-o text-danger fs-4 align-middle"></i></span>
                    <span class="d-lg-inline align-middle" style="color:#6a0f49; font-size: 1rem">Exportar a PDF</span> 
                </button>
                <button id="btnExcel" type="submit" formtarget="_blank" class="btn btn-light btn-sm btn-labeled btn_click" style="border-color: #6a0f49;" title="Generar Reporte Excel" name="action" value="xls">
                    <span class="btn-label"><i class="fa fa-file-excel-o text-success fs-4 align-middle"></i></span>
                    <span class="d-lg-inline align-middle" style="color:#6a0f49; font-size: 1rem">Exportar a Excel</span>
                </button>
            </div>
        </form>
        
        <br>

        <ul class="nav nav-tabs " id="tabs" role="tablist">
            @if(Auth::user()->id_grupo == 1)
            <li class="nav-item" >
                <button class="nav-link textoMorado @if(Auth::user()->id_grupo == 1) active @endif" role="tab" type="button" id="fondoMensual_tab" data-bs-toggle="tab" data-bs-target="#fondoMensual" aria-controls="fondoMensual" @if(Auth::user()->id_grupo == 1)aria-selected="true"@endif>Calendario fondo mensual</button>
            </li>
            <li class="nav-item" >
                <button class="nav-link textoMorado" role="tab" type="button" id="capituloPartida_tab" data-bs-toggle="tab" data-bs-target="#capituloPartida" aria-controls="capituloPartida" aria-selected="false">Resumen capítulo y partida</button>
            </li>
            <li class="nav-item" >
                <button class="nav-link textoMorado" role="tab" type="button" id="avanceGeneral_tab" data-bs-toggle="tab" data-bs-target="#avanceGeneral" aria-controls="avanceGeneral" aria-selected="false">Proyecto avance general</button>
            </li>
            @endif
            <li class="nav-item" >
                <button class="nav-link textoMorado @if(Auth::user()->id_grupo == 4) active @endif" role="tab" type="button" id="calendarioGeneral_tab" data-bs-toggle="tab" data-bs-target="#calendarioGeneral" aria-controls="calendarioGeneral" @if(Auth::user()->id_grupo == 4)aria-selected="true"@endif>Proyecto calendario general</button>
            </li>
            <li class="nav-item" >
                <button class="nav-link textoMorado" role="tab" type="button" id="calendarioGeneralActividad_tab" data-bs-toggle="tab" data-bs-target="#calendarioGeneralActividad" aria-controls="calendarioGeneralActividad" aria-selected="false">Proyecto calendario general de actividades</button>
            </li>
            @if(Auth::user()->id_grupo == 1)
            <li class="nav-item" >
                <button class="nav-link textoMorado" role="tab" type="button" id="avanceProyectoActividadUPP_tab" data-bs-toggle="tab" data-bs-target="#avanceProyectoActividadUPP" aria-controls="avanceProyectoActividadUPP" aria-selected="false">Avance de proyectos con actividades por UPP</button>
            </li>
            @endif
        </ul>

        <div class="tab-content" style="font-size: 14px;">
            {{-- fondo mensual A--}}
            @if(Auth::user()->id_grupo == 1)
            <div class="tab-pane @if(Auth::user()->id_grupo == 1) active @endif" id="fondoMensual" role="tabpanel" aria-labelledby="fondoMensual_tab" >    
                <div class="row mx-auto">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="tableRowStyle table table-hover table-bordered order-table text-center tableSize align-middle"
                                    id="catalogoA" data-right="2,3,4,5,6,7,8,9,10,11,12,13,14" data-left="0,1" data-center="0,1,2,3,4,5,6,7,8,9,10,11,12,13,14" style="width:100%; font-size: 14px;">
                                    <thead class="colorMorado">
                                        <tr>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Ramo</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Fondo</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Enero</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Febrero</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Marzo</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Abril</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Mayo</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Junio</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Julio</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Agosto</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Septiembre</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Octubre</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Noviembre</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Diciembre</th>
                                            <th class="exportable align-middle text-light sum" style="text-align: center !important">Importe total</th>
                                        </tr>
                                    </thead>
                                    <tfoot class="colorMorado">
                                        <tr>
                                            <td class="align-middle text-start" colspan="14">TOTAL</td>
                                            <td class="align-middle text-end total" style="width: 20em;" id="total"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Capitulo y partida B-->
            <div class="tab-pane" id="capituloPartida" role="tabpanel" aria-labelledby="capituloPartida_tab" >
                <div class="row mx-auto">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="tableRowStyle table table-hover table-bordered order-table text-center tableSize align-middle"
                                    id="catalogoB" data-right="2" data-left="0,1" data-center="0,1,2" style="width:100%; font-size: 14px;">
                                    <thead  class="colorMorado">
                                        <tr>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Capítulo</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Partida</th>
                                            <th class="exportable align-middle text-light sum" style="text-align: center !important">Importe</th>
                                        </tr>
                                    </thead>
                                    <tfoot class="colorMorado">
                                        <tr>
                                            <td class="align-middle text-start" colspan="2">TOTAL</td>
                                            <td class="align-middle text-end total" style="width: 20em;" id="total"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             {{-- Proyecto avance general C--}}
             <div class="tab-pane" id="avanceGeneral" role="tabpanel" aria-labelledby="avanceGeneral_tab" >    
                <div class="row mx-auto" >
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="tableRowStyle table table-hover table-bordered order-table text-center tableSize align-middle"
                                    id="catalogoC" data-right="3,4,5,6" data-left="0,1,2,7" data-center="0,1,2,3,4,5,6,7" style="width:100%; font-size: 14px;">
                                    <thead class="colorMorado">
                                        <tr>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Unidad programática presupuestaría</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Fondo</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Capítulo</th>
                                            <th class="exportable align-middle text-light sum" style="text-align: center !important">Monto anual</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Calendarizado</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Disponible</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">% de avance</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Estatus</th>
                                        </tr>
                                    </thead>
                                    <tfoot class="colorMorado">
                                        <tr>
                                            <td class="align-middle text-start" colspan="3">TOTAL</td>
                                            <td class="align-middle text-end total" ></td>
                                            <td class="align-middle text-end total" ></td>
                                            <td class="align-middle text-end total" ></td>
                                            <td class="align-middle text-end total" ></td>
                                            <td class="align-middle text-end total" ></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            {{-- Proyecto calendario general D--}}
            <div class="tab-pane @if(Auth::user()->id_grupo == 4) active @endif" id="calendarioGeneral" role="tabpanel" aria-labelledby="calendarioGeneral_tab" >    
                <div class="row mx-auto" >
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="tableRowStyle table table-hover table-bordered order-table text-center tableSize align-middle"
                                    id="catalogoD" data-right="2,3,4,5,6,7,8,9,10,11,12,13,14" data-left="1" data-center="0,1,2,3,4,5,6,7,8,9,10,11,12,13,14" style="width:100%; font-size: 14px;">
                                    <thead  class="colorMorado " style="text-align: center !important">
                                        <tr>
                                            <th class="exportable align-middle text-light d-none">Columna para agrupar las UPP</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important width:28em; !important">Clave presupuestal</th>
                                            <th class="exportable align-middle text-light sum" style="text-align: center !important">Monto anual</th>
                                            <th class="exportable align-middle text-light sum" style="text-align: center !important">Enero</th>
                                            <th class="exportable align-middle text-light sum" style="text-align: center !important">Febrero</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Marzo</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Abril</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Mayo</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Junio</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Julio</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Agosto</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Septiembre</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Octubre</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Noviembre</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Diciembre</th>
                                        </tr>
                                    </thead>
                                    <tfoot class="colorMorado">
                                        <tr>
                                            <td class="align-middle text-end total d-none" ></td>
                                            <td class="align-middle text-start">TOTAL</td>
                                            <td class="align-middle text-end total" ></td>
                                            <td class="align-middle text-end total" ></td>
                                            <td class="align-middle text-end total" ></td>
                                            <td class="align-middle text-end total" ></td>
                                            <td class="align-middle text-end total" ></td>
                                            <td class="align-middle text-end total" ></td>
                                            <td class="align-middle text-end total" ></td>
                                            <td class="align-middle text-end total" ></td>
                                            <td class="align-middle text-end total" ></td>
                                            <td class="align-middle text-end total" ></td>
                                            <td class="align-middle text-end total" ></td>
                                            <td class="align-middle text-end total" ></td>
                                            <td class="align-middle text-end total" ></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Proyecto calendario actividades general E--}}
            <div class="tab-pane" id="calendarioGeneralActividad" role="tabpanel" aria-labelledby="calendarioGeneralActividad_tab" >    
                <div class="row mx-auto" >
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="tableRowStyle table table-hover table-bordered order-table text-center tableSize align-middle"
                                    id="catalogoE" data-right="7,11,12,13,14,15,16,17,18,19,20,21,22,23" data-left="0,1,2,3,4,5,6,8,9,10" data-center="0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23" style="width:100%; font-size: 14px;">
                                    <thead  class="colorMorado">
                                        <tr>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">UPP</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">UR</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Programa</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Subprograma</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Proyecto</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Fondo</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Actividad</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Cantidad Beneficiarios</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Beneficiarios</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">U. de medida</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Tipo de actividad</th>
                                            <th class="exportable align-middle text-light sum" style="text-align: center !important">Meta anual</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Enero</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Febrero</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Marzo</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Abril</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Mayo</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Junio</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Julio</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Agosto</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Septiembre</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Octubre</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Noviembre</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Diciembre</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Avance de proyectos con actividades por upp F--}}
            @if(Auth::user()->id_grupo == 1)
            <div class="tab-pane" id="avanceProyectoActividadUPP" role="tabpanel" aria-labelledby="avanceProyectoActividadUPP_tab" >    
                <div class="row mx-auto" >
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="tableRowStyle table table-hover table-bordered order-table text-center tableSize align-middle"
                                    id="catalogoF" data-right="1,2,3" data-left="0,4" data-center="0,1,2,3,4" style="width:100%; font-size: 14px;">
                                    <thead class="colorMorado">
                                        <tr>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Unidad programática presupuestaría</th>
                                            <th class="exportable align-middle text-light sum" style="text-align: center !important">Cantidad de proyectos</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Cantidad de proyectos con actividades</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">% de avance</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important">Estatus</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @isset($dataSet)
    @include('panels.datatableReportesAdministrativos')
    @endisset  

    <link href="https://cdn.datatables.net/v//dt/dt-1.13.4/rg-1.3.1/datatables.min.css" rel="stylesheet"/>
    <script src="https://cdn.datatables.net/v/dt/dt-1.13.4/rg-1.3.1/datatables.min.js"></script>
    <script type="text/javascript">
        //inicializamos el data table
        var tabla;
        var letter;
        $(document).ready(function() {
            $(".alert").delay(4000).slideUp(200, function() {
                $(this).alert('close');
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            getDataFechaCorte($('#anio_filter').val());
    
            $('button[data-bs-toggle="tab"]').on('click', function (e) {
                var id = e.target.id;
                selectTable(id);
            });

            $( window ).resize(function() {
                redrawTable(tabla);
            });

            var user = {!! json_encode((array)auth()->user()->clv_upp) !!};

            if(user[0] == null){
                var dt = $('#catalogoA');
                tabla="#catalogoA";
                letter="A";
            }else{
                var dt = $('#catalogoD');
                tabla="#catalogoD";
                letter="D";
            }
            dt.DataTable().clear().destroy();
            getData(tabla,letter);
           
            let form = document.getElementById("form");
            
            $("#form").on('change','.filters',function(){
                var id = $(".active")[1].id;
                selectTable(id);
            });
            
            function selectTable(id){
                switch(id){
                    case "fondoMensual_tab":
                        var dt = $('#catalogoA');
                        tabla="#catalogoA";
                        letter="A";
                        $('.div_upp').addClass('d-none');          
                        dt.DataTable().clear().destroy();
                        getData(tabla,letter);   
                        break;
                    case "capituloPartida_tab":
                        var dt = $('#catalogoB');
                        tabla="#catalogoB";
                        letter="B";
                        $('.div_upp').addClass('d-none');
                        dt.DataTable().clear().destroy();
                        getData(tabla,letter);
                        break;
                    case "avanceGeneral_tab":
                        var dt = $('#catalogoC');
                        tabla="#catalogoC";
                        letter="C";
                        $('.div_upp').addClass('d-none');
                        dt.DataTable().clear().destroy();
                        getData(tabla,letter);
                        break;
                    case "calendarioGeneral_tab":
                        var dt = $('#catalogoD');
                        tabla="#catalogoD";
                        letter="D";
                        $('.div_upp').removeClass('d-none');
                        dt.DataTable().clear().destroy();
                        getData(tabla,letter);
                        break;
                    case "calendarioGeneralActividad_tab":
                        var dt = $('#catalogoE');
                        tabla="#catalogoE";
                        letter="E";
                        $('.div_upp').removeClass('d-none');
                        dt.DataTable().clear().destroy();
                        getData(tabla,letter);
                        break;
                    case "avanceProyectoActividadUPP_tab":
                        var dt = $('#catalogoF');
                        tabla="#catalogoF";
                        letter="F";
                        $('.div_upp').addClass('d-none');
                        dt.DataTable().clear().destroy();
                        getData(tabla,letter);
                        break;
                }
            }
    
        });
        
        $("#form").on("change",".filters_anio",function(e){
            dt.DataTable().clear().destroy();
            getData(tabla,letter);
            getDataFechaCorte($('#anio_filter').val());
        });
        
        $("#form").on("change",".filters_fechaCorte",function(e){
            dt.DataTable().clear().destroy();
            getData(tabla,letter);
        });

        $("#form").on("change",".filters_upp",function(e){
            dt.DataTable().clear().destroy();
            getData(tabla,letter);
        });

        $('.btn_click').click(function(){ // dar click a los botones de descarga y mandar el nombre
            var nombre = "calendario_fondo_mensual";
            switch (tabla) {
                case "#catalogoA":
                    nombre = 'calendario_fondo_mensual';              
                    break;
                case "#catalogoB":
                    var dt = $('#catalogoB');
                    nombre = "reporte_resumen_por_capitulo_y_partida";
                    break;
                case "#catalogoC":
                    nombre = "proyecto_avance_general";
                    break;
                case "#catalogoD":
                    var dt = $('#catalogoD');
                    nombre = "calendario_general";
                    break;
                case "#catalogoE":
                    nombre = "proyecto_calendario_actividades";
                    break;
                case "#catalogoF":
                    nombre = "avance_proyectos_actividades_upp";
                    break;
            }
            $('#form').attr('action', '/Reportes/download/'+nombre);
        });
    </script>
@endsection