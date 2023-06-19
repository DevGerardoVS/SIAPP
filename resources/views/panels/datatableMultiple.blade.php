@section('page_scripts')
<script type="text/javascript">


    function getDataFechaCorte(anio) { //función para actualizar el select fechas de corte
        $.ajax({
            url: "/Reportes/data-fecha-corte/"+ anio,
            type:'POST',
            dataType: 'json',
            success: function(data) {
                var par = $('#fechaCorte_filter');
                par.html('');
                par.append(new Option("Elegir fecha de corte", ""));
                $.each(data, function(i, val){
                    par.append(new Option(data[i].deleted_at, data[i].deleted_at));
                });
            }
        });
    }

    function getData(tabla, rt){
       

       var dt = $(tabla);
       var orderDt = "";
       var column = "";
       var ruta;
       var formatRight = [];
        var formatLeft = [];
        var formatCenter = [];
        var bold = [];
         
       switch(rt){
           case "A":
               ruta = "#buscarFormA";
               break;
           case "B":
               ruta = "#buscarFormB";
               break;
           default:
               break;
       }

        if(dt.attr('data-bold')!=undefined){
            bold = dt.attr('data-bold').split(",");
            for(var i in bold){
                if(bold[i] != ""){
                    bold[i] = parseInt(bold[i]);
                }
            }
        }

        if(dt.attr('data-right')!=undefined){
            formatRight = dt.attr('data-right').split(",");
            for(var i in formatRight){
                if(formatRight[i] != ""){
                    formatRight[i] = parseInt(formatRight[i]);
                }
            }
        }

        if(dt.attr('data-left')!=undefined){
            formatLeft = dt.attr('data-left').split(",");
            for(var i in formatLeft){
                if(formatLeft[i] != ""){
                    formatLeft[i] = parseInt(formatLeft[i]);
                }
            }
        }

        if(dt.attr('data-center')!=undefined){
            formatCenter = dt.attr('data-center').split(",");
            for(var i in formatCenter){
                if(formatCenter[i] != ""){
                    formatCenter[i] = parseInt(formatCenter[i]);
                }
            }
        }
       
       var formData = new FormData();
       var csrf_tpken = $("input[name='_token']").val();
       var anio = $("#anio_filter").val();
       var fecha = $("#fechaCorte_filter").val();
    //    if(fecha != null && fecha != "null") fecha = fecha + " 00:00:00";
    //    console.log(fecha);

       formData.append("_token",csrf_tpken);
       formData.append("anio",anio);
       formData.append("fecha",fecha);

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
                    searching: true,
                    autoWidth: true,
                    ordering: true,
                    processing: true,
                    // serverSide: true,
                    pageLength: 10,
                    dom: 'frltip',
                    scrollX: true,
                    "lengthMenu": [10, 25, 50, 75, 100, 150, 200],
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
                            defaultContent: "-",
                            targets: "_all"
                        },
                        {
                            targets: formatRight,
                            className: 'text-right'
                        },
                        {
                            targets: formatLeft,
                            className: 'text-left'
                        },
                        {
                            targets: formatCenter,
                            className: 'text-center'
                        },
                    ],
                    // Poner el scroll debajo del footer 
                    "fnInitComplete": function(){
                        // Disable TBODY scoll bars
                        $('.dataTables_scrollBody').css({
                            'overflow': 'hidden',
                            'border': '0'
                        });

                        // Habilitar la barra de scroll en el tfoot
                        $('.dataTables_scrollFoot').css('overflow', 'auto');

                        // Sincronizar la barra de scroll con la body
                        $('.dataTables_scrollFoot').on('scroll', function () {
                            $('.dataTables_scrollBody').scrollLeft($(this).scrollLeft());
                        });     
                    },
                    // obtener la suma total
                    footerCallback: function (row, data, start, end, display) {
                        var api = this.api();
            
                        // Cambiar el formato string a entero
                        var intVal = function (i) {
                            return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                        };
            
                        // Suma total de todas las páginas
                        total = api
                            .column(".sum")
                            .data()
                            .reduce(function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
            
                        // Total sobre la página actual
                        pageTotal = api
                            .column(".sum", { page: 'current' })
                            .data()
                            .reduce(function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
            
                        // Actualizar footer
                        $(api.column(".sum").footer()).html( total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") );
                    },
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