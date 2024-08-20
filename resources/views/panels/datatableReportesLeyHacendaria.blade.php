@section('page_scripts')
    <script type="text/javascript">

        $(document).ready(function() {
            $(".alert").delay(10000).slideUp(200, function() {
                $(this).alert('close');
            });
            function getDataFechaCorte(anio) { //función para actualizar el select fechas de corte
                $.ajax({
                    url: "/Reportes/data-fecha-corte/"+ anio,
                    type:'POST',
                    dataType: 'json',
                    success: function(data) {
                        var par = $('#fechaCorte_filter');
                        par.html('');
                        var getLastYear = {!! json_encode($anios[0]->ejercicio) !!}; // Variable para obtener el último año de la tabla pp y pph
                        if(getLastYear == anio) par.append(new Option("Actuales", "")); // Comprobar si el último año es igual al año del select y eliminar la opción de actuales para los años anteriores
                        $.each(data, function(i, val){
                            var deleted_at = val.deleted_at.slice(0,-3);
                            var date = new Date(deleted_at);
                            var getCorrectDate = new Date(date.valueOf() + date.getTimezoneOffset() * 60000);
                            var formattedDate = ("0" + getCorrectDate.getDate()).slice(-2) + "-" + ("0" + (date.getMonth() + 1)).slice(-2) + "-" + date.getFullYear();

                            if(data[i].version == 0) par.append(new Option("Última versión", data[i].deleted_at));
                            else par.append(new Option("V"+ data[i].version +" - "+formattedDate , data[i].deleted_at));
                        });
                    }
                });
            }
            function getNames(anio){
                $.ajax({
                    url: "/Reportes/names/"+anio,
                    type:'GET',
                    success: function(data) {
                        $("#putNames").html('');
                        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        data.forEach(function (row,index) {
                            
                            let replaceUnderscore = row.name.replace(/_/g, ' ');
                            let replaceReport = replaceUnderscore.replace(/reporte/g, '');
                            let replaceNum = replaceReport.replace(/num/g, 'numeral');
                            if (replaceNum.includes('num')) {
                                replaceNum = replaceNum.slice(0, 15) + 'inc ' + replaceNum.slice(15);
                            }
                            let replaceDot = replaceNum.replace(/11 /g, '11.');
                            let correctName = replaceDot;
                            let newName = "";
                            if(anio > 2023){
                                if(correctName == " art 20 frac X inc a numeral 3") newName = " art 20 frac X inc a numeral 5";
                                if(correctName == " art 20 frac X inc a numeral 1") newName = " art 20 frac X inc a numeral 3";
                                if(correctName == " art 20 frac X inc a numeral 2") newName = " art 20 frac X inc a numeral 4"; //pendiente de revisión
                                if(correctName == " art 20 frac X inc b numeral 3") newName = " art 20 frac X inc b numeral 4";
                                if(correctName == " art 20 frac X inc a numeral 6") newName = " art 20 frac X inc a numeral 2";
                                if(correctName == " art 20 frac X inc b numeral 4") newName = " art 20 frac X inc b numeral 2";
                                if(correctName == " art 20 frac X inc b numeral 1") newName = " art 20 frac X inc b numeral 3";
                                if(correctName == " art 20 frac X inc b numeral 10") newName = " art 20 frac X inc c numeral 3";
                                if(correctName == " art 20 frac X inc b numeral 11.1") newName = " art 20 frac X inc c numeral 4.1";
                                if(correctName == " art 20 frac X inc b numeral 11.2") newName = " art 20 frac X inc c numeral 4.2";
                                if(correctName == " art 20 frac X inc b numeral 11.3") newName = " art 20 frac X inc c numeral 4.3";
                                if(correctName == " art 20 frac X inc b numeral 11.4") newName = " art 20 frac X inc c numeral 4.4";
                                if(correctName == " art 20 frac X inc b numeral 11.6") newName = " art 20 frac X inc c numeral 4.6";
                                if(correctName == " art 20 frac X inc b numeral 11.7") newName = " art 20 frac X inc c numeral 4.7";
                                if(correctName == " art 20 frac X inc b numeral 11.8") newName = " art 20 frac X inc c numeral 4.8";
                                if(correctName == " art 20 frac II") newName = " art 20 frac II";
                                if(correctName == " art 20 frac III") newName = " art 20 frac III";
                                if(correctName == " art 20 frac IX") newName = " art 20 frac IX";
                            }
                            var newRow = `
                                <tr>
                                    <td class="d-flex justify-content-between px-5">
                                        <div class="my-auto me-2" style="font-size: 14px;">${anio >2023 ? newName.toUpperCase(): correctName.toUpperCase()}</div>
                                        <div class="d-flex justify-content-end flex-wrap p-1">
                                            <form action="/Reportes/download/${row.name}" method="POST" enctype="multipart/form-data" class="my-auto">
                                                <input type="hidden" name="_token" value=${csrfToken}>
                                                <input type="text" hidden class="anio" id="anio" name="anio">
                                                <input type="text" hidden class="anio" id="anio" name="anio">
                                                <input type="text" hidden class="fechaCorte" id="fechaCorte" name="fechaCorte">
                                                <button id="btnPDF" type="submit" formtarget="_blank" class="btn btn-light btn-sm btn-labeled me-sm-3 align-middle" style="border-color: #6a0f49;" title="Generar Reporte PDF" name="action" value="pdf">
                                                    <span class="btn-label"><i class="fa fa-file-pdf-o text-danger fs-4 align-middle"></i></span>
                                                    <span class="d-sm-none d-lg-inline align-middle" style="color:#6a0f49; font-size: 1rem">Exportar a PDF</span> 
                                                </button>
                                                <button id="btnExcel" type="submit" formtarget="_blank" class="btn btn-light btn-sm btn-labeled" style="border-color: #6a0f49;" title="Generar Reporte Excel" name="action" value="xlsx">
                                                    <span class="btn-label"><i class="fa fa-file-excel-o text-success fs-4 align-middle"></i></span>
                                                    <span class="d-sm-none d-lg-inline align-middle" style="color:#6a0f49; font-size: 1rem">Exportar a Excel</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            `;
                            $("#putNames").append(newRow);
                        });
                    },
                    error: function(response) {
                    console.log("error ajax"+ response);
                    },
                    statusCode: {
                        404: function(response) {
                            console.log('ajax.statusCode: 404');
                        },
                        500: function(response) {
                            console.log('statusCode: 500');
                            var response = response.responseJSON;
                            Swal.close();
                            Swal.fire({
                                icon: response.status,
                                title: response.title,
                                html: response.message,
                                confirmButtonText: "Aceptar",
                            });
                        }
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
            getNames($('#anio_filter option:selected').val());

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
                setTimeout(() => {
                    $(".fechaCorte").val($('#fechaCorte_filter').val());
                }, 500);
                getDataFechaCorte($('#anio_filter').val());
                getNames($('#anio_filter').val());
            });

            $("#buscarForm").on("change", ".filters_fechaCorte", function(e) {
                e.preventDefault();
                $(".fechaCorte").val($('#fechaCorte_filter').val());
            });

        });
    </script>
@endsection
