@section('page_scripts')
    <script type="text/javascript">
        function getData(tabla, rt) {


            var dt = $(tabla);
            var orderDt = "";
            var column = "";
            var ruta;
            var formatRight = [];
            var formatLeft = [];
            var formatCenter = [];
            var bold = [];

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

            if (dt.attr('data-bold') != undefined) {
                bold = dt.attr('data-bold').split(",");
                for (var i in bold) {
                    if (bold[i] != "") {
                        bold[i] = parseInt(bold[i]);
                    }
                }
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
            var estatus = $("#estatus_filter").val();
            var upp = $("#upp_filter").val();

            formData.append("_token", csrf_tpken);
            formData.append("estatus", estatus);
            formData.append("upp", upp);

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
                            }
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
