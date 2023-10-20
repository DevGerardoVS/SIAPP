<?php
    $titleDesc = "Reporte MML";
    
?>

@extends('layouts.app')
@section('content')
    <div class="mx-auto p-4" style="width:80%;">
        <header class="d-flex justify-content-center" style=" border-bottom: 5px solid #17a2b8;">
            <h2>{{ $titleDesc }}</h2>
        </header>

        <form action="{{route('get_avance_mir')}}" id="buscarFormA" name="analisis" method="post"></form>
        <form action="{{route('get_comprobacion')}}" id="buscarFormB" name="analisis" method="post"></form>

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <h4>{{$errors->first()}}</h4>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        <section class="row mt-5" >
            <form id="buscarForm" method="POST"> 
                @csrf
                <div class="row ">
                    {{-- Select Año --}}
                    <div class="col-sm-12 col-md-4 col-lg-2 mb-3 mb-sm-3 mb-md-3 mb-lg-0">
                        <label for="anio_filter" class="form-label fw-bold">Año: </label>
                        <select class="form-control filters filters_anio" id="anio_filter" name="anio_filter" autocomplete="anio_filter">
                            @foreach ($anios as $anio)
                                <option value={{$anio->ejercicio}}>{{ $anio->ejercicio}}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Select UPP --}}
                    <div class="col-sm-12 col-md-6 col-lg-4 mb-3 mb-sm-3 mb-md-3 mb-lg-0">
                        <label for="upp_filter" class="form-label fw-bold">UPP: </label>
                        <select class="form-control filters filters_upp" id="upp_filter" name="upp_filter" autocomplete="upp_filter">
                        </select>
                    </div>
                    {{-- Select Programa --}}
                    <div class="col-sm-12 col-md-6 col-lg-4 mb-3 mb-sm-3 mb-md-3 mb-lg-0 mostrar d-none">
                        <label for="programa_filter" class="form-label fw-bold">Programa:</label>
                        <select class="form-control filters filters_programa" id="programa_filter" name="programa_filter" autocomplete="programa_filter">
                        </select>
                    </div>
                    {{-- Select Estatus --}}
                    <div class="col-sm-12 col-md-4 col-lg-2 mb-3 mb-sm-3 mb-md-3 mb-lg-0 mostrarEstatus">
                        <label for="estatus_filter" class="form-label fw-bold">Estatus: </label>
                        <select class="form-control filters filters_estatus" id="estatus_filter" name="estatus_filter" autocomplete="estatus_filter">
                            <option value="">Todos</option>
                            <option value="3">Validado</option>
                            <option value="0">Pendiente</option>
                        </select>
                    </div>
                    {{-- Select con MIR --}}
                    <div class="col-sm-12 col-md-4 col-lg-2 mb-3 mb-sm-3 mb-md-3 mb-lg-0 mostrar d-none">
                        <label for="mir_filter" class="form-label fw-bold">Con MIR: </label>
                        <select class="form-control filters filters_mir" id="mir_filter" name="mir_filter" autocomplete="mir_filter">
                            <option value="">Todos</option>
                            <option value="1">Con MIR</option>
                            <option value="0">Sin MIR</option>
                        </select>
                </div>
            </form>
        </section>

        <ul class="nav nav-tabs mt-4" id="tabs" role="tablist">
            <li class="nav-item" >
                <button class="nav-link textoMorado active " role="tab" type="button" id="avanceMir_tab" data-bs-toggle="tab" data-bs-target="#avanceMir" aria-controls="avanceMir" aria-selected="true">Avance MIR</button>
            </li>
            <li class="nav-item" >
                <button class="nav-link textoMorado" role="tab" type="button" id="comprobacion_tab" data-bs-toggle="tab" data-bs-target="#comprobacion" aria-controls="comprobacion" aria-selected="false">Comprobación</button>
            </li>
        </ul>

        <div class="tab-content" style="font-size: 14px;">
            {{-- Avance MIR A--}}
            <div class="tab-pane active" id="avanceMir" role="tabpanel" aria-labelledby="avanceMir_tab" >    
                <div class="row justify-content-center">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <table class="tableRowStyle table table-hover table-bordered order-table text-center tableSize align-middle" id="catalogoA" style="width:100%; font-size: 14px;" data-left="1,3">
                                    <thead class="colorMorado">
                                        <tr>
                                            <th class="exportable align-middle" style="text-align: center !important">Clave UPP</th>
                                            <th class="exportable align-middle" style="text-align: center !important">UPP</th>
                                            <th class="exportable align-middle" style="text-align: center !important">Clave PP</th>
                                            <th class="exportable align-middle">Programa presupuestario</th>
                                            <th class="exportable align-middle" style="text-align: center !important">estatus</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Comprobación B--}}
            <div class="tab-pane" id="comprobacion" role="tabpanel" aria-labelledby="comprobacion_tab" > 
                <div class="row mx-auto">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body table-responsive">
                                <table class="tableRowStyle table table-hover table-bordered order-table text-center tableSize align-middle"
                                    id="catalogoB" style="width:100%; font-size: 14px;" data-left="4">
                                    <thead  class="colorMorado">
                                        <tr>
                                            <th class="exportable align-middle text-light" style="text-align: center !important;">UPP</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important;">PP</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important;">UR</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important;">Área funcional</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important;">Nombre del proyecto</th>
                                            <th class="exportable align-middle text-light" style="text-align: center !important;">Con MIR</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Loader --}}
        <div class="custom-swal">
            <div class="custom-swal-content">
                <div class="custom-swal-text fs-2 mb-2 fw-bold">Cargando datos, por favor espere...</div>
                <div class="custom-swal-loader"></div>
            </div>
        </div>
    </div>

    @isset($dataSet)
    @include('panels.datatable_avance_mir')
    @endisset

    <script type="text/javascript">
        //inicializamos el data table
        var tabla;
        var letter;
        $(document).ready(function() {
            $(".alert").delay(10000).slideUp(200, function() {
                $(this).alert('close');
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            getUPP($('#anio_filter').val());
            getPrograma($('#upp_filter').val());

            $('button[data-bs-toggle="tab"]').on('click', function (e) {
                var id = e.target.id;
                selectTable(id);
            });

            $( window ).resize(function() {
                redrawTable(tabla);
            });

            var dt = $('#catalogoA');
            tabla="#catalogoA";
            letter="A";

            dt.DataTable().clear().destroy();
            getData(tabla,letter);
           
            let form = document.getElementById("form");
            
            $("#form").on('change','.filters',function(){
                var id = $(".active")[1].id;
                selectTable(id);
            });
            
            function selectTable(id){
                switch(id){
                    case "avanceMir_tab":
                        var dt = $('#catalogoA');
                        tabla="#catalogoA";
                        letter="A";     
                        $('.mostrar').addClass('d-none');
                        $('.mostrarEstatus').removeClass('d-none');
                        $("#upp_filter").val("");
                        $("#programa_filter").val("");
                        $("#mir_filter").val("");
                        dt.DataTable().clear().destroy();
                        getData(tabla,letter);
                        break;
                    case "comprobacion_tab":
                        var dt = $('#catalogoB');
                        tabla="#catalogoB";
                        letter="B";
                        $('.mostrarEstatus').addClass('d-none');
                        $('.mostrar').removeClass('d-none');
                        $("#upp_filter").val("")
                        $("#estatus_filter").val("")
                        dt.DataTable().clear().destroy();
                        getData(tabla,letter);
                        break;
                }
            }
    
        });
        
        $("#buscarForm").on("change",".filters_anio",function(e){
            $("#upp_filter").val("");
            dt.DataTable().clear().destroy();
            getData(tabla,letter);
            getUPP($('#anio_filter').val());
        });

        $("#buscarForm").on("change",".filters_upp",function(e){
            $("#programa_filter").val("");
            dt.DataTable().clear().destroy();
            getData(tabla,letter);
            getPrograma($('#upp_filter').val());
        });

        $("#buscarForm").on("change",".filters_programa",function(e){
            dt.DataTable().clear().destroy();
            getData(tabla,letter);
        });

        $("#buscarForm").on("change",".filters_estatus",function(e){
            dt.DataTable().clear().destroy();
            getData(tabla,letter);
        });

        $("#buscarForm").on("change",".filters_mir",function(e){
            dt.DataTable().clear().destroy();
            getData(tabla,letter);
        });
    </script>
@endsection