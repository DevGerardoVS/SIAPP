<!--modal store-->
<div class="modal fade bd-example-modal-lg" id="modalNvo" tabindex="-2"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header colorMorado">
                <h5 id="titulo_modal" class="modal-title" id="staticBackdropLabel">{{__("messages.create_new_user")}}</h5>
                <button type="button" class="btn-close" onclick="limpiarCampos()" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formRegistro" class="justify-content-md-center" action="{{ route('users-add') }}" method="POST">
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="col-sm-4">
                            <input type="hidden" id="clave-store-form-hidden" name="clave" class="form-control" >
                            <label for="nombre" class="form-label">{{__("messages.nombres")}}: </label>
                            <input type="text" id="nombre-store" name="nombre" class="form-control" onkeypress="return Solo_Texto(event);" maxlength="50" required>
                            <span id="nombre_error" class="invalid-feedback" role="alert"></span>
                        </div>
                        <div class="col-sm-4">
                            <label for="paterno" class="form-label">{{__("messages.apellido_p")}}: </label>
                            <input type="text" id="paterno-store" name="paterno" class="form-control" onkeypress="return Solo_Texto(event);" maxlength="50">
                            <span id="paterno_error" class="invalid-feedback" role="alert"></span>
                        </div>
                        <div class="col-sm-4">
                            <label for="materno" class="form-label">{{__("messages.apellido_m")}}: </label>
                            <input type="text" id="materno-store" name="materno" class="form-control" onkeypress="return Solo_Texto(event);" maxlength="50">
                            <span id="materno_error" class="invalid-feedback" role="alert"></span>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-3">
                            <label for="username" class="form-label">{{__("messages.nombre_user")}}: </label>
                            <input type="text" id="username-store" name="username" class="form-control" maxlength="15" required>
                            <span id="username_error" class="invalid-feedback" role="alert"></span>
                        </div>
                        <div class="col-sm-6">
                            <label for="email" class="form-label">{{__("messages.correo")}}: </label>
                            <input type="email" id="email-store" name="email" class="form-control" maxlength="100" required>
                            <p id="email-invalido" class="hiddenElement invalid">Ingresa una dirección de email válida</p>
                            <span id="email_error" class="invalid-feedback" role="alert"></span>
                        </div>
                        <div class="col-sm-3">
                            <label for="telefono" class="form-label">{{__("messages.telefono")}}: </label>
                            <input type="text" pattern="([0-9]|[0-9]|[0-9]){10}" maxlength="10" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" id="telefono-store" name="telefono" class="form-control" >
                            <p id="telefono-invalido" class="hiddenElement invalid">El telefono debe contener 10 números</p>
                            <span id="telefono_error" class="invalid-feedback" role="alert"></span>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-4" >
                            <label id="lbl-password-store" for="password" class="form-label">{{__("messages.contraseña")}}: </label>
                            <input type="password" id="password-store" name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" class="form-control @error('password') is-invalid @enderror" minlength="8" maxlength="200">
                            <div id="message">
                                <p>La Contraseña debe concidir con lo siguiente:</p>
                                <p id="letter" class="invalid">Una letra <b>minuscula</b> </p>
                                <p id="capital" class="invalid">Una letra <b>mayuscula</b></p>
                                <p id="number" class="invalid">Un <b>número</b></p>
                                <p id="length" class="invalid">Al menos <b>8 caracteres</b></p>
                            </div>
                            <span id="password_error" class="invalid-feedback" role="alert"></span>
                        </div>
                        <div class="col-sm-4" >
                            <label id="lbl-confirm-password-store" for="confirm-password" class="form-label">{{__("messages.confirm_contraseña")}}: </label>
                            <input type="password" id="confirm-password-store" name="password_confirmation" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" class="form-control @error('confirm-password') is-invalid @enderror" minlength="8" maxlength="200">
                            <div id="message1">
                                <p id="coincide" class="valid">Las contraseñas coinciden</p>
                                <p id="nocoincide" class="invalid">Las contraseñas no coinciden</p>
                            </div>
                            <span id="password_confirmation_error" class="invalid-feedback" role="alert"></span>
                        </div>
                        <div class="col-sm-4">
                            <label for="delegacion" class="form-label">{{__("messages.delegacion")}}: </label>
                            <input type="text" id="delegacion-store" name="delegacion" class="form-control" onkeypress="return Solo_Texto(event);" maxlength="50">
                            <span id="delegacion_error" class="invalid-feedback" role="alert"></span>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="perfil" class="form-label">{{__("messages.perfil")}}: </label>
                            <select id="perfil-store-form" name="perfil" class="form-select" required>
                                <option value="" >{{__("messages.seleccionar_perfil")}}</option>
                                @foreach ($perfiles as $perfil)
                                    <option value="{{$perfil->id}}" data-id="{{$perfil->tipo_perfil}}">{{$perfil->nombre}}</option>
                                @endforeach
                            </select>
                            <span id="perfil_error" class="invalid-feedback" role="alert"></span>
                        </div>
                        <div class="col-sm-4">
                            <label for="estatus" class="form-label">{{__("messages.estatus")}}: </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="me-2">
                                        <input type="radio" value="1" name="estatus" id="radio-vigente-store" checked aria-label="Radio button for following text input"> 
                                        {{__("messages.vigente")}} 
                                    </label>
                                    <label>
                                        <input type="radio" value="0" name="estatus" id="radio-no-vigente-store" aria-label="Radio button for following text input"> 
                                        {{__("messages.no_vigente")}}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <button type="button" class="btn btn-secondary" onclick="limpiarCampos()" data-bs-dismiss="modal">{{__("messages.cancelar")}}</button>
                    <button type="submit" id="btn_guardar" class="btn colorMorado" >{{__("messages.guardar")}}</button>
                </form>
            </div>
        </div>
    </div>
</div>