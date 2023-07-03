@section('page_scripts')
    <script type="text/javascript">
        function getData() {
            var dt = $('#catalogo');

            dt.DataTable().clear().destroy();
            generaDatatable();
        }

        function generaDatatable() {
            var dt = $('#catalogo');
            var orderDt = "";
            var column = "";
            var formatCantidades = [];
            var negritas = [];
            formatCantidades = [];
            var titulo = 'Reporte ' + $('.titulo').text() + ' ' + $("#municipio_filter").find(':selected').text() + '-' + $(
                "#anio_filter").val();

            if (dt.attr('data-bold') != undefined) {
                negritas = dt.attr('data-bold').split(",");
                for (var i in negritas) {
                    if (negritas[i] != "") {
                        negritas[i] = parseInt(negritas[i]);
                    }
                }
            }

            if (dt.attr('data-format') != undefined) {
                formatCantidades = dt.attr('data-format').split(",");
                for (var i in formatCantidades) {
                    if (formatCantidades[i] != "") {
                        formatCantidades[i] = parseInt(formatCantidades[i]);
                    }
                }
            }


            $.ajax({
                url: $("#buscarForm").attr("action"),
                data: $("#buscarForm").serializeArray(),
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    //console.log(response)
                    if (response.dataSet.length == 0) {
                        dt.attr('data-empty', 'true');
                    } else {
                        dt.attr('data-empty', 'false');
                    }
                    if (response.registro_anual == "4") {
                        $('#btn_registro_anual').attr('style', 'color:#0d6efd; display: block;');
                        $('#alert_message').attr('style', 'display: none;');
                    }
                    if (response.registro_anual == "5") {
                        $('#alert_message').attr('style', 'display: none;');
                    } else {
                        $('#alert_message').attr('style', 'display: block;');
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

                        },
                        dom: 'Bfrtip',
                        buttons: [
                            'excel',
                             'pdf'
                        ],
                        columnDefs: [{
                                defaultContent: '0',
                                targets: formatCantidades,
                                className: 'text-right'
                            },
                            {
                                targets: [5],
                                className: 'font-weight-bold'
                            },
                            {
                                targets: [0],
                                className: 'text-left'
                            },
                            {

                                className: 'text-right'
                            },


                        ],
                        fnRowCallback: function(nRow, aData, iDisplayIndex) {
                            if (negritas.includes(iDisplayIndex)) {
                                $('td', nRow).each(function() {
                                    $(this).attr('style', 'font-weight: bold');
                                });
                            }
                            return nRow;
                        },
                        footerCallback: function(row, data, start, end, display) {
                            var api = this.api();
                            api.columns('.sum', {
                                page: 'current'
                            }).every(function() {
                                var sum = this.data().reduce(function(a, b) {
                                    var x = a || 0;
                                    if (b == null) {
                                        b = 0;
                                    }
                                    var y = b.replaceAll(",", "") || 0;
                                    return x + y;
                                }, 0);
                                sum = sum.toFixed(0);
                                // $(this.footer()).html($(this.footer()).attr('data-title')+": "+sum.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                            });
                        }
                    });
                },
                error: function(response) {
                    console.log('Error: ' + response);
                }
            });
        }

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#date_range').on('apply.daterangepicker', (e, picker) => {
                e.preventDefault();
                getData();
            });

            $("#buscarForm").on("change", ".filters", function(e) {
                e.preventDefault();
                getData();
            });



        });
    </script>
@endsection
