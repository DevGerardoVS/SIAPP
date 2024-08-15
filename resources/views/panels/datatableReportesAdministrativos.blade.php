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
                var getLastYear = {!! json_encode($anios[0]->ejercicio) !!}; // Variable para obtener el último año de la tabla pp y pph
                if(getLastYear == anio) par.append(new Option("Actuales", "")); // Comprobar si el último año es igual al año del select y eliminar la opción de actuales para los años anteriores
                $.each(data, function(i, val){
                    var deleted_at = val.deleted_at.slice(0,-3);
                    var date = new Date(deleted_at);
                    var getCorrectDate = new Date(date.valueOf() + date.getTimezoneOffset() * 60000);
                    var formattedDate = ("0" + getCorrectDate.getDate()).slice(-2) + "-" + ("0" + (date.getMonth() + 1)).slice(-2) + "-" + date.getFullYear();

                    if(data[i].version == 0) par.append(new Option("Última versión", data[i].deleted_at));
                    else par.append(new Option("V"+ data[i].version +" - "+formattedDate , data[i].deleted_at)); 
                });
            }
        });
    }

    function getUPPAdministrativo(anio) { //función para actualizar el select UPP dependiendo el usuario
        $.ajax({
            url: "/Reportes/administrativos/data-upp/"+ anio,
            type:'POST',
            dataType: 'json',
            success: function(data) {
                var par = $('#upp_filter');
                par.html('');
                par.append(new Option("Todos", ""));
                $.each(data, function(i, val){
                    par.append(new Option(data[i].clv_upp+" - "+data[i].descripcion, data[i].clv_upp));
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
        var estatus = false;
         
       switch(rt){
           case "A":
               ruta = "#buscarFormA";
               break;
           case "B":
               ruta = "#buscarFormB";
               break;
           case "C":
               ruta = "#buscarFormC";
               break;
           case "D":
               ruta = "#buscarFormD";
               break;
           case "E":
               ruta = "#buscarFormE";
               break;
           case "F":
               ruta = "#buscarFormF";
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
       var fecha = !$("#fechaCorte_filter").val() ? "null" : $("#fechaCorte_filter").val();

       formData.append("_token",csrf_tpken);
       formData.append("anio",anio);
       formData.append("fecha",fecha);
       if(!$('.div_upp').hasClass('d-none')){
           var upp = !$("#upp_filter").val() ? "null" : $("#upp_filter").val();
           formData.append("upp",upp);
           var tipo = !$("#tipo_filter").val() ? "null" : $("#tipo_filter").val();
           formData.append("tipo",tipo);
        }

        $.ajax({
           url: $(ruta).attr("action"),
           data: formData,
            type:'POST',
            dataType: 'json',
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('.custom-swal').css('display', 'block');
            },
            complete: function(){
                $('.custom-swal').css('display', 'none');
            },
           success: function(response) {
                if(response.dataSet.length == 0){
                    dt.attr('data-empty','true');
                    $("#btnPDF").hide();
                    $("#btnExcel").hide();
                }
                else{
                    dt.attr('data-empty','false');
                    $("#btnPDF").show();
                    $("#btnExcel").show();

                }
                // Se habilita el rowgroup dependiendo la tabla en la que esta el usuario
                if(!$("#upp_filter").val()){
                    if(ruta == "#buscarFormD") estatus = true;
                }
               dt.DataTable({
                    data: response.dataSet,
                    searching: true,
                    autoWidth: true,
                    // processing: false,
                    // serverSide: true,
                    // ajax: {
                            
                    //     url:   $(ruta).attr("action"),
                    //     "data": {
                    //         "filtros":  $(ruta).serializeArray()
                    //     },
                    //     "type": "POST",
                    //     },
                    order:[],
                    group: [],
                    rowGroup: estatus,
                    ordering: true,
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
                            className: 'text-right txtR'
                        },
                        {
                            targets: formatLeft,
                            className: 'text-left txtL'
                        },
                        {
                            targets: formatCenter,
                            className: 'text-center'
                        }
                    ],
                    // Poner el scroll debajo del footer 
                    fnInitComplete: function(){
                        //Comprobar si hay footer
                        if($(tabla+' tfoot.colorMorado').length != 0){
                            // Deshabilitar la barra de scroll del body
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
                        }
                    },
                    // obtener la suma total
                    footerCallback: function (row, data, start, end, display) {
                        var api = this.api();
                        
                        // Cambiar el formato string a entero
                        var intVal = function (i) {
                            return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                        };
            
                            if(ruta == "#buscarFormD"){ //Calendario general
                                totalMontoD = api
                                .column(2)
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);
                                
                                totalEnero = api
                                .column(3)
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);
                                totalFebrero = api
                                .column(4)
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);
                                totalMarzo = api
                                .column(5)
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);
                                totalAbril = api
                                .column(6)
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);
                                totalMayo = api
                                .column(7)
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);
                                totalJunio = api
                                .column(8)
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);
                                totalJulio = api
                                .column(9)
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);
                                totalAgosto = api
                                .column(10)
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);
                                totalSeptiembre = api
                                .column(11)
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);
                                totalOctubre = api
                                .column(12)
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);
                                totalNoviembre = api
                                .column(13)
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);
                                totalDiciembre = api
                                .column(14)
                                .data()
                                .reduce(function (a, b) {
                                    return intVal(a) + intVal(b);
                                }, 0);

                                $(api.column(2).footer()).html( (totalMontoD/2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") );
                                $(api.column(3).footer()).html( (totalEnero/2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") );
                                $(api.column(4).footer()).html( (totalFebrero/2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") );
                                $(api.column(5).footer()).html( (totalMarzo/2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") );
                                $(api.column(6).footer()).html( (totalAbril/2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") );
                                $(api.column(7).footer()).html( (totalMayo/2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") );
                                $(api.column(8).footer()).html( (totalJunio/2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") );
                                $(api.column(9).footer()).html( (totalJulio/2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") );
                                $(api.column(10).footer()).html( (totalAgosto/2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") );
                                $(api.column(11).footer()).html( (totalSeptiembre/2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") );
                                $(api.column(12).footer()).html( (totalOctubre/2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") );
                                $(api.column(13).footer()).html( (totalNoviembre/2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") );
                                $(api.column(14).footer()).html( (totalDiciembre/2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") );
                            }else if(ruta == "#buscarFormC"){ //Avance general
                                var totalMontoA = 0;
                                var totalCalendarizado = 0;
                                var totalDisponible = 0;

                                for (let i = 0; i < display.length; i++) {
                                    if(data[i][0] != " " && data[i][0] != ""){
                                        totalMontoA += intVal(data[i][3]);
                                        totalCalendarizado += intVal(data[i][4]);
                                        totalDisponible += intVal(data[i][5]);
                                    }
                                }

                                $(api.column(3).footer()).html( totalMontoA.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") );
                                $(api.column(4).footer()).html( totalCalendarizado.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") );
                                $(api.column(5).footer()).html( totalDisponible.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") );
                                
                            }else if(ruta == "#buscarFormB"){ //Capitulo y partida
                                var total = 0; 

                                for (let i = 0; i < display.length; i++) {
                                    if(data[i][0] != " " && data[i][0] != ""){
                                        total += intVal(data[i][2]);
                                    }
                                }
                                    
                                // Actualizar footer
                                $(api.column(2).footer()).html( total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") );
                            }else{
                                // Suma total de todas las páginas
                                total = api
                                    .column(".sum")
                                    .data()
                                    .reduce(function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0);
                                    
                                // Actualizar footer
                                $(api.column(".sum").footer()).html( total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") );
                            }
                    },
                    rowCallback: function( row, data, index ) {
                        if($("#upp_filter").val() != ""){
                            if(ruta == "#buscarFormD"){
                                if(index == 0) $(row).hide();
                            }
                        }
                    },
               });
               redrawTable(tabla);   
               if(ruta == "#buscarFormD"){ // Eliminar primera columna que contiene las UPP
                    dt.DataTable().column(0).visible(false);
                }
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
<style>
    .custom-select{
        min-width: 4em;
    }
</style>
@endsection