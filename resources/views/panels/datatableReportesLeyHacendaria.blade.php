@section('page_scripts')
<script type="text/javascript">

    function generaDatatable(){
        var dt = $('#tbl-reportes');

        $.ajax({
            url: $("#buscarForm").attr("action"),
            data: $("#buscarForm").serializeArray(),
            type:'POST',
            dataType: 'json',
            success: function(response) {
                console.log($("#buscarForm").serializeArray());
                if(response.dataSet.length == 0){
                    dt.attr('data-empty','true');
                }
                else{
                    dt.attr('data-empty','false');
                }
                dt.DataTable({
                    data: response.dataSet,
                    searching: true,
                    autoWidth: true,
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
                        }
                    },
                    dom: 'frltip',
                });
            },
            error: function(response) {
                console.log('Error: ' + response);
            },
        });
    }
    
    $(document).ready(function() {
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
        getDataFechaCorte($('#anio_filter option:selected').val());
        
        $('#buscarForm').submit( (e) => {
            e.preventDefault();
            $(this).find('.filters_anio').change();
            getDataFechaCorte($('#anio_filter').val()); 
        } );

        $('#buscarForm').submit( (e) => {
            e.preventDefault();
            $(this).find('.filters_fechaCorte').change();
        } );

        $("#buscarForm").on("change",".filters_anio",function(e){
            e.preventDefault();
            $(".anio").val($('#anio_filter').val());
            getDataFechaCorte($('#anio_filter').val());
        });

        $("#buscarForm").on("change",".filters_fechaCorte",function(e){
            e.preventDefault();
            $(".fechaCorte").val($('#fechaCorte_filter').val());
        });

    });
</script>
@endsection