<?php
    $titleDesc = "Reportes Administrativos";
    
?>

@extends('layouts.app')
@section('content')
<div class="container w-100 p-4">
        {{-- Forms de acuerdo a la tabla seleccionada en el tab --}}
        <form action="{{route('calendario_fondo_mensual')}}" id="buscarFormA" method="post">
            <input type="text" id="detalleA_val" name="preg1" style="display: none">
        </form>
        <form action="{{route('resumen_capitulo_partida')}}" id="buscarFormB" method="post">
            <input type="text" id="detalleB_val" name="preg2" style="display: none">
        </form>
        <form action="{{route('proyecto_avance_general')}}" id="buscarFormC" method="post">
            <input type="text" id="detalleC_val" name="preg3" style="display: none">
        </form>
        <form action="{{route('proyecto_calendario_general')}}" id="buscarFormD" method="post">
            <input type="text" id="detalleD_val" name="preg4" style="display: none">
        </form>

        <header>
            <h1 class="fw-bold text-center">{{ $titleDesc }}</h1>
            <div class="rounded-pill" style="height: .5em; background-color: rgb(37, 150, 190)"></div>
        </header>

        <br>
        <div>
            <section class="row mt-5" >
                <form action="" id="buscarForm" method="POST">
                    <div class="col-md-10 col-sm-12 d-md-flex">
                        <div class="col-sm-3 col-md-3 col-lg-2 text-md-end">
                            <label for="anio_filter" class="form-label fw-bold mt-md-1">año: </label>
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
                </form>
            </section>
        </div>
        <br>
        <br>
        <div class="d-flex flex-wrap justify-content-end">
            <form action="{{ route('downloadReport',['nombre'=>'calendario_fondo_mensual']) }}" method="POST" enctype="multipart/form-data">
                @csrf
                {{-- <input type="text" hidden class="anio" id="anio" name="anio">
                <input type="text" hidden class="fechaCorte" id="fechaCorte" name="fechaCorte"> --}}
                <button id="btnPDF" type="submit" formtarget="_blank" class="btn btn-light btn-sm btn-labeled me-3" style="border-color: #6a0f49;" title="Generar Reporte PDF" name="action" value="pdf">
                    <span class="btn-label"><i class="fa fa-file-pdf-o text-danger fs-4 align-middle"></i></span>
                    <span class="d-lg-inline align-middle" style="color:#6a0f49; font-size: 1rem">Exportar a PDF</span> 
                </button>
                <button id="btnExcel" type="submit" formtarget="_blank" class="btn btn-light btn-sm btn-labeled" style="border-color: #6a0f49;" title="Generar Reporte Excel" name="action" value="xls">
                    <span class="btn-label"><i class="fa fa-file-excel-o text-success fs-4 align-middle"></i></span>
                    <span class="d-lg-inline align-middle" style="color:#6a0f49; font-size: 1rem">Exportar a Excel</span>
                </button>
            </form>
        </div>
        <br>

        {{-- TAB --}}
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="calendario-fondo-base" data-bs-toggle="tab" data-bs-target="#fondo-base" type="button" role="tab" aria-controls="fondo-base" aria-selected="false">Calendario fondo base mensual</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="resumen-capitulo-partida" data-bs-toggle="tab" data-bs-target="#capitulo-partida" type="button" role="tab" aria-controls="capitulo-partida" aria-selected="false">Resume capítulo y partida</button>
            </li>
            {{-- <li class="nav-item" role="presentation">
              <button class="nav-link" id="proyecto_avance_general" data-bs-toggle="tab" data-bs-target="#avance-general" type="button" role="tab" aria-controls="avance-general" aria-selected="false">Proyecto avance general</button>
            </li> --}}
          </ul>
          <div class="tab-content" id="myTabContent">
            <div class="tab-pane active" id="fondo-base" role="tabpanel" aria-labelledby="calendario-fondo-base">
                <div class="row mx-auto" >
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="tableRowStyle table table-hover table-bordered order-table text-center tableSize align-middle"
                                    id="catalogoA" data-right="2,3,4,5,6,7,8,9,10,11,12,13,14" data-left="0,1" data-center="0,1,2,3,4,5,6,7,8,9,10,11,12,13,14" style="width:100%">
                                    <thead  class="colorMorado">
                                        <tr>
                                            <th class="exportable align-middle text-light">Ramo</th>
                                            <th class="exportable align-middle text-light" style="width: 300px ;">Fondo</th>
                                            <th class="exportable align-middle text-light">Enero</th>
                                            <th class="exportable align-middle text-light">Febrero</th>
                                            <th class="exportable align-middle text-light">Marzo</th>
                                            <th class="exportable align-middle text-light">Abril</th>
                                            <th class="exportable align-middle text-light">Mayo</th>
                                            <th class="exportable align-middle text-light">Junio</th>
                                            <th class="exportable align-middle text-light">Julio</th>
                                            <th class="exportable align-middle text-light">Agosto</th>
                                            <th class="exportable align-middle text-light">Septiembre</th>
                                            <th class="exportable align-middle text-light">Octubre</th>
                                            <th class="exportable align-middle text-light">Noviembre</th>
                                            <th class="exportable align-middle text-light">Diciembre</th>
                                            <th class="exportable align-middle text-light sum">Importe total</th>
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
            <div class="tab-pane" id="capitulo-partida" role="tabpanel" aria-labelledby="resumen-capitulo-partida">
              <div class="row mx-auto">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="tableRowStyle table table-hover table-bordered order-table text-center tableSize align-middle"
                                id="catalogoB" data-right="2" data-left="0,1" data-center="0,1,2" style="width:100%">
                                <thead  class="colorMorado">
                                    <tr>
                                        <th class="exportable align-middle text-light">Capítulo</th>
                                        <th class="exportable align-middle text-light">Partida</th>
                                        <th class="exportable align-middle text-light sum">Importe</th>
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
            {{-- <div class="tab-pane" id="avance-general" role="tabpanel" aria-labelledby="proyecto_avance_general">
              <div class="row mx-auto">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="tableRowStyle table table-hover table-bordered order-table text-center tableSize align-middle"
                                id="genericDataTable">
                                <thead class="colorMorado">
                                    <tr>
                                        <th class="exportable align-middle text-light">Unidad programática presupuestaría</th>
                                        <th class="exportable align-middle text-light">Fondo</th>
                                        <th class="exportable align-middle text-light">Capítulo</th>
                                        <th class="exportable align-middle text-light">Monto anual</th>
                                        <th class="exportable align-middle text-light">Calendarizado</th>
                                        <th class="exportable align-middle text-light">Disponible</th>
                                        <th class="exportable align-middle text-light">% de avance</th>
                                        <th class="exportable align-middle text-light">Estatus</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div> --}}
            </div>
          </div>
          {{-- TAB --}}
    </div>
    <br>
    <br>

    @isset($dataSet)
    @include('panels.datatableMultiple')
    @endisset 
    {{-- script --}}

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
      
      $("#buscarForm").on('change','.filters',function(){
          var id = $(".active")[1].id;
          selectTable(id);
      });

  
      function selectTable(id){
        switch(id){
            case "calendario-fondo-base":
              var dt = $('#catalogoA');
              tabla="#catalogoA";
              letter="A";
              dt.DataTable().clear().destroy();
              getData(tabla,letter);                    
              break;
            case "resumen-capitulo-partida":
              var dt = $('#catalogoB');
              tabla="#catalogoB";
              letter="B";
              dt.DataTable().clear().destroy();
              getData(tabla,letter);
              break;
        }
      }
  
  });
  
  
       
  
  </script>
    {{-- script --}}
@endsection