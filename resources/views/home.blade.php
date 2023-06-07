@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <button type="button" class="btn colorMorado"
            name="button_modal_carga" id="button_modal_carga">
      <i class="fas fa-plus"></i>
      {{ __('messages.carga_masiva') }}</button>
            <div class="card">
                <div class="card-header">{{ __('Bienvenido') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('Usted ha iniciado sesión en  Comisión Coordinadora del Transporte Público de Michoacán') }}
                </div>
                
            </div>
            <table id="catalogo" class="table table-striped table-bordered text-center " style="width:100%">
                <thead>
                    <tr class="colorMorado">
                        <th>{{__("messages.presupuesto_asignado")}}</th>
                        <th>{{__("messages.presupuesto_calendarizado")}}</th>
                        <th>{{__("messages.disponible")}}</th>
                        <th>{{__("messages.avance")}}</th>
                    </tr>
                </thead>
            </table>
            <br>
            <table id="catalogo" class="table table-striped table-bordered text-center " style="width:100%">
                <thead>
                    <tr class="colorMorado">
                        <th>{{__("messages.clave_fondo")}}</th>
                        <th>{{__("messages.fondo")}}</th>
                        <th>$ {{__("messages.asignado")}}</th>
                        <th>$ {{__("messages.programado")}}</th>
                        <th>% {{__("messages.avance")}}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@include('.CargamasivaModal')

<script type="text/javascript">
    //boton para api soap

    $(".container").on('click', '#button_modal_carga', function () {
        $('#ModalCargaMasiva').modal('show');
    })
    </script>


@endsection

