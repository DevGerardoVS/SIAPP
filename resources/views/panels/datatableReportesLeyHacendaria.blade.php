@section('page_scripts')
    <script type="text/javascript">

        $(document).ready(function() {
            $(".alert").delay(10000).slideUp(200, function() {
                $(this).alert('close');
            });
            function getDataFechaCorte(anio) { //función para actualizar el select fechas de corte
                $.ajax({
                    url: "/Reportes/data-fecha-corte/" + anio,
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        var par = $('#fechaCorte_filter');
                        par.html('');
                        par.append(new Option("Actuales", ""));
                        $.each(data, function(i, val) {
                            var date = new Date(val.deleted_at);
                            var getCorrectDate = new Date(date.valueOf() + date.getTimezoneOffset() * 60000);
                            var formattedDate = ("0" + getCorrectDate.getDate()).slice(-2) + "-" + ("0" + (date.getMonth() + 1)).slice(-2) + "-" + date.getFullYear();
                            par.append(new Option("V"+ data[i].version +" - "+formattedDate , data[i].deleted_at));
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

            $('#buscarForm').submit((e) => {
                e.preventDefault();
                $(this).find('.filters_anio').change();
                getDataFechaCorte($('#anio_filter').val());
            });

            $('#buscarForm').submit((e) => {
                e.preventDefault();
                $(this).find('.filters_fechaCorte').change();
            });

            $("#buscarForm").on("change", ".filters_anio", function(e) {
                e.preventDefault();
                $("#fechaCorte_filter").val("");
                $(".anio").val($('#anio_filter').val());
                getDataFechaCorte($('#anio_filter').val());
            });

            $("#buscarForm").on("change", ".filters_fechaCorte", function(e) {
                e.preventDefault();
                $(".fechaCorte").val($('#fechaCorte_filter').val());
            });

        });
    </script>
@endsection
