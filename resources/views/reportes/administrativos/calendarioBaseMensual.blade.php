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
        <br>

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">

                        <table class="tableRowStyle table table-hover table-bordered order-table text-center tableSize align-middle"
                            id="genericDataTable">
                            <thead  class="colorMorado">
                                <tr>
                                    <th class="exportable text-center text-light">Ramo</th>
                                    <th class="exportable text-center text-light">Fondo</th>
                                    <th class="exportable text-center text-light">Enero</th>
                                    <th class="exportable text-center text-light">Febrero</th>
                                    <th class="exportable text-center text-light">Marzo</th>
                                    <th class="exportable text-center text-light">Abril</th>
                                    <th class="exportable text-center text-light">Mayo</th>
                                    <th class="exportable text-center text-light">Junio</th>
                                    <th class="exportable text-center text-light">Julio</th>
                                    <th class="exportable text-center text-light">Agosto</th>
                                    <th class="exportable text-center text-light">Septiembre</th>
                                    <th class="exportable text-center text-light">Octubre</th>
                                    <th class="exportable text-center text-light">Noviembre</th>
                                    <th class="exportable text-center text-light">Diciembre</th>
                                    <th class="exportable text-center text-light">Importe total</th>
                                </tr>
                            </thead>
                            <tbody>

                                {{-- @foreach ($comprobaciones as $comprobacion)
                                    <tr >
                                        @if (verifyRole('Administrador') || verifyRole('Consultor'))
                                            <td>{{ $comprobacion->claveMunicipio}}</td>
                                        @endif
                                        <td>{{ $comprobacion->municipio}}</td>
                                        <td>{{ $comprobacion->fondo }}</td>
                                        <td>{{ $comprobacion->anio }}</td>
                                        <td>{{ $comprobacion->mes }}</td>
                                        @if (verifyRole('Municipal'))
                                            <td>{{ $comprobacion->tipo }}</td>
                                            <td>{{ $comprobacion->originalArchivo}}</td>
                                        @endif
                                        <td>{{\Carbon\Carbon::parse($comprobacion->fechaCarga)->format('d/m/Y')}}</td>
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                @if (verifyRole('Municipal'))
                                                    <a class="aButton" href="{{route('downloadFile',['comprobacion'=>$comprobacion->id])}}" style="border: none; background: none !important; color:rgb(103, 103, 255);" data-placement="top" title="Descargar {{$comprobacion->tipoArchivo}}">
                                                        <i class="fa fa-download hoverButtonStyle3" aria-hidden="true"></i> {{$comprobacion->tipoArchivo}}
                                                    </a>
                                                    <a class="aButton" href="{{route('comprobacion.edit',['comprobacion'=>$comprobacion->id])}}" style="border: none; background: none !important;  color:rgb(15, 129, 0);" data-placement="top" title="Editar">
                                                        <i class="fa fa-pencil-square-o hoverButtonStyle3" aria-hidden="true"></i>
                                                    </a>
                                                @else
                                                    <form action="{{ route('downloadZip',['comprobacion'=>$comprobacion->id]) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <button type="submit" name="export" value="exportZip" style="border: none; background: none !important; color:rgb(103, 103, 255);" title="Descargar zip">
                                                            <i class="fa fa-download hoverButtonStyle3" aria-hidden="true"></i> zip
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach --}}

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @include('panels.datatable_mod')
    </div>

    <script type="text/javascript">
        //inicializamos el data table
        $(document).ready(function() {
            getData();
        });
    </script>
@endsection