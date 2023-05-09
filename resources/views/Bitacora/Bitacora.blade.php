@extends('configuracion.index')

@section('content_configuraciones')

<!-- listado Bitacora -->
<div class="container w-100 p-4">
    <h5 style="text-align: left; font-weight: bold;">{{__("messages.bitacora_accesos")}}</h5>
    <form action="{{route('bitacora_export')}}" id="exportForm" method="post">
        @csrf
        <input type="hidden" id="anio_export" name="anio_filter" class="form-control">
        <input type="hidden" id="anio_export_fin" name="anio_filter_fin" class="form-control">
        <input type="hidden" id="usuario_export" name="usuario_filter" class="form-control">
        <input type="hidden" id="accion_export" name="accion_filter" class="form-control">
    </form>
    <form action="{{route('get_bitacora')}}" id="buscarForm" method="post">
         @csrf
        <div class="row">
            <div class="col-sm-3">
                <label for="anio_filter" class="form-label">{{__('messages.fecha_ini')}}: </label>
                <input class="form-control filters" id="anio_filter" name="anio_filter" type="date"/>
            </div>
            <div class="col-sm-3">
                <label for="anio_filter" class="form-label">{{__('messages.fecha_fin')}}: </label>
                <input type="date" class="form-control filters" id="anio_filter_fin" name="anio_filter_fin">
            </div>
            <div class="col-sm-2">
                <label for="usuario_filter" class="form-label">{{__('messages.user')}}: </label>
                <select class="form-control filters" id="usuario_filter" name="usuario_filter" autocomplete="usuario_filter">
                    <option value="">{{__('messages.todos')}}</option>
                    @foreach ($usuarios as $usuario)
                        <option value="{{$usuario->usuario}}" >{{$usuario->usuario}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <label for="accion_filter" class="form-label">{{__('messages.accion')}}: </label>
                <select class="form-control filters" id="accion_filter" name="accion_filter" autocomplete="accion_filter">
                    <option value="">{{__('messages.todos')}}</option>
                    @foreach ($acciones as $accion)
                        <option value="{{$accion->accion}}" >{{$accion->accion}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <form action="{{route('bitacora_export')}}">
                    <button type="button" id="btn_export" class="btn" style="color:#0d6efd; text-align: right;"><i class="fas fa-plus">{{__("messages.export_excel")}}</i></button>
                </form>
            </div>
        </div>
        
        <div class="col-6">
            <form action="" method="get">
                <div class="row"> 
                    <select class="form-control filters" name="anio_hidden" id="anio_hidden" value="2022" hidden="true">
                        <option value="2022" id="pr" selected></option>
                        <option value="2023" id="pr" selected></option>
                    </select>
                </div> 
            </form> 
        </div>
    </form>  

    <br>
    <div class="row">
        <div class="col-sm-12">
            <table id="catalogo" class="table table-striped table-bordered text-center " style="width:100%">
                <thead>
                    <tr class="colorMorado">
                        <th>{{__("messages.user")}}</th>
                        <th>{{__("messages.host")}}</th>
                        <th>{{__("messages.modulo")}}</th>
                        <th>{{__("messages.accion")}}</th>
                        <th>{{__("messages.datos")}}</th>
                        <th>{{__("messages.creado_fecha")}}</th>
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
    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }    
        });
    
        date = new Date();
        getData();

        $(".container").on('click','#btn_export',function(e){
            e.preventDefault();
            $("#exportForm").find("#anio_export").val($("#buscarForm").find("#anio_filter").val());
            $("#exportForm").find("#anio_export_fin").val($("#buscarForm").find("#anio_filter_fin").val());
            $("#exportForm").find("#usuario_export").val($("#buscarForm").find("#usuario_filter").val());
            $("#exportForm").find("#accion_export").val($("#buscarForm").find("#accion_filter").val());
            $("#exportForm").submit();
        });

        $("#municipio_filter").on('change',()=>{
            $("#municipio_export option[value='just'").attr("selected",true);
            $('#municipio_export').val($('#municipio_filter').val());
        }); 

        $("#usuario_filter").on('change',()=>{
            $("#usuario_export option[value='just'").attr("selected",true);
            $('#usuario_export').val($('#usuario_filter').val());
        }); 

        $("#accion_filter").on('change',()=>{
            $("#accion_export option[value='just'").attr("selected",true);
            $('#accion_export').val($('#accion_filter').val());
        }); 
    });       
</script>

@endsection