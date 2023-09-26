<?php
    $titleDesc = __("messages.cat_epp");
    //{{$titleDesc}}
?>
@extends('layouts.app')

@section('content')

    <!--Tabla de resultados-->
    <div class="container w-100 p-4">
        <header class="d-flex justify-content-center" style=" border-bottom: 5px solid #17a2b8;">
            <h2>{{__("messages.cat_epp")}}</h2>
        </header>
        <br>
        @if($perfil == 1 || $perfil == 3 || $perfil == 5)
            <div class="col-md-10 col-sm-12 d-md-flex">
                <!--Filtro UPP-->
                <div>
                    <label for="estatus_filter" class="form-label fw-bold">UPP:</label>
                </div>
                <div class="col-sm-12 col-md-3 col-lg-2">
                    <select onchange="actualizarTabla(true)" class="form-control filters filters_upp" id="filters_upp" name="estatus_filter" autocomplete="upp_filter">
                        <option value="000">Todos</option>
                        @foreach ($listaUpp as $upp)
                            <option value={{$upp->clv_upp}}>{{$upp->clv_upp}} - {{$upp->upp}}</option>
                        @endforeach
                    </select>
                </div>
                <!--Filtro UR-->
                <div>
                    <label for="estatus_filter" class="form-label fw-bold">UR:</label>
                </div>
                <div class="col-sm-12 col-md-3 col-lg-2">
                    <select onchange="actualizarTabla(false)" class="form-control filters filters_ur" id="filters_ur" name="estatus_filter" autocomplete="ur_filter">
                        <option value="00">Todos</option>
                    </select>
                </div>
                <!--Filtro de año-->
                <div>
                    <label for="estatus_filter" class="form-label fw-bold">AÑO:</label>
                </div>
                <div class="col-sm-12 col-md-3 col-lg-2">
                    <select onchange="actualizarTabla(false)" class="form-control filters filters_anio" id="filters_anio" name="estatus_filter" autocomplete="upp_filter">
                        <?php $i = 0; $len = count($anios); ?>
                        @foreach ($anios as $anio)
                            <option value={{$anio->ejercicio}} <?php $i++; if($i == $len){echo("selected");} ?>>{{$anio->ejercicio}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

        <form action="{{ route('get-epp', ['anio'=>'0000', 'upp'=>'000', 'ur'=>'00']) }}" id="buscarForm" method="post">
            @csrf
        </form>
       
        <table id="catalogo" class="table table-striped table-bordered text-center display" style="width:100%">
            <thead>
                <tr class="colorMorado">
                    <th>{{__("messages.clasificacion_administrativa")}}</th>
                    <th>{{__("messages.upp")}}</th>
                    <th>{{__("messages.subsecretaria")}}</th>
                    <th>{{__("messages.ur")}}</th>
                    <th>{{__("messages.finalidad")}}</th>
                    <th>{{__("messages.funcion")}}</th>
                    <th>{{__("messages.subfuncion")}}</th>
                    <th>{{__("messages.eje")}}</th>
                    <th>{{__("messages.linea_accion")}}</th>
                    <th>{{__("messages.programa_sectorial")}}</th>
                    <th>{{__("messages.tipologia_conac")}}</th>
                    <th>{{__("messages.programa")}}</th>
                    <th>{{__("messages.subprograma")}}</th>
                    <th>{{__("messages.proyecto")}}</th>
                    <th>{{__("messages.anio")}}</th>
                </tr>
            </thead>
        </table>
    </div>

    @isset($dataSet)
    @include('panels.datatable_epp')
    @endisset
    <script src="{{ asset('vendor/sweetalert/sweetalert.all.js') }}"></script>
    <script src="/js/epp/utils.js"></script>
@endsection