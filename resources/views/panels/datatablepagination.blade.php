@section('page_scripts')
<script type="text/javascript">

    function getData(){
        var dt = $('#catalogo');
        dt.DataTable().destroy();
        generateDataTable ();
    }

    // function generaDatatable(){
    //     var dt = $('#catalogo');
    //     // var orderDt = "";
    //     // var column = "";
    //     // var formatCantidades = [];
    //     // var ordenamiento = [];

    //     // if(dt.attr('data-format')!=undefined){
    //     //     formatCantidades = dt.attr('data-format').split(",");
    //     //     for(var i in formatCantidades){
    //     //         if(formatCantidades[i] != ""){
    //     //             formatCantidades[i] = parseInt(formatCantidades[i]);
    //     //         }
    //     //     }
    //     // }

    //     $.ajax({
    //         url: $("#buscarForm").attr("action"),
    //         data: $("#buscarForm").serializeArray(),
    //         type:'POST',
    //         dataType: 'json',
    //         success: function(response) {
    //         console.log("datos del response");
    //             console.log(response.aaData);
    //             if(response.aaData.length == 0){
    //                 dt.attr('data-empty','true');
    //             }
    //             else{
    //                 dt.attr('data-empty','false');
    //             }

    //             dt.DataTable({
    //                 data: response.aaData,
                    
    //                 scrollX: true,
    //                 searching: true,
    //                 autoWidth: true,
    //                 processing: false,
    //                 serverSide: true,
    //                 snapshot: null,
    //                 select: true,
    //                 ServerSide: true,
    //                 language: {
    //                     processing: "Procesando...",
    //                     lengthMenu: "Mostrar   _MENU_   registros",
    //                     zeroRecords: "No se encontraron resultados",
    //                     emptyTable: "Ningún dato disponible en esta tabla",
    //                     info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
    //                     infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
    //                     infoFiltered: "(filtrado de un total de _MAX_ registros)",
    //                     search: "Búsqueda:",
    //                     infoThousands: ",",
    //                     loadingRecords: "Cargando...",
    //                     buttonText: "Imprimir",
    //                     paginate: {
    //                         first: "Primero",
    //                         last: "Último",
    //                         next: "Siguiente",
    //                         previous: "Anterior",
    //                     },
    //                     buttons: {
    //                         copyTitle: 'Copiado al portapapeles',
    //                         copySuccess: {
    //                             0: '%d registros copiados',
    //                             1: 'Se copio un registro'
    //                         }
    //                     },
    //                 },
    //                 // columnDefs: [
    //                 //     {
    //                 //         targets: formatCantidades,
    //                 //         className: 'text-left'
    //                 //     }
    //                 // ],
    //                 footerCallback: function(row, data, start, end, display){
    //                     var api = this.api();
    //                     // api.columns('.sum',{
    //                     //     page: 'current'
    //                     // }).every(function(){
    //                     //     var sum = this.data().reduce(function(a,b){
    //                     //         var x = parseFloat(a) || 0;
    //                     //         if(b == null){
    //                     //             b = "0.00";
    //                     //         }
    //                     //         var y = parseFloat(b.replaceAll(",","")) || 0;
    //                     //         return x + y;
    //                     //     },0);
    //                     //     sum = sum.toFixed(2);
    //                     //     $(this.footer()).html($(this.footer()).attr('data-title')+": "+sum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
    //                     // });
    //                 },
    //                 "fnDrawCallback": function(oSettings) {
    //                 $('tbody').removeAttr("hidden");
    //             }
    //             });
    //         },
    //         error: function(response) {
    //             console.log('Error: ' + response);
    //         }
    //     });
    // }

    function generateDataTable(){
        table = $("#catalogo").DataTable({
            scrollX: true,
            autoWidth: false,
            processing: false,
            serverSide: true,
            snapshot: null,
            aaSorting: [],
            order: [] ,
            ordering: false,
            ajax: {
                url: $("#buscarForm").attr("action"),
                data: {
                    "filtros":  $("#buscarForm").serializeArray()
                },
                type: "POST",
                beforeSend: function() {
                    let timerInterval
                    Swal.fire({
                        title: '{{__("messages.msg_cargando_datos")}}',
                        html: ' <b></b>',
                        allowOutsideClick: false,
                        timer: 2000000,
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                },
                complete: function(){
                    Swal.close();
                },
            },
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
                    "targets": [],
                    "width":"3%",
                },
            ],
            initComplete:function(){
                Swal.close();
            },
            fnDrawCallback: function(oSettings) {
                $('tbody').removeAttr("hidden");
            }
        });
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#buscarForm').submit( (e) => {
            e.preventDefault();
            $(this).find('.filters').change();
        } );

        $('#date_range').on('apply.daterangepicker', (e, picker) => {
            e.preventDefault();
            getData();
        });

        $("#buscarForm").on("change",".filters",function(e){
            e.preventDefault();
            getData();
        });
    });
</script>
@endsection
