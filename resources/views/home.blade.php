
@if (Auth::user()->id_grupo !=1)
<script type="text/javascript">
    window.location.href = "/calendarizacion/claves";
 </script>
@else
@extends('layouts.app')

@section('content')
<br>
<div class="container">
    <header class="d-flex justify-content-center" style=" border-bottom: 5px solid #17a2b8;">
        <h2>Inicio</h2>
    </header>
<br>

<div class="col-sm-12 col-md-4 col-lg-2">
    <label class="form-label fw-bold mt-md-1">Ejercicio:</label>
    <label class="form-label fw-bold mt-md-1" id="anio"></label>
</div>
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
    <div class="row">
            <div class="col-sm-2 text-md-end my-auto">
                <label for="fondo_filter" class="form-label fw-bold mt-md-1">Fondo:</label>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-6 my-auto">
                <select class="form-control filters filters_fondo" id="fondo_filter" name="fondo_filter" autocomplete="fondo_filter">
                </select>
            </div>
            <div class="text-right col-sm-12 col-md-12 col-lg-4 mt-sm-0 mt-2">
                <button type="button" class="btn btn-outline-success"  onclick="exportExcel()">
                    <i class="fa fa-file-excel-o"></i> Exportar Excel
                </button>
                <button style="margin: 10px;" type="button" class="btn btn-outline-danger" onclick="exportPdf()">
                    <i class="fa fa-file-pdf-o"></i> Exportar PDF
                </button>
            </div>
    </div>
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
        var dt = $('#catalogoB');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#fondo_filter').on('change', function(){
            dt.DataTable().search(this.value).draw();   
        });

        getDatos();
        getFondos();

    });
    function exportPdf(){
        _url = "/export-Pdf";
        window.location.href = _url;
    }

    function exportExcel(){
        _url = "/export-Excel";
        window.location.href = _url;
    }

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
                   searching: false,
                   paging: false,
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
                   paging: false,
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
                           targets: [1],
                           className: 'text-left'
                       },
                       {
                           targets: [0,2,3,4],
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

    function getFondos() { //función obtener fondos
        $.ajax({
            url: "/fondos/inicio",
            type:'POST',
            dataType: 'json',
            success: function(data) {
                var par = $('#fondo_filter');
                $('#anio').text(data[0].ejercicio);
                par.html('');
                par.append(new Option("Todos", ""));
                $.each(data, function(i, val){
                    par.append(new Option(data[i].clv_fondo_ramo +" "+ data[i].fondo_ramo, data[i].clv_fondo_ramo+" "+ data[i].fondo_ramo));
                });
            }
        });
    }


</script>
@endsection
@endif