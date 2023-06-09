<?php
    $titleDesc = "Calendario Fondo Base Mensual";
    
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
            <section class="row mt-5" id="filter">
                <form action="{{route('get_reporte')}}" id="buscarForm" method="POST">
                <div class="col-md-10 col-sm-12 d-md-flex">
                        <div class="col-sm-3 col-md-3 col-lg-2 text-md-end">
                            <label for="anio_filter" class="form-label fw-bold mt-md-1">año: </label>
                        </div>
                        <div class="col-sm-12 col-md-3 col-lg-2">
                            <select class="form-control filters" id="anio_filter" name="anio_filter" autocomplete="anio_filter">
                                @foreach ($anios as $anio)
                                    <option value={{$anio->ejercicio}}>{{ DateTime::createFromFormat('y', $anio->ejercicio)->format('Y')}}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        @php
                            $fechas = DB::select('select distinct deleted_at from programacion_presupuesto pp where ejercicio = ? and deleted_at is not null',[23]);
                        @endphp
                        <div class="col-sm-3 col-md-3 col-lg-2 text-md-end">
                            <label for="corte_filter" class="form-label fw-bold mt-md-1">Fecha de corte:</label>
                        </div>
                        <div class="col-sm-12 col-md-3 col-lg-2">
                            <select class="form-control filters filters_fecha" id="corte_filter" name="corte_filter" autocomplete="corte_filter">
                                <option value="">Elegir fecha de corte</option>
                                @foreach ($fechas as $fecha)
                                    
                                <option value={{\Carbon\Carbon::parse($fecha->deleted_at)->format('Y-m-d')}}>{{\Carbon\Carbon::parse($fecha->deleted_at)->format('Y-m-d')}}</option>
                                @endforeach
                            </select>
                            {{-- @php $date = empty($fechas) ? 0 : "2023-05-07"  @endphp --}}
                        </div>
                    </div>
                </form>
            </section>
            {{--  --}}
            {{-- <form action="{{route('get_reporte')}}" id="buscarForm" method="POST">
                <div class="col-sm-3 col-md-3 col-lg-2 text-md-end">
                    <label for="anio_filter" class="form-label fw-bold mt-md-1">año: </label>
                </div>
                <div class="col-sm-12 col-md-3 col-lg-2">
                    <select class="form-control filters filters_anio" id="anio_filter" name="anio_filter" autocomplete="anio_filter">
                        @foreach ($anios as $anio)
                            <option value={{$anio->ejercicio}}>{{ DateTime::createFromFormat('y', $anio->ejercicio)->format('Y')}}</option>
                        @endforeach
                    </select>
                </div>
            </form> --}}
        </div>
        <br>
        <br>
        <div class="d-flex flex-wrap">
            <form action="{{ route('downloadReport') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <button id="btnPDF" type="submit" formtarget="_blank" class="btn btn-light btn-sm btn-labeled me-3" style="border-color: #6a0f49;" title="Generar Reporte PDF">
                    <span class="btn-label"><i class="fa fa-file-pdf-o text-danger fs-4 align-middle"></i></span>
                    <span class="d-lg-inline align-middle" style="color:#6a0f49; font-size: 1rem">Exportar a PDF</span> 
                </button>
            </form>
                <button id="btnExcel" type="button" class="btn btn-light btn-sm btn-labeled" style="border-color: #6a0f49;" title="Generar Reporte Excel">
                        <span class="btn-label"><i class="fa fa-file-excel-o text-success fs-4 align-middle"></i></span>
                        <span class="d-lg-inline align-middle" style="color:#6a0f49; font-size: 1rem">Exportar a Excel</span>
                </button>
        </div>
    </div>
    <br>
    <div class="row mx-auto" style="width: 90%">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <table class="tableRowStyle table table-hover table-bordered order-table text-center tableSize align-middle"
                        id="genericDataTable" data-format="2,3,4,5,6,7,8,9,10,11,12,13,14">
                        <thead  class="colorMorado">
                            <tr>
                                <th class="exportable align-middle text-light">Ramo</th>
                                <th class="exportable align-middle text-light" style="width: 25em;">Fondo</th>
                                <th class="exportable align-middle text-light sum">Enero</th>
                                <th class="exportable align-middle text-light sum">Febrero</th>
                                <th class="exportable align-middle text-light sum">Marzo</th>
                                <th class="exportable align-middle text-light sum">Abril</th>
                                <th class="exportable align-middle text-light sum">Mayo</th>
                                <th class="exportable align-middle text-light sum">Junio</th>
                                <th class="exportable align-middle text-light sum">Julio</th>
                                <th class="exportable align-middle text-light sum">Agosto</th>
                                <th class="exportable align-middle text-light sum">Septiembre</th>
                                <th class="exportable align-middle text-light sum">Octubre</th>
                                <th class="exportable align-middle text-light sum">Noviembre</th>
                                <th class="exportable align-middle text-light sum">Diciembre</th>
                                <th class="exportable align-middle text-light sum">Importe total</th>
                            </tr>
                        </thead>
                        <tfoot class="colorMorado" style="">
                            <tr>
                                <td class="align-middle text-center" colspan="14">TOTAL</td>
                                <td class="align-middle text-end total" style="width: 20em;" id="total"></td>
                            </tr>
                        </tfoot>
                    </table>
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