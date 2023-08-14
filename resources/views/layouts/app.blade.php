@include('layouts.links')
@include('layouts.scripts')

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<?php ini_set('memory_limit', '-1');
date_default_timezone_set("America/Mexico_City");
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @php
    use Carbon\Carbon;
         $lastActivity = Session::get('last_activity');
        $inactivityLimit = 1800; // 30 minutes (in seconds)
        if (!Auth::guest()) {
            $fullname= Auth::user()->nombre;
        }
        else{
            $fullname= "";
        }
        
      
    @endphp
    <!-- CSRF Token -->
    <meta id="meta" name="csrf-token" content="{{ csrf_token() }}">


    {!! htmlScriptTagJsApi([
        'callback_then' => 'callbackThen',
    
        'callback_catch' => 'callbackCatch',
    ]) !!}
    @if (isset($titleDesc) && $titleDesc != '' && isset($acr))
        <title>{{ $acr . ' - ' . $titleDesc }}</title>
    @else
        <title>Sistema Integral de Análisis Programático Presupuestal </title>
    @endif
  

    {{-- Page Scripts --}}
    @yield('page_scripts')

    <!-- Styles -->
 


    {{-- Page Styles --}}
    @yield('page_styles')
</head>

<body>
    <div id="app" style="">
        @if (isset(Auth::user()->id))
            <nav class="navbar navbar-expand-md navbar-dark shadow-sm colorMorado">
                <div class="container">
                    <a class="navbar-brand" href="/"
                        title="Sistema Integral de Análisis Programático Presupuestal ">
                        <img src="{{ asset('img/logoWhite.png') }}"
                            style="max-height: 45px; margin-left:10px; margin-right:10px; pointer-events: none !important;"
                            alt="logo">

                        <b>SIAPP </b>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav me-auto">
                        </ul>
                        <ul class="navbar-nav ms-auto">
                            <?php $menus = DB::select('CALL sp_menu_sidebar(?,?, ?)', [Auth::user()->id, Session::get('sistema'), null]); ?>
                            @foreach ($menus as $menu)
                                <?php $hijos = DB::select('CALL sp_menu_sidebar(?,?, ?)', [Auth::user()->id, Session::get('sistema'), $menu->id]); ?>
                                @if ($hijos)
                                    <li class="nav-item dropdown">
                                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#"
                                            role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false" v-pre>
                                            {{ $menu->nombre_menu }}
                                        </a>

                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown"
                                            style="text-align: center;">
                                            @foreach ($hijos as $hijo)
                                                <a class="dropdown-item text-sm-left" href="{{ $hijo->ruta }}">
                                                    <i class="{{ $hijo->icono }}" aria-hidden="true"></i>
                                                    &nbsp
                                                    {{ $hijo->nombre_menu }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </li>
                                @else
                                    <li class="nav-item dropdown">
                                        <a class="nav-link" href="{{ $menu->ruta }}">
                                            {{ $menu->nombre_menu }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                            <!--CERRAR SESION Y CAMBIO DE CONTRASEÑA-->
                            <li class="nav-item dropdown">

                                <a id="navbarDropdown" class="nav-link dropdown-toggle text-sm-left" href="#" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre
                                title={{{$fullname}}}>
                                <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end " aria-labelledby="navbarDropdown"
                                style="text-align: center;">
                                <h5 style=" border-bottom: 5px solid #FFC3D0;"><i class=" fa fa-user" aria-hidden="true"></i>
                                    &nbsp <b>{{{Auth::user()->username}}}</b></h5>
                                <a class="dropdown-item text-sm-left" href="{{ route('cambiar_contrasenia') }}">
                                    <i class="fa fa-key" aria-hidden="true"></i>
                                        {{ __('Cambiar contraseña') }}
                                </a>
                                
                                <a class="dropdown-item text-sm-left"  onclick="_gen.logOut()">
                                    <i class="fa fa-sign-out" aria-hidden="true"></i>
                                    {{ __('Cerrar Sesión') }}
                                </a>
                                </div>
                            </li>
                        </ul>
                        <!-- Right Side Of Navbar -->
                    </div>
                </div>
            </nav>
        @endif
        @if (Request::is('/', 'login', 'password/reset', 'cambiar-contrasenia'))
            <main style="min-height: auto; min-width:auto;">
                @yield('content')
            </main>
        @else
            <main class="py-4">
                @yield('content')
            </main>
        @endif

    </div>
</body>
<br>
<br>
<br>
<br>
@if (isset(Auth::user()->id))
    <footer class="text-center text-lg-start text-white colorMorado" style="">
        <div class="container pb-0"></div>
        <div class="text-center">
            <label class="footerMessageMain" style="">
                © {{ date('Y') }} Dirección General de Gobierno Digital | Secretaría de Finanzas y
                Administración |
                <a class="customFooterA" href="https://www.michoacan.gob.mx">
                    Gobierno del Estado de Michoacán
                </a>
            </label>
        </div>
        <div class="container pb-0"></div>

    </footer>
@endif

</html>