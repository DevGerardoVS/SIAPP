@section('page_scripts')
    <script type="text/javascript">
        function getData(tabla, rt) {

            var dt = $(tabla);
            var ruta;
            var formatRight = [];
            var formatLeft = [];
            var formatCenter = [];
            var estatus = false;
            var columns = [];

            switch (rt) {
                case "A":
                    ruta = "#buscarFormA";
                    columns = [
                            { width: "5em"},
                            { width: "20em"},
                            { width: "5em"},
                            { width: "20em"},
                            { width: "10em"},
                        ];
                    break;
                case "B":
                    ruta = "#buscarFormB";
                    columns = [
                            { width: "5em"},
                            { width: "5em"},
                            { width: "5em"},
                            { width: "10em"},
                            { width: "20em"},
                            { width: "10em"},
                            { width: "20em"},
                            { width: "20em"},
                        ];
                    break;
                default:
                    break;
            }

            if (dt.attr('data-right') != undefined) {
                formatRight = dt.attr('data-right').split(",");
                for (var i in formatRight) {
                    if (formatRight[i] != "") {
                        formatRight[i] = parseInt(formatRight[i]);
                    }
                }
            }

            if (dt.attr('data-left') != undefined) {
                formatLeft = dt.attr('data-left').split(",");
                for (var i in formatLeft) {
                    if (formatLeft[i] != "") {
                        formatLeft[i] = parseInt(formatLeft[i]);
                    }
                }
            }

            if (dt.attr('data-center') != undefined) {
                formatCenter = dt.attr('data-center').split(",");
                for (var i in formatCenter) {
                    if (formatCenter[i] != "") {
                        formatCenter[i] = parseInt(formatCenter[i]);
                    }
                }
            }

            var formData = new FormData();
            var csrf_tpken = $("input[name='_token']").val();
            var anio = $("#anio_filter").val();

            formData.append("_token", csrf_tpken);
            formData.append("anio", anio);

            dt.DataTable().clear().destroy();
            $.ajax({
                url: $(ruta).attr("action"),
                data: formData,
                type: 'POST',
                dataType: 'json',
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $('.custom-swal').css('display', 'block');
                },
                complete: function() {
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
                        columnDefs: [{
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
                            },
                        ],
                        columns: columns,
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

        @media(max-width: 575px) {
            div.dataTables_wrapper div.dataTables_paginate ul.pagination {
                justify-content: center;
                flex-wrap: wrap;
            }
        }
    </style>
@endsection
