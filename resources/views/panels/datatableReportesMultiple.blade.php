@section('page_scripts')
<script type="text/javascript">


    function getData(tabla, rt){
       var dt = $(tabla);
       var orderDt = "";
       var column = "";
       var formatCantidades = [];
       var ruta;
       var tittle;
       switch(rt){
           case "A":
               ruta = "#buscarFormA";
               tittle="Mich_Pre_1";
               formatCantidades = [2];
               break;
           case "B":
               ruta = "#buscarFormB";
               tittle="Mich_Pre_2";
               formatCantidades = [2,3,4,5,6];
               break;
           case "C":
               ruta = "#buscarFormC";
               tittle="Mich_Pre_3";
               formatCantidades = [2];

               break;
               case "D":
               ruta = "#buscarFormD";
               tittle="Mich_Pre_4";
               formatCantidades = [2,3,4,5,6];

               break;
               case "E":
               ruta = "#buscarFormE";
               tittle="Mich_Pre_5";
               formatCantidades = [2,3];

               break;
               case "F":
               ruta = "#buscarFormF";
               tittle="Mich_Pre_6";
               formatCantidades = [2,3,4];
               break;
           default:
               break;
       }

       var formData = new FormData();
        anio = $("#anio_filter").val();
       var consulta = $(tabla+'_val').val();
       $(tabla+'_Date').val(anio);
       var csrf_tpken = $("input[name='_token']").val();
       formData.append("_token",csrf_tpken);
       formData.append("anio",anio);
       formData.append("analisis",consulta);
       var titulo = 'Reporte '+tittle+' '+$("#anio_filter").val();

        $.ajax({
           url: $(ruta).attr("action"),
           data: formData,
            type:'POST',
            dataType: 'json',
            contentType: false,
            processData: false,
           success: function(response) {
               if(response.dataSet.length == 0){
                   dt.attr('data-empty','true');
               }
               else{
                   dt.attr('data-empty','false');
               }
               dt.DataTable({
                   data: response.dataSet,
                   searching: false,
                    paging: false,
                    ordering: false,
                    scrollX: true,
                    autoWidth: false,
                    processing: true,
                    ServerSide: true,
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
                   dom:'Bfrtip',
                   buttons:  [
                          {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i>', extend: 'excel', className: 'btn-success me-2', titleAttr:'Descargar excel',title: titulo,
                    className: 'btn-success'
                    },
                    {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i>', extend: 'pdf', className: 'btn-danger', titleAttr:'Descargar PDF' ,title: titulo,
                    className: 'btn-danger ms-1 '

                    }],
                    columnDefs: [
                        {
                            targets: formatCantidades,
                            className: 'text-right'
                        },
                    ],
                   footerCallback: function(row, data, start, end, display){
                       var api = this.api();
                       api.columns('.sum',{
                           page: 'current'
                       }).every(function(){
                           var sum = this.data().reduce(function(a,b){
                               var x = parseFloat(a) || 0;
                               if(b == null){
                                   b = "0.00";
                               }
                               var y = parseFloat(b.replaceAll(",","")) || 0;
                               return x + y;
                           },0);
                           sum = sum.toFixed(2);
                           $(this.footer()).html($(this.footer()).attr('data-title')+": "+sum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                       });
                   }
               });
               redrawTable(tabla);
            },
           error: function(response) {
               console.log('{{__("messages.error")}}: ' + response);
           }
       });
   }
   function redrawTable(tabla){
        dt = $(tabla);
        dt.DataTable().columns.adjust().draw();
        dt.children("thead").css("visibility","hidden");
    }

    

</script>
@endsection