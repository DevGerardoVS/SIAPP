@section('page_scripts')
<script type="text/javascript">

    function getData(){
        var dt = $('#catalogo');
        dt.DataTable().clear().destroy();
        generaDatatable();
    }

    function generaDatatable(){
        var dt = $('#catalogo');
        var orderDt = "";
        var column = "";
        var formatCantidades = [];
        var ordenamiento = [];
        var columns_hidden = [];

        if(dt.attr('data-id')!=undefined){
            var data_order = dt.attr('data-id').split(",");
            for(var i in data_order){
                var dato = data_order[i].split("_");
                orderDt = dato[0];
                column = dato[1];
                ordenamiento[i] = [parseInt(column),""+orderDt];
            }
        }

        if(dt.attr('data-hidden')!=undefined){
            var data_hidden = dt.attr('data-hidden').split(",");
            for(var i in data_hidden){
                columns_hidden[i] = parseInt(data_hidden[i]);
            }
        }

        if(dt.attr('data-format')!=undefined){
            formatCantidades = dt.attr('data-format').split(",");
            for(var i in formatCantidades){
                if(formatCantidades[i] != ""){
                    formatCantidades[i] = parseInt(formatCantidades[i]);
                }
            }
        }
        $.ajax({
            url:  $("#buscarForm").attr("action"),
            data: $("#buscarForm").serializeArray(),
            type:$("#buscarForm").attr("method"),
            dataType: 'json',
            success: function(response) {
                console.log("res-DataTable",response)
                if(response?.dataSet.length == 0){
                    dt.attr('data-empty','true');
                }
                else{
                    dt.attr('data-empty','false');
                }
                

                dt.DataTable({
                    data: response?.dataSet,
                    pageLength:10,
                    scrollX: true,
                    autoWidth: false,
                    processing: true,
                    order: ordenamiento,
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
                    columnDefs: [
                        {
                            targets: formatCantidades,
                            className: 'text-right'
                        },
                        {
                            targets: columns_hidden,
                            visible: false,
                            searcheable: false
                        }
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
                redrawTable('#catalogo');
            },
            error: function(response) {
                console.log('Error: ' + response);
            }
        });
    }

    function redrawTable(tabla){
        dt = $(tabla);
        dt.DataTable().columns.adjust().draw();
        dt.children("thead").css("visibility","hidden");
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        /*$('#buscarForm').submit( (e) => {
            e.preventDefault();
            $(this).find('.filters').change();
        } );*/

        $("#buscarForm").on("change",".filters_anio",function(e){
            e.preventDefault();
            getData();
        });

        $('#date_range').on('apply.daterangepicker', (e, picker) => {
            e.preventDefault();
            getData();
        });

        $("#buscarForm").on("change",".filters",function(e){
            e.preventDefault();
            getData();
        });

        $( window ).resize(function() {
            redrawTable("#catalogo");
        });
    });
</script>
@endsection
