@section('page_scripts')
    <script type="text/javascript">
        $(".alert").delay(10000).slideUp(200, function() {
            $(this).alert('close');
        });

        $(document).ready(function() {
            function getDataFechaCorte(anio) { //función para actualizar el select fechas de corte
                $.ajax({
                    url: "/Reportes/data-fecha-corte/" + anio,
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        var par = $('#fechaCorte_filter');
                        par.html('');
                        par.append(new Option("Todo", ""));
                        $.each(data, function(i, val) {
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
