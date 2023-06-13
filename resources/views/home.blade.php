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
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        getDatos();
        
    });

    function getDatos(){
        var tabla = $("#catalogo");
        var tabla_b = $("#catalogoB");

        try{
            $.ajax({
            url:"{{route('inicio_a')}}",
            type: "POST",
            dataType: 'json',
            processData: false,
            contentType: false,
            success:function(response){
                response = response.dataSet;
                var dt = $(tabla);
                if(response.length == 0){
                    dt.attr('data-empty','true');
                }
                else{
                    dt.attr('data-empty','false');
                }
                dt.DataTable({
                   data: response,
                   pageLength:10,
                   scrollX: true,
                   autoWidth: false,
                   processing: true,
                   order: [],
                   ServerSide: true,
                   api:true,
                   language: {
                       processing: "Procesando...",
                       lengthMenu: "Mostrar _MENU_ registros",
                       zeroRecords: "No se encontraron resultados",
                       emptyTable: "Ningún dato disponible en esta tabla",
                       info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                       infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                       infoFiltered: "(filtrado de un total de _MAX_ registros)",
                       search: "Búsqueda:",
                       infoThousands: ",",
                       loadingRecords: "Cargando...",
                       buttonText: "Imprimir",
                       paginate: {
                           first: "Primero",
                           last: "Último",
                           next: "Siguiente",
                           previous: "Anterior",
                       },
                       buttons: {
                           copyTitle: 'Copiado al portapapeles',
                           copySuccess: {
                               _: '%d registros copiados',
                               1: 'Se copio un registro'
                           }
                       },
                   },
                   columnDefs: [
                       {
                           targets: [0,1,2,3],
                           className: 'text-right'
                       },
                   ],
                  
               });
            },
            error: function(response) {
                var mensaje="";
                $.each(response.responseJSON.errors, function( key, value ) {
                    mensaje += value+"\n";
                });
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: mensaje,
                    confirmButtonText: "Aceptar",
                });
                //$('#errorModal').modal('show');
                console.log('Error: ' +  JSON.stringify(response.responseJSON));
            }
        });
        }catch (error) {
            console.error("error: "+error);
        }

        try{
            
            $.ajax({
            url:"{{route('inicio_b')}}",
            type: "POST",
            dataType: 'json',
            processData: false,
            contentType: false,
            success:function(response){
                response = response.dataSet;
                var dt = $(tabla_b);
                if(response.length == 0){
                    dt.attr('data-empty','true');
                }
                else{
                    dt.attr('data-empty','false');
                }
                dt.DataTable({
                   data: response,
                   pageLength:10,
                   scrollX: true,
                   autoWidth: false,
                   processing: true,
                   order: [],
                   ServerSide: true,
                   api:true,
                   language: {
                       processing: "Procesando...",
                       lengthMenu: "Mostrar _MENU_ registros",
                       zeroRecords: "No se encontraron resultados",
                       emptyTable: "Ningún dato disponible en esta tabla",
                       info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                       infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                       infoFiltered: "(filtrado de un total de _MAX_ registros)",
                       search: "Búsqueda:",
                       infoThousands: ",",
                       loadingRecords: "Cargando...",
                       buttonText: "Imprimir",
                       paginate: {
                           first: "Primero",
                           last: "Último",
                           next: "Siguiente",
                           previous: "Anterior",
                       },
                       buttons: {
                           copyTitle: 'Copiado al portapapeles',
                           copySuccess: {
                               _: '%d registros copiados',
                               1: 'Se copio un registro'
                           }
                       },
                   },
                   columnDefs: [
                       {
                           targets: [0,1,2,3],
                           className: 'text-right'
                       },
                   ],
                  
               });
            },
            error: function(response) {
                var mensaje="";
                $.each(response.responseJSON.errors, function( key, value ) {
                    mensaje += value+"\n";
                });
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: mensaje,
                    confirmButtonText: "Aceptar",
                });
                //$('#errorModal').modal('show');
                console.log('Error: ' +  JSON.stringify(response.responseJSON));
            }
        });
        }catch (error) {
            console.error("error: "+error);
        }
    }

    
</script>
@endsection


