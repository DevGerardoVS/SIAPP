
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
<div class="form-group d-flex align-items-center col-sm-12 col-md-4 col-lg-2" style="display:flex;align-items:center">
    <label class="form-label fw-bold mt-md-1" style="margin-right: 10px">Ejercicio:</label>
    <select class="form-control filters filters_fondo" id="ejercicio_filter" name="ejercicio_filter" autocomplete="ejercicio_filter">
    </select>
</div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <table id="catalogo" class="table table-striped table-bordered text-center " style="width:100%">
                <thead>
                    <tr class="colorMorado">
                        <th>$ {{__("messages.presupuesto_asignado")}}</th>
                        <th>$ {{__("messages.presupuesto_calendarizado")}}</th>
                        <th>$ {{__("messages.disponible")}}</th>
                        <th>% {{__("messages.avance")}}</th>
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

        
        getFondos(); 

        $("#ejercicio_filter").on('change', function(){
            getFondos(); 

        });

    });
    function exportPdf(){
        var anio = $("#ejercicio_filter").val();
        _url = "/export-Pdf/"+anio;
        window.location.href = _url;
    }

    function exportExcel(){
        var anio = $("#ejercicio_filter").val();
        //console.log(anio);
        _url = "/export-Excel/"+anio;
        window.location.href = _url;
       
    }

    function getDatos(){
        var tabla = $("#catalogo");
        var tabla_b = $("#catalogoB");

        var anio = $("#ejercicio_filter").val();
        var formData = new FormData();
        formData.append("anio",anio);

        tabla.DataTable().destroy();
        tabla_b.DataTable().destroy();
        
        try{
            $.ajax({
            url:"{{route('inicio_a')}}",
            type: "POST",
            data: formData,
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
            data: formData,
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
        
        var anio = $("#ejercicio_filter").val();

        var formData = new FormData();
        formData.append("anio",anio);
        
        $.ajax({
            url: "/fondos/inicio",
            data: formData,
            type:'POST',
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(data) {
                //console.log(data);
                try {
                    var par = $('#fondo_filter');
                    var anios = $("#ejercicio_filter");

                    var fondos = data.fondos;
                    
                    par.html('');
                    par.append(new Option("Todos", ""));
                    $.each(fondos, function(i, val){
                        par.append(new Option(fondos[i].clv_fondo +" "+ fondos[i].fondo_ramo, fondos[i].clv_fondo+" "+ fondos[i].fondo_ramo));
                    });

                    var ejercicios = data.ejercicios;

                    $('#anio').text(ejercicios[0].ejercicio);

                    anios.empty();

                    $.each(ejercicios, function(i, val){
                        anios.append(new Option(ejercicios[i].ejercicio,ejercicios[i].ejercicio));
                        
                        if(anio==ejercicios[i].ejercicio){
                            //console.log(anio+" "+ejercicios[i].ejercicio);
                            $("#ejercicio_filter option[value="+anio+"]").prop('selected',true);
                        } 
                    });
                } catch (error) {
                    
                }
                
                getDatos();

            }
        });
    }


</script>
@endsection
@endif