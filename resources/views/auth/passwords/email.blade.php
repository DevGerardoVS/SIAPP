@extends('layouts.app')

@section('content')
    <div class="container" style=" min-width:100%;">
        <div class="row " style=" min-width:100%;">
            <div class="  col-sm-6 text-center  ladizqlogin">
                <div style="padding-top: 6%">
                    
                    <h1 style="padding-top: 5%"> {{ __('messages.nombre_sistema') }}</h1>
                    {{-- <h1 style="padding-bottom:5%;"> {{ __('Transporte Público') }}</h1> --}}
                </div>

                <div style="text-align: left;">
                <img src="{{ asset('/img/LogosGD/Group20.png') }}" style="width: 65%; left" 
                    alt="alt text">
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
                <br>
                <br><br><br>
                <div class="  " style="text-align: center; padding-top: 4%;padding-bottom: 4%;">
                    <img src="{{ asset('/img/LogosGD/GDVertical.png') }}" style="width: 26%" class="css-class"
                        alt="alt text">
                </div>
                <br>
               <div style="text-align:center" ><h1>{{ __('Reset Password') }} </h1></div>
               <br>
               <br>
               <div class="card-body">
                   @if (session('status'))
                       <div class="alert alert-success" role="alert">
                           {{ session('status') }}
                       </div>
                   @endif
                      
                   <form method="POST" action="{{ route('password.email') }}">
                       @csrf

                       <div class="row mb-3">
                           <label for="email" class="col-md-4 col-form-label text-md-end">{{__('Dirección de correo')}}:</label>

                           <div class="col-md-6">
                               <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                               @error('email')
                                   <span class="invalid-feedback" role="alert">
                                       <strong>{{ $message }}</strong>
                                   </span>
                               @enderror
                           </div>
                       </div>

                       <div class="row mb-0">
                           <div  style="text-align:center">
                               <button type="submit"  class="btn btn-primary">
                                   {{ __('Send Password Reset Link') }}
                               </button>
                           </div>
                       </div>
                   </form>
               </div>
                {{-- <div class="text-center colortextfoot" style="padding-bottom: 2%" id="footerA">
                    © 2023 Dirección General de Gobierno Digital | Secretaría de Finanzas y Administración |
                    <a href="https://www.michoacan.gob.mx">
                        Gobierno del Estado de Michoacán
                    </a>
                </div> --}}
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
