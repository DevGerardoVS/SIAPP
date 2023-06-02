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
        
        <section class="mt-5">
            {{-- <div class="widget-body  row justify-content-center">
                <table style="width: 80%;" class="table table-striped table-bordered" id="tbl-reportes">
                    <tbody>
                        @foreach ($names as $name)
                            <tr>
                                <td class="d-flex justify-content-between px-5">
                                    <div class="my-auto me-2">{{$name->name}}</div>
                                    <div class="d-flex justify-content-end">
                                        <button id="btnPDF" type="button" class="btn btn-sm btn-danger btn-labeled me-3">
                                                <span class="btn-label"><i class="fa fa-file-pdf-o"></i></span>
                                                Generar Reporte
                                        </button>
                                        <button id="btnExcel" type="button" class="btn btn-sm btn-success btn-labeled">
                                                <span class="btn-label"><i class="fa fa-file-excel-o"></i></span>
                                                Generar Reporte
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach 			
                    </tbody>
                </table>
            </div> --}}
            <div class="row justify-content-center">
                <div class="col-md-10 col-sm-12">
                    <div class="card">
                        <div class="card-body">

                            <table  class="table table-striped table-bordered" id="tbl-reportes">
                                <tbody>
                                    @foreach ($names as $name)
                                        <tr>
                                            <td class="d-flex justify-content-between px-5">
                                                <div class="my-auto me-2">{{$name->name}}</div>
                                                <div class="d-flex justify-content-end flex-wrap">
                                                    <button id="btnPDF" type="button" class="btn btn-sm btn-danger btn-labeled me-sm-3" title="Generar Reporte PDF">
                                                            <span class="btn-label"><i class="fa fa-file-pdf-o"></i></span>
                                                            <span class="d-sm-none d-lg-inline">Generar Reporte </span> 
                                                    </button>
                                                    <button id="btnExcel" type="button" class="btn btn-sm btn-success btn-labeled" title="Generar Reporte Excel">
                                                            <span class="btn-label"><i class="fa fa-file-excel-o"></i></span>
                                                            <span class="d-sm-none d-lg-inline">Generar Reporte </span>
                                                    </button>
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