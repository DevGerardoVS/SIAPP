@section('page_scripts')
    <script type="text/javascript">

        function getData(){
            var dt = $('#catalogo');
            dt.DataTable().clear().destroy();
            generaDatatable();
        }

        function generaDatatable() {


            var dt =  $('#catalogo');
            var orderDt = "";
            var column = "";
        
            var formData = new FormData();
            var csrf_tpken = $("input[name='_token']").val();
            var anio = $("#anio_filter").val();

            formData.append("_token", csrf_tpken);
            formData.append("anio", anio);

            $.ajax({
                url: $("#buscarForm").attr("action"),
                data: formData,
                type: 'POST',
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
                    if (response.dataSet.length == 0) {
                        dt.attr('data-empty', 'true');
                    } else {
                        dt.attr('data-empty', 'false');
                    }

                    dt.DataTable({
                        data: response.dataSet,
                        searching: true,
                        autoWidth: true,
                        order: [],
                        ordering: true,
                        fixedColumns: true,
                        scrollCollapse: true,
                        pageLength: 10,
                        dom: 'frltip',
                        scrollX: true,
                        "lengthMenu": [10, 25, 50, 80],
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
                            targets:[1,3],
                            className: "text-left"
                        },
                        ],
                        columns: [
                            { width: "5em"},
                            { width: "20em"},
                            { width: "5em"},
                            { width: "20em"},
                            { width: "10em"},
                        ],
                    });
                    redrawTable(dt);
                },
                error: function(response) {
                    console.log('{{ __('messages.error') }}: ' + response);
                }
            });
        }

        function redrawTable(dt) {
            dt = $(dt);
            dt.DataTable().columns.adjust().draw();
            dt.children("thead").css("visibility", "hidden");
        }

        
    </script>
    <style>
        .custom-select {
            min-width: 4em;
        }
    </style>
@endsection