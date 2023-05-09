@extends('layouts.app')
<?php
$stName = __('messages.nombre_sistema');
$acr = 'COCOTRA';

?>
@section('content')
<div class="container w-100 p-4">
    <h3 style="text-align: center; font-weight: bold;">{{ __('messages.reporte_movimientos_x_concesion') }}</h3>
    <br>
    <form action="{{route('export_reporte_movimientos_x_concesion_pdf')}}" id="exportFormPdf" method="post">
        @csrf
        <input type="hidden" id="no_concesion_export_pdf" name="no_concesion_filter" class="form-control">
        <input type="hidden" id="user_export_pdf" name="user_filter" class="form-control">
        <input type="hidden" id="fecha_ini_export_pdf" name="fecha_ini_filter" class="form-control">
        <input type="hidden" id="fecha_fin_export_pdf" name="fecha_fin_filter" class="form-control">
    </form>
    <form action="{{route('export_reporte_movimientos_x_concesion')}}" id="exportForm" method="post">
        @csrf
        <input type="hidden" id="no_concesion_export" name="no_concesion_filter" class="form-control">
        <input type="hidden" id="user_export" name="user_filter" class="form-control">
        <input type="hidden" id="fecha_ini_export" name="fecha_ini_filter" class="form-control">
        <input type="hidden" id="fecha_fin_export" name="fecha_fin_filter" class="form-control">
    </form>
    <form action="{{ route('get_reporte_movimientos_x_concesion') }}" id="buscarForm" method="post">
        @csrf
        <div class="row">
            <div class="col-sm-3">
                <label for="no_concesion_filter" class="form-label">{{__('messages.no_concesion')}}: </label>
                <input placeholder="{{__('messages.no_concesion')}}" type="text" id="no_concesion_filter" name="no_concesion_filter" class="form-control filters">  
            </div>
            <div class="col-sm-3">
                <label for="user_filter" class="form-label">{{__('messages.user')}}: </label>
                <select class="form-control filters" id="user_filter" name="user_filter" autocomplete="user_filter">
                    <option value="">{{__('messages.todos')}}</option>
                    @foreach ($users as $item)
                        <option value="{{$item->usuario}}">{{$item->usuario}}</option>
                    @endforeach
                </select>    
            </div>
            <div class="col-sm-3">
                <label for="fecha_ini_filter" class="form-label">{{__('messages.fecha_ini')}}: </label>
                <input placeholder="{{__('messages.fecha_ini')}}" type="date" id="fecha_ini_filter" name="fecha_ini_filter" data-date-format="dd/mm/yyyy" class="form-control datepicker filters">  
            </div>
            <div class="col-sm-3">
                <label for="fecha_fin_filter" class="form-label">{{__('messages.fecha_fin')}}: </label>
                <input placeholder="{{__('messages.seleccionar_fecha_fin')}}" type="date" id="fecha_fin_filter" name="fecha_fin_filter" data-date-format="dd/mm/yyyy" class="form-control datepicker filters">   
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-sm-2 offset-md-8">
                @if(verifyPermission('reportes.reporte_de_movimientos_de_concesion.exportar'))
                <button type="button" id="btn_export_pdf" class="btn" style="color:#0d6efd"><i class="fas fa-print"> {{__("messages.export_pdf")}}</i></button>
                @endif
            </div>
            <div class="col-sm-2">
                @if(verifyPermission('reportes.reporte_de_movimientos_de_concesion.exportar'))
                <button type="button" id="btn_export" class="btn" style="color:#0d6efd"><i class="fas fa-print"> {{__("messages.export_excel")}}</i></button>
                @endif
            </div>
        </div>
    </form>
    <br>
    <div class="row">
        <div class="col-sm-12">
            <table id="catalogo" class="table table-striped table-bordered text-center " style="width:100%">
                <thead>
                    <tr class="colorMorado">
                        <th>{{ __('messages.no_concesion') }}</th>
                        <th>{{ __('messages.user') }}</th>
                        <th>{{ __('messages.movimiento') }}</th>
                        <th>{{ __('messages.datos') }}</th>
                        <th>{{ __('messages.fecha_hora') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@isset($dataSet)
    @include('panels.datatable')
@endisset

<script type="text/javascript">
    //inicializamos el data table
    $(document).ready(function() {
        getData();

        $(".container").on('click','#btn_export',function(e){
            e.preventDefault();
            $("#exportForm").find("#no_concesion_export").val($("#buscarForm").find("#no_concesion_filter").val());
            $("#exportForm").find("#user_export").val($("#buscarForm").find("#user_filter").val());
            $("#exportForm").find("#fecha_ini_export").val($("#buscarForm").find("#fecha_ini_filter").val());
            $("#exportForm").find("#fecha_fin_export").val($("#buscarForm").find("#fecha_fin_filter").val());
            $("#exportForm").submit();
        });

        $(".container").on('click','#btn_export_pdf',function(e){
            e.preventDefault();
            $("#exportFormPdf").find("#no_concesion_export_pdf").val($("#buscarForm").find("#no_concesion_filter").val());
            $("#exportFormPdf").find("#user_export_pdf").val($("#buscarForm").find("#user_filter").val());
            $("#exportFormPdf").find("#fecha_ini_export_pdf").val($("#buscarForm").find("#fecha_ini_filter").val());
            $("#exportFormPdf").find("#fecha_fin_export_pdf").val($("#buscarForm").find("#fecha_fin_filter").val());
            $("#exportFormPdf").submit();
        });

        $("#buscarForm").on('change',"#fecha_ini_filter",function(){
            date_ini = $(this).val();
            $("#buscarForm").find("#fecha_fin_filter").attr('min', date_ini);
        });
    });
</script>
@endsection