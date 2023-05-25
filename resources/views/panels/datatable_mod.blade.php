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
        var bold = [];
        var titulo = $('.titulo').text()+' '+$(".filters_municipio").find(':selected').text()+'-'+$(".filters_anio").val();
        if($(".municipio_predial").text() != '')
            titulo = $('.titulo').text()+' '+$(".municipio_predial").text()+'-'+$(".filters_anio").val();
        /*if(dt.attr('data-id')!=undefined){
            var data_order = dt.attr('data-id').split(",");
            for(var i in data_order){
                var dato = data_order[i].split("_");
                orderDt = dato[0];
                column = dato[1];
                ordenamiento[i] = [parseInt(column),""+orderDt];
            }
        }*/
        if(dt.attr('data-bold')!=undefined){
            bold = dt.attr('data-bold').split(",");
            for(var i in bold){
                if(bold[i] != ""){
                    bold[i] = parseInt(bold[i]);
                }
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
            url: $("#buscarForm").attr("action"),
            data: $("#buscarForm").serializeArray(),
            type:'POST',
            dataType: 'json',
            success: function(response) {
                // dt.row.add( 'hola').draw();
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
                    pageLength:10,
                    scrollX: true,
                    autoWidth: true,
                    processing: true,
                    order: ordenamiento,
                    ServerSide: true,
                    paging: false,
                    searching: false,
                    ordering: false,
                    info: false,
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
                    dom: 'Bfrtip',
                    buttons: [
                        { text: '<i class="fas fa-file-excel"></i>', extend: 'excel',footer: true, className: 'btn-success me-2', titleAttr:'Descargar excel', title: titulo,
                        "excelStyles": [
                             {
                                cells: "A",
                                style: {
                                    font: {
                                        size: "14"
                                    },
                                    alignment: {
                                        horizontal: "left",
                                        vertical: "left"
                                    }
                                }
                            },
                            {
                                cells: "1",
                                style:{
                                    alignment: {
                                        horizontal: "center",
                                        vertical: "center"
                                    }
                                }
                            },
                            {
                                cells: "2",
                                height: 40,
                                style: {
                                    font: {
                                        size: "14",
                                        color: "FFFFFF"
                                    },
                                    fill: {
                                        pattern: {
                                            color: "6A0F49"
                                        }
                                    },
                                    alignment: {
                                        horizontal: "center",
                                        vertical: "center"
                                    }
                                }
                            },
                        ]},
                        { text: '<i class="fas fa-file-pdf"></i>', extend: 'pdf',footer: true, className: 'btn-danger align-middle', titleAttr:'Descargar PDF', title: titulo,
                        {{-- alinear a la derecha los elementos del body --}}
                        customize: function(doc) {
                            doc.defaultStyle.fontSize = 12;
                            var rowCount = doc.content[1].table.body.length;
                            for (i = 1; i < rowCount; i++) {
                                doc.content[1].table.body[i][5].alignment = 'right';
                                doc.content[1].table.body[i][4].alignment = 'right';
                                doc.content[1].table.body[i][3].alignment = 'right';
                                doc.content[1].table.body[i][2].alignment = 'right';
                                doc.content[1].table.body[i][1].alignment = 'right';
                            };
                        }  },
                    ],
                    columnDefs: [
                        {
                            defaultContent: '0.00',
                            targets: formatCantidades,
                            className: 'text-right'
                        },
                        {
                            targets: [0],
                            className: 'text-left'
                        },
                        {
                            targets: [0,1,2,3,4,5],
                            className: 'text-center'
                        },
                    ],
                    fnRowCallback: function(nRow, aData, iDisplayIndex) {
                        if (bold.includes(iDisplayIndex)) {
                            $('td', nRow).each(function(){
                                $(this).attr('style','font-weight: bold');
                            });
                        }
                        return nRow;
                    },
                    "footerCallback": function ( row, data, start, end, display ) {
                        var api = this.api(), data;
            
                        // convertir a entero
                        var intVal = function ( i ) {
                            return typeof i === 'string' ?
                                i.replace(/[\$,]/g, '')*1 :
                                typeof i === 'number' ?
                                    i : 0;
                        };
            
                        // calcular el total de cada columna
                        var trimestre1 = api
                            .column( 1 )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );
                            
                        var trimestre2 = api
                            .column( 2 )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );
                            
                        var trimestre3 = api
                            .column( 3 )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );

                        var trimestre4 = api
                            .column( 4 )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );
                            
                        var total = api
                            .column( 5 )
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );
                        
                        // mostrar los totales 
                        $( api.column( 0 ).footer() ).html('Total');
                        $( api.column( 1 ).footer() ).html(numberRenderer(trimestre1));
                        $( api.column( 2 ).footer() ).html(numberRenderer(trimestre2));
                        $( api.column( 3 ).footer() ).html(numberRenderer(trimestre3));
                        $( api.column( 4 ).footer() ).html(numberRenderer(trimestre4));
                        $( api.column( 5 ).footer() ).html(numberRenderer(total));
                    },
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

        $('#buscarForm').submit( (e) => {
            e.preventDefault();
            $(this).find('.filters_anio').change();
        } );

        $('#buscarForm').submit( (e) => {
            e.preventDefault();
            $(this).find('.filters_municipio').change();
        } );

        $("#buscarForm").on("change",".filters_anio",function(e){
            e.preventDefault();
            getData();
        });

        $("#buscarForm").on("change",".filters_municipio",function(e){
            e.preventDefault();
            getData();
        });
    });
</script>
@endsection