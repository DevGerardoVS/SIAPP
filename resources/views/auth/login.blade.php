@extends('layouts.app')

@section('content')
    <div class="container containerlogin" style=" min-width:100%; display: contents;">
        <div class="row containerlogin" style=" min-width:100%; ">
            <div class="  col-sm-6 text-center  ladizqlogin">
                <div style="padding-top: 18%">
                    {{-- <h2 style="padding-top: 5%"> <b>Sistema Integral de Análisis Programático Presupuestal </b> </h2> --}}
                </div>

                <div style="text-align: left;">
                    <img src="{{ asset('/img/LogosGD/Group20.png') }}" style="width: 75%; left" alt="alt text">
                </div>

                <!-- Copyright -->

                <!-- Copyright -->
            </div>

            <div class="col-sm-6">

                @php
                    $logouttrue = false;
                    $previus = false;
                    $intented = false;
                    
                    $actualink = Session::all();
                    if (isset($actualink['url']['intended'])) {
                        $intented = true;
                    }
                    
                @endphp
                <div class="card-body">
                    <div class="  " style="text-align: center; padding-top: 34%;padding-bottom: 4%;">
                        <!--<img src="{{ asset('/img/LogosGD/GDVertical.png') }}" style="width: 30%" class="css-class"
                                    alt="alt text">-->
                    </div>
                    @if (isset($errors) && $errors->any())
                        <br>
                        <br>
                        <div class="alert alert-danger">
                            <ul style="text-align: center; ">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <h5 class="text-center" style="font-size: 24px; ">
                            <b>Calendarización y Asignación de Presupuest</b>
                        </h5>
                        <br>
                        {{-- <h5 class="text-center" style="font-size: 24px; padding-bottom: 3%">En Línea </h5> --}}

                        <h5 class="text-center" style="font-size: 24px">Inicio de sesión</h5>
                        <br>
                        <div class="row mb-3" style="text-align: center; ">
                            <input id="username" type="username" style="width: 75%"
                                class="form-control @error('username') is-invalid @enderror" name="username"
                                placeholder="{{ __('Usuario') }}" value="{{ old('username') }}" required
                                autocomplete="username" autofocus>

                            @error('username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="row mb-3" style="text-align: center">
                            <input id="password" type="password" style="width: 75%"
                                class="form-control @error('password') is-invalid @enderror" name="password"
                                placeholder="{{ __('Password') }}" required autocomplete="current-password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="row mb-3" hidden>
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div>
                                <div class="col-md-6 offset-md-3" style="text-align: center">
                                    <button type="submit" class="btn botoninicio">
                                        {{ __('Iniciar sesión') }}
                                    </button>
                                </div>
                                <div style="text-align: center">
                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="text-center colortextfoot" style="margin-bottom: 15%" id="footerA">
                    © 2023 Dirección General de Gobierno Digital | Secretaría de Finanzas y Administración |
                    <a href="https://www.michoacan.gob.mx">
                        Gobierno del Estado de Michoacán
                    </a>
                </div>
            </div>
        </div>
    </div>
    @if (session()->has('success'))
        <br>
        <br>
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
    @endif
    <!--Valida si hay errores por el metodo validate-->

    <script src="{{ asset('vendor/sweetalert/sweetalert.all.js') }}"></script>
    <script>
        var estaactivo = "{{ Auth::check() }}";


        @if ($intented)
            Swal.fire({
                    title: "Tu sesión ha terminado",
                    text: "Por favor ingresa nuevamente",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        window.location.href = '/login';
                    } else {
                        window.location.href = '/login';
                    }
                });
        @endif
    </script>
@endsection
