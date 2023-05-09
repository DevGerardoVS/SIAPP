<?php
    $titleDesc = __("messages.admin_users");
    //{{$titleDesc}}
?>
@extends('layouts.app')

@section('content')

    <!--Mensajes de error o exito-->
    {{--@if (session('success'))--}}
        <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{__("messages.exito")}}</h5>
                <button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    <h6 id="successMessage" class="alert alert-success">{{session('success')}}</h6>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary close_modal" data-dismiss="modal">{{__("messages.cerrar")}}</button>
                </div>
            </div>
            </div>
        </div>
    {{--@endif--}}

    {{--@if ($errors->any())--}}
        <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{__("messages.error")}}</h5>
                <button type="button" class="close close_modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" style="background-color: white">
                        <ul id="error-list">
                            @foreach ($errors->all() as $error)
                                <li style="color: red">* {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary close_modal" data-dismiss="modal">{{__("messages.cerrar")}}</button>
                </div>
            </div>
            </div>
        </div>
        
    {{--@endif--}}

    <!--Tabla de resultados-->
    <div class="container w-100 p-4">
        <h5 style="text-align: left; font-weight: bold;">{{__("messages.cat_users")}}</h5>
        <form action="{{ route('get-users') }}" id="buscarForm" method="post">
            @csrf
            <div class="row">
                <div class="col-sm-2">
                    @if(verifyPermission('usuarios.usuarios.agregar'))
                    <button type="button" class="btn" style="color:#0d6efd" onclick="Nueva()"><i class="fas fa-plus"> {{__("messages.nuevo_registro")}}</i></button>
                    @endif
                </div>
                <div class="col-sm-8"></div>
                @if(verifyPermission('usuarios.usuarios.exportar'))
                <div class="col-sm-2">
                    <button type="button" class="btn" style="color:#0d6efd"><a href="{{ route('users-export')}}"><i class="fas fa-plus">{{__("messages.export_excel")}}</i></a></button>
                </div>
                @endif
            </div>
        </form>
        <br>
       
        <table id="catalogo" class="table table-striped table-bordered text-center " style="width:100%">
            <thead>
                <tr class="colorMorado">
                    <th>{{__("messages.clave")}}</th>
                    <th>{{__("messages.nombre")}}</th>
                    <th>{{__("messages.user")}}</th>
                    <th>{{__("messages.email")}}</th>
                    <th>{{__("messages.telefono")}}</th>
                    <th>{{__("messages.user_creacion")}}</th>
                    <th>{{__("messages.perfil")}}</th>
                    <th>{{__("messages.delegacion")}}</th>
                    <th>{{__("messages.estado")}}</th>
                    <th>{{__("messages.acciones")}}</th>
                </tr>
            </thead>
        </table>
        
    </div>
    <!--modal delete-->
    <div class="modal fade" id="modaldel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header colorMorado">
                <h5 class="modal-title" id="exampleModalLabel">{{__("messages.deshabilitar_user")}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{__("messages.msg_deshabilitar_user")}}: <strong id="deshabilitar-user"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__("messages.cancelar")}}</button>
                <button id="confirmar-baja" type="button" class="btn btn-primary">{{__("messages.deshabilitar_user")}}</button>
                
            </div>
            </div>
        </div>
    </div>
    <!--modal store-->
    @include('users.registrar_usuarios_modal')

    @isset($dataSet)
    @include('panels.datatable')
    @endisset
    <script src="{{ asset('vendor/sweetalert/sweetalert.all.js') }}"></script>
    <script type="text/javascript">
        //inicializamos el data table
        $(document).ready(function() {
            getData();

            $("form").keypress(function(e) {
                //Enter key
                if (e.which == 13) {
                    return false;
                }
            });

            $("#modalNvo").on('click','#btn_guardar',function(e){
                e.preventDefault();
                form = $(this).closest('#formRegistro');
                $.ajax({
                    url:form.attr('action'),
                    type: "POST",
                    data: form.serializeArray(),
                    dataType: 'json',
                    beforeSend: function() {
                        let timerInterval
                        Swal.fire({
                            title: 'Guardando datos, espere por favor...',
                            html: ' <b></b>',
                            allowOutsideClick: false,
                            timer: 2000000,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });
                    },
                    success:function(response){
                        getData();
                        Swal.close();
                        limpiarCampos();
                        $("#successModal").find("#successMessage").html(response.message);
                        $('#successModal').modal('show');
                        $("#modalNvo").modal('toggle');
                    },
                    error: function(response) {
                        var mensaje="";
                        $.each(response.responseJSON.errors, function( key, value ) {
                            $("#modalNvo").find("#"+key+"-store").addClass("is-invalid");
                            $("#modalNvo").find("#"+key).addClass('is-invalid');
                            $("#modalNvo").find("#"+key+'_error').addClass('d-block');
                            $("#modalNvo").find("#"+key+'_error').html('<strong>'+value+'</strong>');
                            //$("#error-list").append("<li>"+value+"</li>");
                            mensaje += value+"\n";
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: mensaje,
                            confirmButtonText: "Aceptar",
                        });
                        //$('#errorModal').modal('show');
                        console.log('Error: ' +  JSON.stringify(response.responseJSON));
                    }
                });
            });
        });

        $("#modalNvo").on('click','.close_modal',function(e){
            $("#modalNvo").modal('toggle');
        });

        $("#successModal").on('click','.close_modal',function(e){
            $("#successModal").modal('toggle');
        });

        $("#errorModal").on('click','.close_modal',function(e){
            $("#errorModal").modal('toggle');
        });

        @if (session('success'))
            $('#successModal').modal('show');
        @endif
        @if ($errors->any())

            $('#errorModal').modal('show');
        @endif
        
        function Nueva() {
            $('#modalNvo').find("#lbl-password-store").attr('style','display: block;');
            $('#modalNvo').find("#password-store").attr('style','display: block;');
            $('#modalNvo').find("#lbl-confirm-password-store").attr('style','display: block;');
            $('#modalNvo').find("#confirm-password-store").attr('style','display: block;');
            $('#modalNvo').find("#titulo_modal").html('{{__("messages.create_new_user")}}');
            $('#modalNvo').find("#formRegistro").attr('action',"{{route('users-add')}}");
            $('#modalNvo').modal('show');
        }
        limpiarCampos();
        function limpiarCampos(){
            $('#clave-store-form-hidden').val("");
            $('#nombre-store').val("");
            $('#paterno-store').val("");
            $('#materno-store').val("");
            $('#username-store').val("");
            $('#password-store').val("");
            $('#confirm-password-store').val("");
            $('#email-store').val("");
            $('#telefono-store').val("");
            $('#delegacion-store').val("");
            $('#perfil-store-form').val("");

            $('#nombre-update').val("");
            $('#paterno-update').val("");
            $('#materno-update').val("");
            $('#username-update').val("");
            $('#password-update').val("");
            $('#email-update').val("");
            $('#telefono-update').val("");

            $('#modalNvo').find('#nombre_error').removeClass('d-block').attr('style','display: none;');
            $('#modalNvo').find('#paterno_error').removeClass('d-block').attr('style','display: none;');
            $('#modalNvo').find('#username_error').removeClass('d-block').attr('style','display: none;');
            $('#modalNvo').find('#materno_error').removeClass('d-block').attr('style','display: none;');
            $('#modalNvo').find('#password_error').removeClass('d-block').attr('style','display: none;');
            $('#modalNvo').find('#password_confirmation_error').removeClass('d-block').attr('style','display: none;');
            $('#modalNvo').find('#email_error').removeClass('d-block').attr('style','display: none;');
            $('#modalNvo').find('#telefono_error').removeClass('d-block').attr('style','display: none;');
            $('#modalNvo').find('#delegacion_error').removeClass('d-block').attr('style','display: none;');
            $('#modalNvo').find('#perfil_error').removeClass('d-block').attr('style','display: none;');
            $("#modalNvo").find("#nombre-store").removeClass("is-invalid");
            $("#modalNvo").find("#paterno-store").removeClass("is-invalid");
            $("#modalNvo").find("#materno-store").removeClass("is-invalid");
            $("#modalNvo").find("#username-store").removeClass("is-invalid");
            $("#modalNvo").find("#password-store").removeClass("is-invalid");
            $("#modalNvo").find("#email-store").removeClass("is-invalid");
            $("#modalNvo").find("#telefono-store").removeClass("is-invalid");
            $("#modalNvo").find("#delegacion-store").removeClass("is-invalid");

            $("#modalNvo").find("#email-invalido").attr("style","display: none;");
            $("#modalNvo").find("#telefono-invalido").attr("style","display: none;");
            $("#modalNvo").find("#message1").attr("style","display: none;");
            $("#modalNvo").find("#message").attr("style","display: none;");
        }
        

        function eliminarRegistro(i){
            modal = $('#modaldel');
            modal.modal('show');
            $('#confirmar-baja').removeAttr('onclick');
            
            $.ajax({
                url:"{{route('users-edit')}}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "clave": i
                },
                success:function(response){
                    modal.find("#deshabilitar-user").text(response.user.nombre);
                    $('#confirmar-baja').attr('onClick', 'confirmarBaja('+i+');');
                },
                error: function(response) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response,
                        confirmButtonText: "Aceptar",
                    });
                    //$('#errorModal').modal('show');
                    console.log('Error: ' +  JSON.stringify(response));
                }
            });
        }
        function confirmarBaja(i){
            console.log("entra");
            $.ajax({
                url:"{{route('users-destroy')}}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": i
                },
                success:function(response){
                    getData();
                    $("#successModal").find("#successMessage").html(response.message);
                    $('#successModal').modal('show');
                    $("#modaldel").modal('toggle');
                },
                error: function(response) {
                    $("#modaldel").modal('toggle');
                    $('#errorModal').modal('show');
                    console.log('Error: ' + response);
                }
            });
        }
        function editarRegistro(i){
            $('#clave-store-form').val("");
            $('#siglas-store-form').val("");
            $('#nombre-store-form').val("");
            $('#clave-store-form-hidden').val("");
            
            $('#modalNvo').find("#lbl-password-store").attr('style','display: none;');
            $('#modalNvo').find("#lbl-confirm-password-store").attr('style','display: none;');
            $('#modalNvo').find("#password-store").attr('style','display: none;');
            $('#modalNvo').find("#confirm-password-store").attr('style','display: none;');
            $('#modalNvo').find("#confirm").attr('style','display: none;');

            $('#modalNvo').find("#titulo_modal").html('{{__("messages.edicion_user")}}');
            $('#modalNvo').find("#formRegistro").attr('action',"{{route('users-update')}}");
            $('#modalNvo').modal('show');
            $.ajax({
                url:"{{route('users-edit')}}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "clave": i
                },
                success:function(response){
                    Swal.close();

                    $('#clave-store-form-hidden').val(response.user.id);
                    $('#nombre-store').val(response.user.nombre);
                    $('#paterno-store').val(response.user.apellidoP);
                    $('#materno-store').val(response.user.apellidoM);
                    $('#username-store').val(response.user.username);
                    $('#email-store').val(response.user.email);
                    $('#telefono-store').val(response.user.telefono);
                    $('#delegacion-store').val(response.user.delegacion);
                    $("#perfil-store-form").val(response.user.perfil_id);
                    
                    if(response.user.estatus=="Vigente"){
                        $('#radio-vigente-store').prop("checked", true);
                    }else{
                        $('#radio-no-vigente-store').prop("checked", true);
                    }
                }
            });
        }

        function Solo_Texto(e) {
            var code;
            if (!e) var e = window.event;
            if (e.keyCode) code = e.keyCode;
            else if (e.which) code = e.which;
            var character = String.fromCharCode(code);
            var AllowRegex  = /^[\ba-zA-Z\s-]$/;
            if (AllowRegex.test(character)) return true;     
            return false; 
        }

        //Validacion de contraseñas
        var passwordInput = document.getElementById("password-store");
        var confirmpass = document.getElementById("confirm-password-store");
        var coincide = document.getElementById("coincide");
        var nocoincide = document.getElementById("nocoincide");
        var letter = document.getElementById("letter");
        var capital = document.getElementById("capital");
        var number = document.getElementById("number");
        var length = document.getElementById("length");

        passwordInput.onkeyup = function() {
            // Validate lowercase letters
            var lowerCaseLetters = /[a-z]/g;
            if(passwordInput.value.match(lowerCaseLetters)) {
                letter.classList.remove("invalid");
                letter.classList.add("valid");
            } else {
                letter.classList.remove("valid");
                letter.classList.add("invalid");
            }

            // Validate capital letters
            var upperCaseLetters = /[A-Z]/g;
            if(passwordInput.value.match(upperCaseLetters)) {
                capital.classList.remove("invalid");
                capital.classList.add("valid");
            } else {
                capital.classList.remove("valid");
                capital.classList.add("invalid");
            }

            // Validate numbers
            var numbers = /[0-9]/g;
            if(passwordInput.value.match(numbers)) {
                number.classList.remove("invalid");
                number.classList.add("valid");
            } else {
                number.classList.remove("valid");
                number.classList.add("invalid");
            }

            // Validate length
            if(passwordInput.value.length >= 8) {
                length.classList.remove("invalid");
                length.classList.add("valid");
            } else {
                length.classList.remove("valid");
                length.classList.add("invalid");
            }
        }

        passwordInput.onfocus = function() {
            document.getElementById("message").style.display = "initial";
        }

        confirmpass.onkeyup = function() {
            if(passwordInput.value==confirmpass.value){
                coincide.style.display = "initial";
                nocoincide.style.display = "none";
            }else{
                coincide.style.display = "none";
                nocoincide.style.display = "initial";
            }
        }
        confirmpass.onfocus = function() {
            document.getElementById("message1").style.display = "initial";
        }
        confirmpass.onblur = function() {
            document.getElementById("message1").style.display = "none";
        }
        // When the user clicks outside of the password field, hide the message box
        passwordInput.onblur = function() {
            document.getElementById("message").style.display = "none";
        }

        //Validacion de correo
        var emailInput = document.getElementById("email-store");
        emailInput.onkeyup = function() {
            var mailformat = /^[a-z0-9!#$%&*+_-]+(?:\.[a-z0-9!#$%&*+_-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/;
            if(emailInput.value.match(mailformat))
            {
                $("#email-invalido").css("display","none");
            }
            else
            {
                $("#email-invalido").css("display","initial");
            }
        }

        

        var phoneInput = document.getElementById("telefono-store");
        phoneInput.onkeyup = function() {
            var phoneformat = /^\(?(\d{3})\)?[- ]?(\d{3})[- ]?(\d{4})$/;
            if(phoneInput.value.match(phoneformat))
            {
                $("#telefono-invalido").css("display","none");
            }
            else
            {
                $("#telefono-invalido").css("display","initial");
            }
        }
        
    </script>

@endsection

