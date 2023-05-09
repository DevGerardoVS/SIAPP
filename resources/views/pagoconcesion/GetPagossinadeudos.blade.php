@extends('layouts.app')
<?php
$stName = __('messages.nombre_sistema');
$acr = 'COCOTRA';

?>
@section('content')

    <div class="row justify-content-center" style="width: 100%">

        <nav class="navbar  colorgriss " style="width: 100%">
            <div class="row" style=" padding-left: 3%; width: 100%">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-5 col-xl-4">
                    <img src="{{ asset('img/LogosHeader&Footer/logosHeader.svg') }}" id="logo_cocotra" alt="logo">
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-7 col-xl-8"><p id="title_concesiones"><b>{{ __('messages.nombre_sistema') }}</b></p></div>
            </div>
        </nav>

        <div class="card" style="padding-left: 3%; padding-right: 3% ;">
            <div class="row" style="text-align: center; ">
                <div class="card-header col-sm border-end" style="border: 1px solid white; background-color: #ffe1e8">
                    <b>Paso
                        1
                    </b><br><b>Realizar la búsqueda </b>
                </div>
                <div class="card-header col-sm border-end" style="border: 1px solid white;background-color: #FFC3D0"><b>Paso
                        2
                    </b><br><b>Confirmar pago </b></div>
                <div class="card-header col-sm border-end" style="border: 1px solid white; background-color: #ffe1e8">
                    <b>Paso
                        3
                    </b><br><b>Realizar pago </b>
                </div>

            </div>


            <div class="card-body">
                <div class="row" style="text-align: center; ">
                    <div class="col-sm"><b>Concesión asociada al RFC:</b><b style="font-size: 20px"> {{ $RFC }}</b>
                        <br>
                        <b>Es necesario subir los datos de la aseguradora de la concesión a pagar</b>
                        <br>
                    </div>

                    <div class="row" style="text-align: center; ">
                        <div class="col-sm-9" style="text-align: left">
                            <div class="row" style="padding-bottom: 1%; padding-right: 2%;">
                                <HR width="40%" align="center">

                                <div class="col-sm-2" style="text-align: left; padding-left: 3%;">
                                    <b><i class="fa fa-address-book-o" aria-hidden="true"></i> Nombre contribuyente:</b>
                                </div>
                                <div class="col-sm-10" style="text-align: left; padding-left: 0%;">
                                    {{ $nombconses }}
                                </div>

                                <HR width="40%" align="center">
                            </div>
                        </div>
                        <div class="col-sm-3" style="text-align: center">
                            <div class="row" style="padding-bottom: 1%; padding-left: 4%;">
                                <HR width="40%" align="center">

                                <div class="col-sm-12" style="text-align: center;">
                                    <b>Agregar Concesión</b>
                                </div>

                                <HR width="40%" align="center">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3" style="text-align: center; ">
                        <div class="col-sm-2" style="text-align: left">
                        </div>
                        <div class="col-sm-2" style="text-align: left">
                        </div>
                    </div>



                    <div class="row mb-3" style="text-align: center; ">
                        <div class="col-sm-12" style="text-align: left">
                            <div class="row  " style="padding-bottom: 1%">
                                <div class="col-sm-3" style="padding-top: 4%; padding-left: 3%;">
                                    <div class="row  " style="padding-bottom: 1%">
                                        <div class="col-sm-6" style="text-align: left">
                                            <b><i class="fa fa-address-book-o" aria-hidden="true"></i>
                                                {{ __('messages.no_concesion') }}:</b>
                                        </div>
                                        <div class="col-sm-6" style="text-align: left">
                                            {{ $No_Consesion }}
                                        </div>
                                    </div>
                                    <div class="row  " style="padding-bottom: 1%">
                                        <div class="col-sm-6" style="text-align: left">
                                            <b><i class="fa fa-address-book-o" aria-hidden="true"></i> Número de placas:</b>
                                        </div>
                                        <div class="col-sm-6" style="text-align: left">
                                            {{ $No_placa }}
                                        </div>
                                    </div>
                                    <div class="row  " style="padding-bottom: 1%">
                                        <div class="col-sm-6" style="text-align: left">
                                            <b><i class="fa fa-address-book-o" aria-hidden="true"></i> Número de serie
                                                vehícular:</b>
                                        </div>
                                        <div class="col-sm-6" style="text-align: left">
                                            {{ $No_serie }}
                                        </div>
                                    </div>
                                </div>


                                <div class="col-sm-6" style="text-align: center">
                                    <h4><b><i class="fas fa-money-bill-1-wave"></i> Detalles de pago:</b></h4>
                                    <table id="customers">
                                        <tr>

                                            <div class="alert alert-warning" id="alert_message" role="alert">
                                                <h1> Sin adeudos </h1>

                                                <div class="title m-b-md">
                                                    <h6>Lee este código QR para descargar tu refrendo digital </h6>
                                                    {!!QrCode::size(170)->generate($urladeudo) !!}
                                                 </div>
                                                 <br>
                                                 <div class="title m-b-md">
                                                    <h6>
                                                        <a href={{$urladeudo}}>Descargar refrendo digital</a> 
                                                    </h6>
                                                   
                                                 </div>
                                            </div>
                                        </tr>

                                    </table>

                                </div>
                                <div class="col-sm-3" style="text-align: center;padding-left: 4%;padding-top: 2%;">



                                    <button type="button" class="btn btn-primary botonmodall" data-toggle="modal"
                                        data-target="#ModalPolizaImg">
                                        <i class="fa-solid fa-plus"></i> Agregar póliza
                                    </button>
                                    <div style="background-color: " class="bottonok " hidden>

                                        <i class="fas fa-check-double" style="background-color: rgb(37, 197, 43)"> PÓLIZA
                                            AGREGADA </i>
                                        <br>


                                        <div class="row  " style="padding-bottom: 1%">
                                            <div class="col-sm-6" style="text-align: left;">
                                                <b>Número de póliza:</b>
                                            </div>
                                            <div class="col-sm-6" style="text-align: left;">
                                                <p id="PPOLICY_NO"> </p>
                                            </div>
                                        </div>
                                        <div class="row  " style="padding-bottom: 1%">
                                            <div class="col-sm-6" style="text-align: left;">
                                                <b>Aseguradora:</b>
                                            </div>
                                            <div class="col-sm-6" style="text-align: left;">
                                                <p id="pAseguradora"> </p>
                                            </div>
                                        </div>

                                        <div class="row  " style="padding-bottom: 1%">
                                            <div class="col-sm-6" style="text-align: left;">
                                                <b>Fecha de expiración:</b>
                                            </div>
                                            <div class="col-sm-6" style="text-align: left;">
                                                <p id="pexpiration_date"> </p>
                                            </div>
                                        </div>



                                    </div>
                                    <!-- Modal -->
                                 <div class="modal fade" id="ModalPolizaImg" tabindex="-1" role="dialog"
                                        aria-labelledby="ModalPolizaImgTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLongTitle">Agregar datos de
                                                        aseguradora</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">

                                                    <div class="row  " style="padding-bottom: 1%;">
                                                        <div class="col-sm-5" style="text-align: left;"
                                                            style="padding-left: 2%; padding-right: 2%">

                                                            <label for="Aseguradora" class="form-label">Empresa
                                                                aseguradora
                                                            </label>

                                                            <select name="Aseguradora" id="Aseguradora"
                                                                class="form-control">
                                                                @foreach ($cataseg as $aseguradoras)
                                                                    <option value="{{ $aseguradoras->id }}">
                                                                        {{ $aseguradoras->nombre }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-sm-5" style="text-align: left;"
                                                            class="Name_aseg_otro_div" id="Name_aseg_otro_div"
                                                            style="padding-left: 2%; padding-right: 2%" hidden>
                                                            <label for="Name_aseg_otro" class="form-label">Nombre
                                                                aseguradora
                                                            </label>
                                                            <input type="text" id="Name_aseg_otro"
                                                                name="Name_aseg_otro" class="form-control"
                                                                placeholder="Nombre aseguradora">


                                                        </div>

                                                    </div>

                                                    <br>

                                                    <div class="row  " style="padding-bottom: 1%;">
                                                        <div class="col-sm-4" style="text-align: left; padding-left: 2%">
                                                            <label for="POLICY_NO" class="form-label">Número de póliza:
                                                            </label>
                                                            <input type="text" id="POLICY_NO" name="POLICY_NO"
                                                                class="form-control" placeholder="Número de póliza"
                                                                onkeypress="return Solo_Texto(event);"  pattern=".{3,}" required title="minimo 3 caracteres" maxlength="20">
                                                        </div>
                                                        <div class="col-sm-8"
                                                            style="text-align: left; padding-right: 2%;">
                                                            <label for="expiration_date" class="form-label">Fecha de
                                                                vencimiento:</label>
                                                            <input type="date" id="expiration_date"
                                                                min="{{ date('Y-m-d') }}" name="expiration_date"
                                                                class="form-control" placeholder="Vencimiento">
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <div class="row  " style="padding-bottom: 1%;">
                                                        <div class="col-sm-12" style="text-align: left;">
                                                            <input type="file" class="dropify form-control"
                                                                name="archivo" id="archivo" data-height="100"
                                                                data-max-file-size="6M" required />
                                                        </div>
                                                    </div>
                                                    <div class="row  " style="padding-bottom: 1%;">
                                                        <div class="col-sm-5" style="text-align: left; padding-left: 2%">
                                                            <label for="email"
                                                                class="form-label">{{ __('messages.correo') }}: </label>
                                                            <input type="email" id="email-store" name="email"
                                                                class="form-control" maxlength="40"
                                                                placeholder="Obligatorio" required>
                                                            <p id="email-invalido" class="hiddenElement invalid">Ingresa
                                                                una dirección de email válida</p>
                                                            <span id="email_error" class="invalid-feedback"
                                                                role="alert"></span>
                                                        </div>

                                                        <div class="col-sm-5" style="text-align: left; padding-left: 2%">
                                                            <label for="telefono"
                                                                class="form-label">{{ __('messages.telefono') }}: </label>
                                                            <input type="text" pattern="([0-9]|[0-9]|[0-9]){10}"
                                                                maxlength="10" placeholder="Opcional"
                                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');"
                                                                id="telefono-store" name="telefono" class="form-control">
                                                            <p id="telefono-invalido" class="hiddenElement invalid">El
                                                                telefono debe contener 10 números</p>
                                                            <span id="telefono_error" class="invalid-feedback"
                                                                role="alert"></span>
                                                        </div>


                                                        <div class="row justify-content-center "
                                                        style="padding-bottom: 1%;text-align: center;">

                                                        <div class="col-sm-10"
                                                            style="text-align: left;padding-top: 1%; padding-left: 2%">
                                                            <label for="observaciones"
                                                                class="form-label">{{ __('messages.observaciones') }}:
                                                            </label>
                                                            <input type="text" maxlength="100" placeholder="Opcional"
                                                                id="observaciones-store" name="observaciones"
                                                                class="form-control">
                                                            <p id="observaciones-invalido" class="hiddenElement invalid">
                                                               </p>
                                                            <span id="observaciones_error" class="invalid-feedback"
                                                                role="alert"></span>
                                                        </div>
                                                    </div>



                                                        <div class="row  " style="padding-bottom: 1%;">

                                                            <p><input type="checkbox" id="checkterms" name="checkterms">
                                                                En
                                                                este acto, <u>manifiesto que me hago sabedor que incurrir en
                                                                    falsedad de manifestaciones, declaraciones o el
                                                                    incumplimiento a
                                                                    las disposiciones administrativas puede ser causa para
                                                                    la
                                                                    revocación y/o nulidad de la concesión y/o actos
                                                                    administrativos
                                                                    que se deriven de la falta de cumplimiento, conforme a
                                                                    los
                                                                    supuestos que señala el Código de Justicia
                                                                    Administrativa
                                                                    del
                                                                    Estado de Michoacán de Ocampo.</u></p>




                                                        </div>



                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Cerrar</button>
                                                        <button type="button" class="btn colorMorado guardarcambios"
                                                            hidden>Guardar
                                                            cambios</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                </div>
                            </div>

                           
                        </div>
                    </div>






                </div>



                <div class="card" id="" style="margin-left:20px">

                    <div class="logositiooficial" style="">

                        <img src="{{ asset('/img/LogosGD/GDSello.svg') }}" style=""
                            class="css-class imgSitioOficial" alt="alt text">
                        <div>

                            <p>
                            <h5 style="color: #6A0F49"><b> Sitio oficial </b></h5>
                            </p>

                            Este es un sitio validado por la Secretaría de Finanzas del estado de Michoacán a través de
                            la
                            <br>
                            Dirección General de Gobierno Digital.
                            <br>

                            Escanea el código QR para comprobar su válidez

                            <br>
                            <img src="data:validacion/image/jpeg;base64,iVBORw0KGgoAAAANSUhEUgAAAG8AAABvAQMAAADYCwwjAAAABlBMVEX///8AAABVwtN+AAABH0lEQVQ4jdXUsa3DIBAG4EMUdPYCkViDjpWcBRxnAbwSHWsgscBLR4F8+R1HSfV8tEEu+Cxh7s4HRL84RubFtckX5ixyIB2cfdSyUAc9Jm0ivdYurrWsqZf3iNhKF7FFyrP7BnlC5BtcwfNJ/4QYivXm8qeYJ0Q8nEilyzXJVCkPjlfm7RWVwGo3lD3a5dj0lGNqM7XB5FuSSabgb94RWJU5claRyNvtXckzYi3KPjl8gTp4mb1ejA1GJiqzebrGfEQl0JCKHDwfv0kiJpZrmx3JdE3FNhDNpoOk/whNi9iyyNdyPHlkmejYvb3340MisdFidDB5MB30JeyVRNZdXCvjPKrjGpGJjmVmkrnfOQ0vH0nmni86lu03/f/5e+MJhMrxtpAGpicAAAAASUVORK5CYII="
                                id="imgqr" style="border:1px solid black;width: 90px;height: 90px;" alt="">
                            <br>
                            <a href="Aviso de Privacidad Simplificado.pdf">
                                <font size="2" style="color:#707070"><u>Aviso de protección de datos</u></font>
                            </a>
                        </div>
                    </div>
                    <script>
                        let vendor = "{{ asset('css/customStyleformat.css') }}";
                    </script>
                    <script>
                        $('#checkterms').on('click', function() {
                            if ($(this).is(':checked')) {
                                // Hacer algo si el checkbox ha sido seleccionado

                                $('.guardarcambios').removeAttr('hidden');
                                // alert("El checkbox con valor " + $(this).val() + " ha sido seleccionado");
                            } else {
                                $(".guardarcambios").attr("hidden", true);

                                // Hacer algo si el checkbox ha sido deseleccionado
                                // alert("El checkbox con valor " + $(this).val() + " ha sido deseleccionado");
                            }
                        });


                        function Solo_Texto(e) {
                            var code;
                            if (!e) var e = window.event;
                            if (e.keyCode) code = e.keyCode;
                            else if (e.which) code = e.which;
                            var character = String.fromCharCode(code);
                            var AllowRegex = /^[a-zA-Z\-0-9]+$/;
                            if (AllowRegex.test(character)) return true;
                            return false;
                        }



                        var emailInput = document.getElementById("email-store");
                        emailInput.onkeyup = function() {
                            var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
                            if (emailInput.value.match(mailformat)) {
                                $("#email-invalido").css("display", "none");
                            } else {
                                $("#email-invalido").css("display", "initial");
                            }
                        }

                        var phoneInput = document.getElementById("telefono-store");
                        phoneInput.onkeyup = function() {
                            var phoneformat = /^\(?(\d{3})\)?[- ]?(\d{3})[- ]?(\d{4})$/;
                            if (phoneInput.value.match(phoneformat)) {
                                $("#telefono-invalido").css("display", "none");
                            } else {
                                $("#telefono-invalido").css("display", "initial");
                            }
                        }



                        $(document).ready(function() {
                            dropifyInit();

                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });

                        });

                        $('#Aseguradora').on('change', function() {
                            if (this.value == 11) {
                                var otroaseg = true;
                                $('#Name_aseg_otro_div').removeAttr('hidden');
                                $("#Name_aseg_otro").attr("required", true);
                            } else {
                                var otroaseg = false;
                                $('#Name_aseg_otro').removeAttr('required');
                                $("#Name_aseg_otro_div").attr("hidden", true);
                            }

                        });
                        $('.guardarcambios').click(function() {
                            var archivo = $("#archivo").val();
                            var Name_aseg_otro = $("#Name_aseg_otro").val();
                            // console.log("🚀 $ ~ archivo:", archivo)
                            var expiration_date = $("#expiration_date").val();
                            // //console.log("🚀  $ ~ expiration_date:", expiration_date)
                            var POLICY_NO = $("#POLICY_NO").val();
                            // //console.log("🚀  $ ~ POLICY_NO:", POLICY_NO)
                            var Aseguradora = $("#Aseguradora").val();
                            // //console.log("🚀 $ ~ Aseguradora:", Aseguradora)


                            var email = $("#email-store").val();

                            var telefono = $("#telefono-store").val();
                            var observaciones = $("#observaciones-store").val();
                            if (archivo != "" && expiration_date != "" && POLICY_NO != "" && Aseguradora != "" && email != "") {
                                if ((Name_aseg_otro != "" && Aseguradora == 11) || Aseguradora != 11) {
                                    $("[data-dismiss=modal]").trigger({
                                        type: "click"
                                    });
                                    $('#ModalPolizaImg').modal('hide');
                                    $('#modal').modal('hide');
                                    $('.bottonok').removeAttr('hidden');
                                    $(".botonmodall").attr("hidden", true);

                                    if (Aseguradora == 11) {
                                        $("#pAseguradora").text(Name_aseg_otro);
                                        $("#asegotro").val(Name_aseg_otro);

                                    } else {
                                        $("#pAseguradora").text($("#Aseguradora option:selected").text());

                                    }



                                    $("#pexpiration_date").text(expiration_date);
                                    $("#PPOLICY_NO").text(POLICY_NO);
                                    $("#aseg").val(Aseguradora);
                                    $("#aseg_vencim").val(expiration_date);
                                    $("#num_poliz").val(POLICY_NO);



                                    let target = document.getElementById("archivo");
                                    let source = document.getElementById("archivo2");

                                    let No_Consesion = "{{ $No_Consesion }}";
                                    let nombconses = "{{ $nombconses }}";


                                    var formData = new FormData();
                                    var archivo = $("#archivo")[0].files[0];
                                    var csrf_tpken = $('meta[name="csrf-token"]').attr('content');
                                    formData.append("_token", csrf_tpken);
                                    formData.append("archivo", archivo);
                                    formData.append("aseg", Aseguradora);
                                    formData.append("aseg_vencim", expiration_date);
                                    formData.append("num_poliz", POLICY_NO);
                                    formData.append("No_Consesion", No_Consesion);
                                    formData.append("nombconses", nombconses);
                                    formData.append("asegotro", Name_aseg_otro);
                                    formData.append("email", email);
                                    formData.append("telefono", telefono);
                                    formData.append("observaciones", observaciones);











                                    $.ajax({
                                        url: "{{ route('guardarpoliza') }}",
                                        data: formData,
                                        type: 'POST',
                                        dataType: 'json',
                                        contentType: false,
                                        processData: false,
                                        beforeSend: function() {
                                            $("#confirmAcept").attr('disabled', 'disabled');
                                            let timerInterval
                                            Swal.fire({
                                                title: 'Cargando datos, espere por favor...',
                                                html: ' <b></b>',
                                                allowOutsideClick: false,
                                                timer: 2000000,
                                                timerProgressBar: true,
                                                didOpen: () => {
                                                    Swal.showLoading()
                                                }
                                            });
                                        },
                                        success: function(response) {


                                            if (response.status == "success") {
                                                Swal.close();
                                                Swal.fire({
                                                    icon: response.status,
                                                    title: response.title,
                                                    text: response.message,
                                                    confirmButtonText: "Aceptar",
                                                });
                                            }
                                        },
                                        error: function(response) {
                                            // console.log('Error: ' + response);
                                            Swal.fire({
                                                icon: response.status,
                                                title: response.title,
                                                text: response.message,
                                                confirmButtonText: "Aceptar",
                                            });
                                        }
                                    });


                                } else {
                                    camposmal = "<ol>";
                                    if (archivo == "") {

                                        camposmal += "<li>Documento de la poliza  </li>";

                                    }
                                    if (expiration_date == "") {
                                        camposmal += "<li> Fecha de expiración de la póliza  </li>";
                                    }
                                    if (POLICY_NO == "") {
                                        camposmal += "<li> Número de poliza </li>";
                                    }
                                    if (Aseguradora == "") {
                                        camposmal += "<li>Selecciona una aseguradora </li>";
                                    }
                                    if (email == "") {
                                        camposmal += "<li>Ingresa un email </li>";

                                    } else {
                                        //  block of code to be executed if the condition1 is false and condition2 is false
                                    }
                                    camposmal += "</ol>";



                                    //     Swal.fire({
                                    //         title: 'Complete los campos ',
                                    //         icon:'warning'
                                    //         html: "camposmal.toString()",
                                    // });
                                    Swal.fire({
                                        title: '<strong><u>Complete los campos</u></strong>',
                                        icon: 'warning',
                                        html: camposmal.toString(),
                                        showCloseButton: true,
                                        showCancelButton: true,
                                        focusConfirm: false,
                                        //     confirmButtonText: '<i class="fa fa-thumbs-up"></i> Great!',
                                        //     confirmButtonAriaLabel: 'Thumbs up, great!',
                                        //     cancelButtonText: '<i class="fa fa-thumbs-down"></i>',
                                        //     cancelButtonAriaLabel: 'Thumbs down'
                                        //
                                    });
                                }


                            } else {
                                camposmal = "<ol>";
                                if (archivo == "") {

                                    camposmal += "<li>Documento de la poliza  </li>";

                                }
                                if (expiration_date == "") {
                                    camposmal += "<li> Fecha de expiración de la póliza  </li>";
                                }
                                if (POLICY_NO == "") {
                                    camposmal += "<li> Número de poliza </li>";
                                }
                                if (Aseguradora == "") {
                                    camposmal += "<li>Selecciona una aseguradora </li>";
                                }
                                if (email == "") {
                                    camposmal += "<li>Ingresa un email </li>";

                                } else {
                                    //  block of code to be executed if the condition1 is false and condition2 is false
                                }
                                camposmal += "</ol>";






                                Swal.fire({
                                    title: '<strong><u>Complete los campos</u></strong>',
                                    icon: 'warning',
                                    html: camposmal.toString(),
                                    showCloseButton: true,
                                    showCancelButton: true,
                                    focusConfirm: false,
                                    //     confirmButtonText: '<i class="fa fa-thumbs-up"></i> Great!',
                                    //     confirmButtonAriaLabel: 'Thumbs up, great!',
                                    //     cancelButtonText: '<i class="fa fa-thumbs-down"></i>',
                                    //     cancelButtonAriaLabel: 'Thumbs down'
                                    //
                                });

                            }




                        });


                        function dropifyInit() {
                            $('.dropify').dropify({

                                messages: {
                                    'default': 'Arrastre y suelte el archivo de su póliza aquí o haga click',
                                    'replace': 'Arrastre y suelte aquí o haga click para reemplazar',
                                    'remove': 'Remover',
                                    'error': 'Ooops, algo ha salido mal.'
                                },
                                error: {
                                    'fileSize': 'El archivo es muy grande (máximo 6 Mb).',
                                    'minWidth': 'El ancho de la imágen es muy pequeño (mínimo px).',
                                    'maxWidth': 'El ancho de la imágen es muy grande (máximo px).',
                                    'minHeight': 'El alto de la imágen es muy pequeño (mínimo px).',
                                    'maxHeight': 'El alto de la imágen es muy grande (máxima px).',
                                    'imageFormat': 'El formato de esta imágen no esta permitido (solo JPG).',
                                    'fileFormat': 'El formato de archivo no esta permitido (solamente pdf, doc, docx, xls, xlsx).',
                                }
                            });
                        }
                    </script>
                </div>
            </div>
        </div>
    @endsection
