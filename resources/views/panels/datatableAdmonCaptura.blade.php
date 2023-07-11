@section('page_scripts')
    <script type="text/javascript">
        function getData(tabla, rt) {


            var dt = $(tabla);
            var orderDt = "";
            var column = "";
            var ruta;

            switch (rt) {
                case "A":
                    ruta = "#buscarFormA";
                    break;
                case "B":
                    ruta = "#buscarFormB";
                    break;
                default:
                    break;
            }
            var formData = new FormData();
            var csrf_tpken = $("input[name='_token']").val();
            var estatus = $("#estatus_filter").val();

            formData.append("_token", csrf_tpken);
            formData.append("estatus", estatus);

            $.ajax({
                url: $(ruta).attr("action"),
                data: formData,
                type: 'POST',
                dataType: 'json',
                contentType: false,
                processData: false,
                beforeSend: function() {
                    let timerInterval
                    Swal.fire({
                        title: 'Cargando datos, por favor espere...',
                        html: ' <b></b>',
                        allowOutsideClick: false,
                        timer: 2000000,
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                },
                complete: function() {
                    Swal.close();
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
                            targets:[1],
                            className: "text-left"
                        },
                        ],
                        columns: [
                            { width: "5em"},
                            { width: "80em"},
                            { width: "10em"},
                            { width: "10em"},
                            { width: "20em"},
                        ],
                    });
                    redrawTable(tabla);
                },
                error: function(response) {
                    console.log('{{ __('messages.error') }}: ' + response);
                }
            });
        }

        function redrawTable(tabla) {
            dt = $(tabla);
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
