<?php
    $titleDesc = "Reporte avance MIR";
    
?>

@extends('layouts.app')
@section('content')
    <div class="mx-auto p-4" style="width:80%;">
        <header class="d-flex justify-content-center" style=" border-bottom: 5px solid #17a2b8;">
            <h2>{{ $titleDesc }}</h2>
        </header>

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <h4>{{$errors->first()}}</h4>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        <section class="row mt-5" >
            <form action="{{route('get_avance_mir')}}" id="buscarForm" method="POST"> 
                @csrf
                <div class="col-md-10 col-sm-12 d-md-flex">
                    <div class="col-sm-3 col-md-3 col-lg-2 text-md-end">
                        <label for="anio_filter" class="form-label fw-bold mt-md-1">Año: </label>
                    </div>
                    <div class="col-sm-12 col-md-3 col-lg-2">
                        <select class="form-control filters filters_anio" id="anio_filter" name="anio_filter" autocomplete="anio_filter">
                            @foreach ($anios as $anio)
                                <option value={{$anio->ejercicio}}>{{ $anio->ejercicio}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </section>
            <div class="row justify-content-center">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                            <table class="tableRowStyle table table-hover table-bordered order-table text-center tableSize align-middle" id="catalogo" style="width:100%; font-size: 14px;">
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
        $(document).ready(function() {
            getData();
        });

        $("#buscarForm").on("change", ".filters_anio", function(e) {
            e.preventDefault();
            getData();
        });
    </script>
@endsection