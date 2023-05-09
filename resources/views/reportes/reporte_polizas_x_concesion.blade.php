@extends('layouts.app')
<?php
$stName = __('messages.nombre_sistema');
$acr = 'COCOTRA';

?>
@section('content')
<div class="container w-100 p-4">
    <h3 style="text-align: center; font-weight: bold;">{{ __('messages.reporte_polizas_x_concesion') }}</h3>
    <br>
    <form action="{{route('export_reporte_polizas_x_concesion_pdf')}}" id="exportFormPdf" method="post">
        @csrf
        <input type="hidden" id="tipo_servicio_export_pdf" name="tipo_servicio_filter" class="form-control">
        <input type="hidden" id="grupo_export_pdf" name="grupo_filter" class="form-control">
        <input type="hidden" id="modalidad_export_pdf" name="modalidad_filter" class="form-control">
        <input type="hidden" id="aseguradora_export_pdf" name="aseguradora_filter" class="form-control">
        {{--<input type="hidden" id="estatus_export_pdf" name="estatus_filter" class="form-control">--}}
        <input type="hidden" id="fecha_ini_export_pdf" name="fecha_ini_filter" class="form-control">
        <input type="hidden" id="fecha_fin_export_pdf" name="fecha_fin_filter" class="form-control">
    </form>
    <form action="{{route('export_reporte_polizas_x_concesion')}}" id="exportForm" method="post">
        @csrf
        <input type="hidden" id="tipo_servicio_export" name="tipo_servicio_filter" class="form-control">
        <input type="hidden" id="grupo_export" name="grupo_filter" class="form-control">
        <input type="hidden" id="modalidad_export" name="modalidad_filter" class="form-control">
        <input type="hidden" id="aseguradora_export" name="aseguradora_filter" class="form-control">
        {{--<input type="hidden" id="estatus_export" name="estatus_filter" class="form-control">--}}
        <input type="hidden" id="fecha_ini_export" name="fecha_ini_filter" class="form-control">
        <input type="hidden" id="fecha_fin_export" name="fecha_fin_filter" class="form-control">
    </form>
    <form action="{{ route('get_reporte_polizas_x_concesion') }}" id="buscarForm" method="post">
        @csrf
        <div class="row">
            <div class="col-sm-3">
                <label for="tipo_servicio_filter" class="form-label">{{__('messages.tipo_servicio')}}: </label>
                <select class="form-control filters" id="tipo_servicio_filter" name="tipo_servicio_filter" autocomplete="tipo_servicio_filter">
                    <option value="">{{__('messages.todos')}}</option>
                    @foreach ($tipo_servicio as $key=>$item)
                        <option value="{{$item->tipo_servicio}}">{{$item->tipo_servicio}}</option>
                    @endforeach
                </select>    
            </div>
            <div class="col-sm-2">
                <label for="modalidad_filter" class="form-label">{{__('messages.modalidad')}}: </label>
                <select class="form-control filters" id="modalidad_filter" name="modalidad_filter" autocomplete="modalidad_filter">
                    <option value="">{{__('messages.todas')}}</option>
                    @foreach ($modalidad as $item)
                        <option value="{{$item->modalidad}}">{{$item->modalidad}}</option>
                    @endforeach
                </select>    
            </div>
            <div class="col-sm-2">
                <label for="grupo_filter" class="form-label">{{__('messages.grupo')}}: </label>
                <select class="form-control filters" id="grupo_filter" name="grupo_filter" autocomplete="grupo_filter">
                    <option value="">{{__('messages.todos')}}</option>
                    @foreach ($grupo as $key=>$item)
                        <option value="{{$key}}">{{$item}}</option>
                    @endforeach
                </select>    
            </div>
            <div class="col-sm-2">
                <label for="aseguradora_filter" class="form-label">{{__('messages.aseguradora')}}: </label>
                <select class="form-control filters" id="aseguradora_filter" name="aseguradora_filter" autocomplete="aseguradora_filter">
                    <option value="">{{__('messages.todas')}}</option>
                    @foreach ($aseguradora as $item)
                        <option value="{{$item->id}}">{{$item->nombre}}</option>
                    @endforeach
                </select>    
            </div>
            {{--<div class="col-sm-2">
                <label for="estatus_filter" class="form-label">{{__('messages.estatus_poliza')}}: </label>
                <select class="form-control filters" id="estatus_filter" name="estatus_filter" autocomplete="estatus_filter">
                    <option value="">{{__('messages.todos')}}</option>
                    @foreach ($estatus as $key=>$item)
                        <option value="{{$key}}">{{$item}}</option>
                    @endforeach
                </select>    
            </div>--}}
            <div class="col-sm-3">
                <label for="user_creacion_filter" class="form-label">{{__('messages.user_creacion')}}: </label>
                <select class="form-control filters" id="user_creacion_filter" name="user_creacion_filter" autocomplete="estatus_poliza_filter">
                    <option value="">{{__('messages.todos')}}</option>
                    @foreach ($users_creacion as $item)
                        <option value="{{$item->created_by}}">{{$item->created_by}}</option>
                    @endforeach
                </select>    
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-sm-3">
                <label for="fecha_ini_filter" class="form-label">{{__('messages.fecha_ini')}}: </label>
                <input placeholder="{{__('messages.fecha_ini')}}" type="date" id="fecha_ini_filter" name="fecha_ini_filter" data-date-format="dd/mm/yyyy" class="form-control datepicker filters">  
            </div>
            <div class="col-sm-3">
                <label for="fecha_fin_filter" class="form-label">{{__('messages.fecha_fin')}}: </label>
                <input placeholder="{{__('messages.seleccionar_fecha_fin')}}" type="date" id="fecha_fin_filter" name="fecha_fin_filter" data-date-format="dd/mm/yyyy" class="form-control datepicker filters">   
            </div>
            <div class="col-sm-2 offset-md-2">
                @if(verifyPermission('reportes.reporte_de_polizas_de_seguro_por_concesion.exportar'))
                <button type="button" id="btn_export_pdf" class="btn" style="color:#0d6efd"><i class="fas fa-print"> {{__("messages.export_pdf")}}</i></button>
                @endif
            </div>
            <div class="col-sm-2">
                @if(verifyPermission('reportes.reporte_de_polizas_de_seguro_por_concesion.exportar'))
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
                        <th>{{ __('messages.nombre_propietario') }}</th>
                        <th>{{ __('messages.no_concesion') }}</th>
                        <th>{{ __('messages.tipo_servicio') }}</th>
                        <th>{{ __('messages.grupo') }}</th>
                        <th>{{ __('messages.modalidad') }}</th>
                        <th>{{ __('messages.num_poliza') }}</th>
                        <th>{{ __('messages.aseguradora') }}</th>
                        <th>{{ __('messages.fecha_vencimiento_poliza') }}</th>
                        <th>{{ __('messages.user_creacion') }}</th>
                        {{--<th>{{ __('messages.estatus_poliza') }}</th>--}}
                        <th>{{ __('messages.observaciones') }}</th>
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
            $("#exportForm").find("#tipo_servicio_export").val($("#buscarForm").find("#tipo_servicio_filter").val());
            $("#exportForm").find("#grupo_export").val($("#buscarForm").find("#grupo_filter").val());
            $("#exportForm").find("#aseguradora_export").val($("#buscarForm").find("#aseguradora_filter").val());
            $("#exportForm").find("#modalidad_export").val($("#buscarForm").find("#modalidad_filter").val());
            $("#exportForm").find("#fecha_ini_export").val($("#buscarForm").find("#fecha_ini_filter").val());
            $("#exportForm").find("#fecha_fin_export").val($("#buscarForm").find("#fecha_fin_filter").val());
            $("#exportForm").find("#estatus_export").val($("#buscarForm").find("#estatus_filter").val());
            $("#exportForm").submit();
        });

        $(".container").on('click','#btn_export_pdf',function(e){
            e.preventDefault();
            $("#exportFormPdf").find("#tipo_servicio_export_pdf").val($("#buscarForm").find("#tipo_servicio_filter").val());
            $("#exportFormPdf").find("#grupo_export_pdf").val($("#buscarForm").find("#grupo_filter").val());
            $("#exportFormPdf").find("#aseguradora_export_pdf").val($("#buscarForm").find("#aseguradora_filter").val());
            $("#exportFormPdf").find("#modalidad_export_pdf").val($("#buscarForm").find("#modalidad_filter").val());
            $("#exportFormPdf").find("#fecha_ini_export_pdf").val($("#buscarForm").find("#fecha_ini_filter").val());
            $("#exportFormPdf").find("#fecha_fin_export_pdf").val($("#buscarForm").find("#fecha_fin_filter").val());
            $("#exportFormPdf").find("#estatus_export_pdf").val($("#buscarForm").find("#estatus_filter").val());
            $("#exportFormPdf").submit();
        });

        $("#buscarForm").on('change',"#fecha_ini_filter",function(){
            date_ini = $(this).val();
            $("#buscarForm").find("#fecha_fin_filter").attr('min', date_ini);
        });
    });
</script>
@endsection