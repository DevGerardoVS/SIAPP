<?php
    $titleDesc = "Proyecto Calendario General";
    // $titleDesc = "Proyecto Calendario por UPP"
?>

@extends('layouts.app')
@section('content')
    <div class="container w-100 p-4">
        <header>
            <h1 class="fw-bold text-center">{{ $titleDesc }}</h1>
            <div class="rounded-pill" style="height: .5em; background-color: rgb(37, 150, 190)"></div>
        </header>

        <br>
        <div>
            <section class="row mt-5" >
                <form action="{{route('get_reporte_administrativo',['nombre'=>'calendario_fondo_mensual'])}}" id="buscarForm" method="POST">
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
                    <div class="col-md-10 col-sm-12 d-md-flex mt-2">
                        <div class="col-sm-3 col-md-3 col-lg-2 text-md-end">
                            <label for="fechaCorte_filter" class="form-label fw-bold mt-md-1">UPP:</label>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <select class="form-control filters filters_upp" id="upp_filter" name="upp_filter" autocomplete="upp_filter">
                                @foreach ($upps as $upp)
                                    <option value={{$upp->clave}} {{$upp->descripcion}}>{{$upp->clave}} {{$upp->descripcion}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </section>
        </div>
        <br>
        <br>
        <div class="d-flex flex-wrap justify-content-end">
            <form action="{{ route('downloadReport',['nombre'=>'calendario_general']) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="text" hidden class="anio" id="anio" name="anio">
                <input type="text" hidden class="fechaCorte" id="fechaCorte" name="fechaCorte">
                <input type="text" hidden class="upp" id="upp" name="upp">
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
        <div class="row mx-auto" >
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table class="tableRowStyle table table-hover table-bordered order-table text-center tableSize align-middle"
                            id="genericDataTable" data-right="2,3,4,5,6,7,8,9,10,11,12,13,14" data-left="0,1" data-center="0,1,2,3,4,5,6,7,8,9,10,11,12,13,14">
                            <thead  class="colorMorado">
                                <tr>
                                    <th class="exportable align-middle text-light">Clave presupuestal</th>
                                    <th class="exportable align-middle text-light">Monto Anual</th>
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
                                </tr>
                            </thead>
                            <tfoot class="colorMorado">
                                <tr>
                                    <td class="align-middle text-start">TOTAL</td>
                                    <td class="align-middle text-end total" style="width: 20em;" id="total"></td>
                                    {{-- poner los demas totales --}}
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <br>
    @isset($dataSet)
    @include('panels.datatableReportesAdministrativos')
    @endisset

<script type="text/javascript">
    //inicializamos el data table
    $(document).ready(function() {
        getData();

    });
</script>
@endsection