@extends('layouts.app')
<?php
$stName = __('messages.nombre_sistema');
$acr = 'COCOTRA';

?>
@section('content')
    @if ($errors->any())
        {{-- @if ($errors->has('')) --}}
        <script type="text/javascript">
            var errorsss = "{{ $errors->first() }}";
            // console.log("🚀 ~ file: Desbloqueoconcesion.blade.php:12 ~ errorsss:", errorsss)

            switch (errorsss) {
                case "correcto":
                    
                    $('#formRegistro').find("#No_Consesion").val('');
                    $(".No_Consesion").val('');
                    var errorsss = "{{ $errors }}";

                    var errojson = JSON.parse(errorsss.replace(/&quot;/g, '"'))
                    // console.log("🚀 ~ file: Desbloqueoconcesion.blade.php:20 ~ errojson:", errojson)
                    Swal.fire({
                        icon: "success",
                        title: errojson.title,
                        text: errojson.message,
                        confirmButtonText: "Aceptar",
                    });
                    break;
                case "error":
                
                $('#formRegistro').find("#No_Consesion").val('');
                    var errorsss = "{{ $errors }}";
                    var errojson = JSON.parse(errorsss.replace(/&quot;/g, '"'))
                    // console.log("🚀 ~ file: Desbloqueoconcesion.blade.php:30 ~ errojson:", errojson)
                    Swal.fire({
                        icon: "warning",
                        title: errojson.title,
                        text: errojson.message,
                        confirmButtonText: "Aceptar",
                    });
                    break;

                default:

                    Swal.fire({
                        icon: "warning",
                        title: "Sin resultados",
                        text: "Revisa la información nuevamente",
                        confirmButtonText: "Aceptar",
                    });
                    break;

            }
        </script>
    @endif

    <div class="row justify-content-center" style="width: 100%">

        <nav class="navbar colorgriss" style="width: 100%">
            {{-- <div class="container" style="width: 100%"> --}}
            <div class="row" style=" padding-left: 3%; width: 100%">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-5 col-xl-4">
                    <img src="{{ asset('img/LogosHeader&Footer/logosHeader.svg') }}" id="logo_cocotra" alt="logo">
                </div>

                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-7 col-xl-8">
                    <p id="title_concesiones"><b>Desbloqueo de Pago de
                            Concesiones en Línea</b></p>
                </div>

                {{-- </div> --}}
            </div>
        </nav>

        <div class="card" style="padding-left: 3%; padding-right: 3% ;">
            <div class="row" style="text-align: center; ">
                <div class="card-header col-sm" style="border: 1px solid white; background-color: #FFC3D0">
                    <b>Realizar la búsqueda </b>
                </div>

            </div>


            <div class="card-body">
                <div class="row" style="text-align: center; ">
                    <div class="col-sm"><u>ingresar número de concesión a desbloquear
                        </u>
                        <br> <br>
                    </div>
                    <form id="formRegistro" class="justify-content-md-center"
                        action="{{ route('desbloqueoconcesionupdate') }}" method="POST">
                        @csrf
                        @method('POST')
                        <div class="row" style="text-align: center; ">
                            <div class="col-sm-1 col-md-3 col-lg-3 col-xl-3" style="text-align: left">
                            </div>
                            <div class="col-sm-10 col-md-6 col-lg-6 col-xl-6">
                                {{-- <div class="row d-flex justify-content-center " style="padding-bottom: 1%">
                                <div class="col-sm-5 col-md-5 col-lg-4 col-xl-4" style="text-align: left" >
                                    <label for="RFC"  class="form-label">{{ __('RFC') }}:
                                    </label>
                                </div>
                                <div class="col-sm-6 col-md-7 col-lg-6 col-xl-5">
                                    <input tabindex=""  type="text" style="text-align:left;"
                                        value="{{ old('RFC')}}" pattern="^[A-Z,Ñ,&]{3,4}[0-9]{2}[0-1][0-9][0-3][0-9][A-Z,0-9]?[A-Z,0-9]?[0-9,A-Z]?$" title="RFC" id="RFC" name="RFC"
                                        class="form-control @if ($errors->any() && str_contains($errors->first(), 'rfc')) is-invalid @endif" maxlength="19" autocomplete="RFC" required>
                                        @if ($errors->any() && str_contains($errors->first(), 'rfc'))<span id="RFC_error" class="invalid-feedback" role="alert"><strong>{{ $errors->RFC }}</strong></span>@endif
                                </div>
                            </div> --}}
                                <div class="row d-flex justify-content-center " style="padding-bottom: 1%">
                                    <div class="col-sm-5 col-md-5 col-lg-4 col-xl-4" style="text-align: left">
                                        <label for="No_Consesion" class="form-label">{{ __('messages.no_concesion') }}:
                                        </label>
                                    </div>
                                    <div class="col-sm-6 col-md-7 col-lg-6 col-xl-5">
                                        <input tabindex=""  type="text" 
                                            style="text-align:left;" title="Numero de consesión"
                                            value="" id="No_Consesion" name="No_Consesion"
                                            class="form-control No_Consesion"
                                            maxlength="13" autocomplete="No_Consesion" required>
                                        @if ($errors->any() && str_contains($errors->first(), 'concesión'))
                                            {{-- <span id="No_Consesion_error" class="invalid-feedback"
                                                role="alert"><strong>{{ $errors->first() }}</strong></span> --}}
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-sm-1 col-md-3 col-lg-3 col-xl-3" style="text-align: left">
                        </div>
                </div>
                <br>
                <div class="row d-flex justify-content-center " style="padding-bottom: 1%">
                    <div class="col-md-12" style="text-align: center">
                        <button type="submit" class="btn botonbuscar">
                            <i class="fa fa-search"></i> {{ __('Desbloquear') }}
                        </button>
                    </div>
                </div>
                </form>
            </div>
        </div>
        <div class="card-body" style="text-align: center; padding-top: 4%; padding-bottom: 4%;">

            <img src="{{ asset('/img/LogosGD/GDSello.svg') }}" class="css-class imgSitioOficial" alt="alt text">
            <div>
                <p>
                <h5 style="color: #6A0F49"><b> Sitio oficial </b></h5>
                </p>
                Este es un sitio validado por la Secretaría de Finanzas del estado de Michoacán a través de la
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
    </div>
    </div>
@endsection
