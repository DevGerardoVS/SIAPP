<?php
    $titleDesc = "Reportes Ley de Planeación Hacendaria";
    
?>

@extends('layouts.app')
@section('content')
    <div class="container w-100 p-4">
        <header>
            <h1 class="fw-bold text-center">{{ $titleDesc }}</h1>
            <div class="rounded-pill" style="height: .5em; background-color: rgb(37, 150, 190)"></div>
        </header>
        
        <section class="row mt-5" id="filter">
            <div class="col-md-10 col-sm-12 d-md-flex">
                <div class="col-sm-3 col-md-3 col-lg-2 text-md-end">
                    <label for="anio_filter" class="form-label fw-bold mt-md-1">año: </label>
                </div>
                <div class="col-sm-12 col-md-3 col-lg-2">
                    <select class="form-control filters" id="anio_filter" name="anio_filter" autocomplete="anio_filter" onchange="f()">
                        @foreach ($anios as $anio)
                            <option value={{$anio->ejercicio}}>{{ DateTime::createFromFormat('y', $anio->ejercicio)->format('Y')}}</option>
                        @endforeach
                            @php $get_anio = $anio->ejercicio @endphp
                    </select>
                </div>
                
                @php
                    $fechas = DB::select('select distinct deleted_at from programacion_presupuesto pp where ejercicio = ? and deleted_at is not null',[$get_anio]);
                @endphp
                <div class="col-sm-3 col-md-3 col-lg-2 text-md-end">
                    <label for="corte_filter" class="form-label fw-bold mt-md-1">Fecha de corte:</label>
                </div>
                <div class="col-sm-12 col-md-3 col-lg-2">
                    <select class="form-control filters" id="corte_filter" name="corte_filter" autocomplete="corte_filter">
                        <option value="">Elegir fecha de corte</option>
                        @foreach ($fechas as $fecha)
                            
                        <option value={{\Carbon\Carbon::parse($fecha->deleted_at)->format('Y-m-d')}}>{{\Carbon\Carbon::parse($fecha->deleted_at)->format('Y-m-d')}}</option>
                        @endforeach
                    </select>
                    @php $date = empty($fechas) ? 0 : "2023-05-07"  @endphp
                </div>

            </div>
        </section>

        <section class="mt-5">
            <div class="row justify-content-center">
                <div class="col-md-10 col-sm-12">
                    <div class="card">
                        <div class="card-body">

                            <table  class="table table-striped table-bordered" id="tbl-reportes">
                                <tbody>
                                    @foreach ($names as $name)
                                        <tr>
                                            <td class="d-flex justify-content-between px-5">
                                                @php
                                                    $replace_underscore = str_replace('_',' ',$name->name);
                                                    $replace_report = str_replace('reporte','',$replace_underscore);
                                                    // $replace_art = str_replace('art',' ART.',$replace_report);
                                                    $replace_num = str_replace('num','numeral',$replace_report);
                                                    if (str_contains($replace_num, 'num')) {
                                                        $replace_num = substr_replace( $replace_num, "inc ", 15, 0 );
                                                    }
                                                    $correct_name = $replace_num;  
                                                @endphp

                                                <div class="my-auto me-2">{{strtoupper($correct_name)}}</div>
                                                <div class="d-flex justify-content-end flex-wrap">
                                                    <form action="{{ route('downloadReport',['name'=>$name->name, 'anio'=> 23, 'date'=> 0]) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <button id="btnPDF" type="submit" formtarget="_blank" class="btn btn-light btn-sm btn-labeled me-sm-3" style="border-color: #6a0f49;" title="Generar Reporte PDF">
                                                            <span class="btn-label"><i class="fa fa-file-pdf-o text-danger fs-4 align-middle"></i></span>
                                                            <span class="d-sm-none d-lg-inline align-middle" style="color:#6a0f49; font-size: 1rem">Exportar a PDF</span> 
                                                        </button>
                                                    </form>
                                                    {{-- <button id="btnExcel" type="button" class="btn btn-light btn-sm btn-labeled" style="border-color: #6a0f49;" title="Generar Reporte Excel">
                                                            <span class="btn-label"><i class="fa fa-file-excel-o text-success fs-4 align-middle"></i></span>
                                                            <span class="d-sm-none d-lg-inline align-middle" style="color:#6a0f49; font-size: 1rem">Exportar a Excel</span>
                                                    </button> --}}
                                                    
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach 			
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection