@extends('layouts.app')

@section('content')
<div class="container">
    <form action="{{ route('getBitacora') }}" id="buscarForm" method="POST">
        @csrf
        <input style="display: none" type="text" id="fecha" name="fecha">
    </form>
    <br>
    <header>
        <h2>Inicio</h2>

    </header>


    <div class="row justify-content-center">
        <div class="col-md-8">
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
        </div>
    </div>
    <br>
    <div class="row justify-content-center">
        <div class="col-sm-12">
            <table id="catalogoB" class="table table-striped table-bordered text-center " style="width:100%">
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

<script src="https://momentjs.com/downloads/moment.js"></script>
@include('panels.datatable')
<script>

</script>
@endsection


