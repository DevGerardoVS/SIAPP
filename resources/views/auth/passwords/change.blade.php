    @extends('layouts.app')

    @section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Change Password') }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('change_password') }}">
                            @csrf 
    
                            @if (session('status'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @else
                                    @foreach ($errors->all() as $error)
                                    <div class="alert alert-danger" role="alert">
                                        {{ $error }}
                                    </div>
                                    @endforeach
                                @endif

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">{{__('Contraseña actual')}}</label>
    
                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" name="contraseña_actual" autocomplete="contraseña_actual">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">{{('Nueva contraseña')}}</label>
    
                                <div class="col-md-6">
                                    <input id="nueva_contraseña" type="password" class="form-control  @error('nueva_contraseña')  is-invalid @enderror"  pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required minlength="8" name="nueva_contraseña" autocomplete="contraseña_actual">
                                    <div id="message">
                                        <p>La Contraseña debe concidir con lo siguiente:</p>
                                        <p id="letter" class="invalid">Una letra <b>minuscula</b> </p>
                                        <p id="capital" class="invalid">Una letra <b>mayuscula</b></p>
                                        <p id="number" class="invalid">Un <b>número</b></p>
                                        <p id="length" class="invalid">Al menos <b>8 caracteres</b></p>
                                    </div>
                                    @error('nueva_contraseña')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
    
                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">{{('Confirmar contraseña')}}</label>
        
                                <div class="col-md-6">
                                    <input id="confirmar_nueva_contraseña" type="password" class="form-control" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required minlength="8" name="confirmar_nueva_contraseña" autocomplete="contrasenia_actual">
                                </div>
                                <div id="message1">
                                    <p id="coincide" class="valid">Las contraseñas coinciden</p>
                                    <p id="nocoincide" class="invalid">Las contraseñas no coinciden</p>
                                </div>
                                @error('confirmar_nueva_contraseña')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
    
                            <div class="form-group row mb-0">
                                <div class="col-md-12 d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary">
                                        {{('Cambiar contraseña')}}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">

        //Validacion de contraseñas
        var passwordInput = document.getElementById("nueva_contraseña");
            var confirmpass = document.getElementById("confirmar_nueva_contraseña");
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
    </script>

    @endsection