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
                    @else
                        
                    @endif
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="justify-content-center text-center">
                            <h2 style="padding-top: 5%"><strong>{{ __('messages.nombre_sistema') }}</strong></h2>
                            <br>
                            <div style="text-align:center">
                                <h3>{{ __('Reset Password') }} </h3>
                            </div>
                        </div>

                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="email" class="col-md-10  text-left">{{ __('Dirección de correo') }}</label>
                                <input id="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                          
                            <div class="row" style="text-align:center">
                                <div class="col-2"></div>
                                <div class=" col-8 justify-content-center">
                                    <br>
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Send Password Reset Link') }}
                                    </button>
                                </div>
                                
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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
                    // if (willDelete) {
                    //     window.location.href = '/login';
                    // } else {
                    //     window.location.href = '/login';
                    // }
                });
        @endif
    </script>
@endsection
