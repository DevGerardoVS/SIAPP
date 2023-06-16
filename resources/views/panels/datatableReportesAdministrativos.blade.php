@section('page_scripts')
<script type="text/javascript">

    function getData(){
        var dt = $('#genericDataTable');
        dt.DataTable().clear().destroy();
        generaDatatable();
    }

    function generaDatatable(){
        var dt = $('#genericDataTable');
        var orderDt = "";
        var column = "";
        var formatRight = [];
        var formatLeft = [];
        var formatCenter = [];
        var bold = [];

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
           
        $.ajax({
            url: $("#buscarForm").attr("action"),
            data: $("#buscarForm").serializeArray(),
            type:'POST',
            dataType: 'json',
            success: function(response) {
                // console.log($("#buscarForm").serializeArray());
                if(response.dataSet.length == 0){
                    dt.attr('data-empty','true');
                }
                else{
                    dt.attr('data-empty','false');
                }
                if(response.registro_anual == "4"){
                    $('#btn_registro_anual').attr('style','color:#0d6efd; display: block;');
                    $('#alert_message').attr('style','display: none;');
                }
                if(response.registro_anual == "5"){
                    $('#alert_message').attr('style','display: none;');
                }
                else{
                    $('#alert_message').attr('style','display: block;');
                }
                var numberRenderer = $.fn.dataTable.render.number( ',', '.', 2,   ).display;
                dt.DataTable({
                    data: response.dataSet,
                    searching: true,
                    autoWidth: true,
                    ordering: true,
                    // processing: true,
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
                        }
                    },
                    dom: 'frltip',
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

                        // Enable TFOOT scoll bars
                        $('.dataTables_scrollFoot').css('overflow', 'auto');

                        // Sync TFOOT scrolling with TBODY
                        $('.dataTables_scrollFoot').on('scroll', function () {
                            $('.dataTables_scrollBody').scrollLeft($(this).scrollLeft());
                        });     
                    },
                    // obtener la suma total
                    footerCallback: function (row, data, start, end, display) {
                        var api = this.api();
            
                        // Remove the formatting to get integer data for summation
                        var intVal = function (i) {
                            return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
                        };
            
                        // Total over all pages
                        total = api
                            .column(".sum")
                            .data()
                            .reduce(function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
            
                        // Total over this page
                        pageTotal = api
                            .column(".sum", { page: 'current' })
                            .data()
                            .reduce(function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0);
            
                        // Update footer
                        $(api.column(".sum").footer()).html( total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") );
                    },
                    // obtener la suma de cada vista
                //     footerCallback: function(row, data, start, end, display){
                //        var api = this.api();
                //        api.columns(14,{
                //            page: 'current'
                //        }).every(function(){
                //            var sum = this.data().reduce(function(a,b){
                //                var x = parseFloat(a) || 0;
                //                if(b == null){
                //                    b = "0";
                //                }
                //                var y = parseFloat(b.toString().replaceAll(",","")) || 0;
                //                return x + y;
                //            },0);
                //            $(".total").html(sum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                //        });
                //    }
                });
            },
            error: function(response) {
                console.log('Error: ' + response);
            },
        });
    }
    
    $(document).ready(function() {
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (event) {
            $.fn.dataTable.Api('.dataTable')
                .columns.adjust()
                .responsive.recalc();
        });
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(".anio").val($('#anio_filter option:selected').val());
        $(".upp").val($('#upp_filter option:selected').val());
        // console.log($(".upp").val($('#upp_filter option:selected').val()));
        getDataFechaCorte($('#anio_filter option:selected').val());  //Llenar el select de fecha de acuerdo al valor del año por defecto
        
        $('#buscarForm').submit( (e) => {
            e.preventDefault();
            $(this).find('.filters_anio').change();
            getDataFechaCorte($('#anio_filter').val()); //Llenar el select de fecha de acuerdo al valor del año
        } );

        $('#buscarForm').submit( (e) => {
            e.preventDefault();
            $(this).find('.filters_fechaCorte').change();
        } );

        $('#buscarForm').submit( (e) => {
            e.preventDefault();
            $(this).find('.filters_upp').change();
        });

        $("#buscarForm").on("change",".filters_anio",function(e){
            e.preventDefault();
            getData();
            $(".anio").val($('#anio_filter').val());
            getDataFechaCorte($('#anio_filter').val());
        });

        $("#buscarForm").on("change",".filters_fechaCorte",function(e){
            e.preventDefault();
            getData();
            $(".fechaCorte").val($('#fechaCorte_filter').val());
            $("#fechaCorte").val($('#fechaCorte_filter').val());
        });

        $("#buscarForm").on("change",".filters_upp",function(e){
            e.preventDefault();
            getData();
            $(".upp").val($('#upp_filter').val());
            $("#upp").val($('#upp_filter').val());
        });
    });
</script>
<style>
    .custom-select{
        min-width: 4em;
    }
</style>
@endsection