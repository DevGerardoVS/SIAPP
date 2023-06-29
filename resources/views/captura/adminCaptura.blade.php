<?php
    $titleDesc = "Administración de Captura";
    
?>

@extends('layouts.app')
@section('content')

<div class="container w-100 p-4" >
    <header>
        <h1 class="fw-bold text-center">{{ $titleDesc }}</h1>
        <div class="rounded-pill" style="height: .5em; background-color: rgb(37, 150, 190)"></div>
        <form action="{{route('claves_presupuestarias')}}" id="buscarFormA" name="buscarFormA" method="post"></form>
        <form action="{{route('metas_actividades')}}" id="buscarFormB" name="buscarFormB" method="post"></form> 
    </header>

     {{-- Form para cambiar los select --}}
     <form id="form" method="POST">
        @csrf
        <div class="col-md-10 col-sm-12 d-md-flex mt-5">
            <div class="col-sm-3 col-md-3 col-lg-2 text-md-end">
                <label for="anio" class="form-label fw-bold mt-md-1">Ejercicio: </label>
            </div>
            <div class="col-sm-12 col-md-3 col-lg-2">
                <p class="fw-bold">{{$anio}}</p>
            </div>
            
            <div class="col-sm-3 col-md-3 col-lg-2 text-md-end">
                <label for="estatus_filter" class="form-label fw-bold mt-md-1">Estatus:</label>
            </div>
            <div class="col-sm-12 col-md-3 col-lg-2">
                <select class="form-control filters filters_estatus" id="estatus_filter" name="estatus_filter" autocomplete="estatus_filter">
                    <option value="">Todos</option>
                    @foreach ($estatus as $est)
                    <option value={{$est->estatus}}>{{$est->estatus}}</option>
                @endforeach
                </select>
            </div>
        </div>
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
    </form>


    <ul class="nav nav-tabs " id="tabs" role="tablist">
        <li class="nav-item" >
            <button class="nav-link textoMorado active" role="tab" type="button" id="clavePresupuestaria_tab" data-bs-toggle="tab" data-bs-target="#clavePresupuestaria" aria-controls="clavePresupuestaria" aria-selected="true">Calendario fondo mensual</button>
        </li>
        <li class="nav-item" >
            <button class="nav-link textoMorado" role="tab" type="button" id="metasActividad_tab" data-bs-toggle="tab" data-bs-target="#metasActividad" aria-controls="metasActividad" aria-selected="false">Resumen capítulo y partida</button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- Claves presupuestarias A --}}
        <div class="tab-pane active" id="clavePresupuestaria" role="tabpanel" aria-labelledby="clavePresupuestaria_tab" >    
            <div class="row mx-auto">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="tableRowStyle table table-hover table-bordered order-table text-center tableSize align-middle"
                                id="catalogoA" data-right="" data-left="" data-center="" style="width:100%">
                                <thead class="colorMorado">
                                    <tr>
                                        <th class="exportable align-middle text-light">Clave UPP</th>
                                        <th class="exportable align-middle text-light">UPP</th>
                                        <th class="exportable align-middle text-light">Fecha de último cambio</th>
                                        <th class="exportable align-middle text-light">Estatus</th>
                                        <th class="exportable align-middle text-light">Usuario que actualizó</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Metas de actividades B -->
        <div class="tab-pane" id="metasActividad" role="tabpanel" aria-labelledby="metasActividad_tab" >    
            <div class="row mx-auto">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="tableRowStyle table table-hover table-bordered order-table text-center tableSize align-middle"
                                id="catalogoB" data-right="" data-left="" data-center="" style="width:100%">
                                <thead class="colorMorado">
                                    <tr>
                                        <th class="exportable align-middle text-light">Clave UPP</th>
                                        <th class="exportable align-middle text-light">UPP</th>
                                        <th class="exportable align-middle text-light">Fecha de último cambio</th>
                                        <th class="exportable align-middle text-light">Estatus</th>
                                        <th class="exportable align-middle text-light">Usuario que actualizó</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @isset($dataSet)
    @include('panels.datatableAdminCaptura')
    @endisset  
</div>

<script type="text/javascript">
    //inicializamos el data table
    var tabla;
    var letter;
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

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
                case "clavePresupuestaria_tab":
                    var dt = $('#catalogoA');
                    tabla="#catalogoA";
                    letter="A";        
                    dt.DataTable().clear().destroy();
                    getData(tabla,letter);   
                    break;
                case "metasActividad_tab":
                    var dt = $('#catalogoB');
                    tabla="#catalogoB";
                    letter="B";
                    dt.DataTable().clear().destroy();
                    getData(tabla,letter);
                    break;
            }
        }

    });
    
    $("#form").on("change",".filters_estatus",function(e){
        dt.DataTable().clear().destroy();
        getData(tabla,letter);
    });

    $("#form").on("change",".filters_upp",function(e){
        dt.DataTable().clear().destroy();
        getData(tabla,letter);
    });

</script>
@endsection