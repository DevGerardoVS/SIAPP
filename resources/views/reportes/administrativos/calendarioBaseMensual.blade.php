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

    <div class="row mx-auto " style="width: 90%">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">

                    <table class="tableRowStyle table table-hover table-bordered order-table text-center tableSize align-middle"
                        id="genericDataTable">
                        <thead  class="colorMorado">
                            <tr>
                                <th class="exportable align-middle text-light">Ramo</th>
                                <th class="exportable align-middle text-light" style="width: 25em;">Fondo</th>
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
                                <th class="exportable align-middle text-light">Importe total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reportes as $reporte)
                                
                            <tr>
                                <td class="align-middle text-start">{{$reporte->ramo}}</td>
                                <td class="align-middle text-start">{{$reporte->fondo}}</td>
                                <td class="align-middle text-end">{{number_format($reporte->enero)}}</td>
                                <td class="align-middle text-end">{{number_format($reporte->febrero)}}</td>
                                <td class="align-middle text-end">{{number_format($reporte->marzo)}}</td>
                                <td class="align-middle text-end">{{number_format($reporte->abril)}}</td>
                                <td class="align-middle text-end">{{number_format($reporte->mayo)}}</td>
                                <td class="align-middle text-end">{{number_format($reporte->junio)}}</td>
                                <td class="align-middle text-end">{{number_format($reporte->julio)}}</td>
                                <td class="align-middle text-end">{{number_format($reporte->agosto)}}</td>
                                <td class="align-middle text-end">{{number_format($reporte->septiembre)}}</td>
                                <td class="align-middle text-end">{{number_format($reporte->octubre)}}</td>
                                <td class="align-middle text-end">{{number_format($reporte->noviembre)}}</td>
                                <td class="align-middle text-end">{{number_format($reporte->diciembre)}}</td>
                                <td class="align-middle text-end">{{number_format($reporte->diciembre)}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @include('panels.datatableReportesAdministrativos')
@endsection