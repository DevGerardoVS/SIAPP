<?php
    $titleDesc = __("messages.cat_epp");
    //{{$titleDesc}}
?>
@extends('layouts.app')

@section('content')

    <!--Tabla de resultados-->
    <div class="container w-100 p-4">
        <h5 style="text-align: left; font-weight: bold;">{{__("messages.cat_epp")}}</h5>
        
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
        <br>
       
        <table id="catalogo" class="table table-striped table-bordered text-center " style="width:100%">
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
    @include('panels.datatable')
    @endisset
    <script src="{{ asset('vendor/sweetalert/sweetalert.all.js') }}"></script>
    <script type="text/javascript">
        //inicializamos el data table

        $(document).ready(function() {
            getData();

            $("buscarForm").keypress(function(e) {
                //Enter key
                if (e.which == 13) {
                    return false;
                }
            });
        });

        function actualizarTabla(updateUR){
            var e = document.getElementById("filters_upp");
            var upp = e.value;

            var e = document.getElementById("filters_ur");
            var ur = e.value;

            var e = document.getElementById("filters_anio");
            var anio = e.value;
            
            //RECARGAR TABLA
            var opt = document.getElementById("buscarForm");
            var largo = opt.action.length - 11;
            var accion = opt.action.substring(0,largo)+anio+"/"+upp;
            if(updateUR) actualizarListaUR(upp);

            accion += "/" + ur;
            opt.action = accion;
            //console.log(accion);
            getData();
        }

        function actualizarListaUR(clv_upp){
            let select = document.getElementById("filters_ur");
            select.options.length = 1;

            $.ajax({
                url: "{{ route('get-ur') }}",
                data: {upp: clv_upp},
                type:'POST',
                dataType: 'json',
                success: function(response) {
                    listaur = response.listaUR;
                    listaur.forEach((c) => {
                        var ur = c.clv_ur + " - " + c.ur;
                        var newOption = new Option(ur,c.clv_ur);
                        select.add(newOption,undefined);
                    });
                },
                error: function(response) {
                    console.log('Error: ' + response);
                }
            });
        }
    </script>

@endsection