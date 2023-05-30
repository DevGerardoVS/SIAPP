<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script typ1e="text/javascript">
        function callbackThen(response) {
            // read HTTP status
            // read Promise object
            response.json().then(function(data) {
                console.log(data.action);
            });

        }

        function callbackCatch(error) {

            console.error('Error:', error)

        }
    </script>

    {!! htmlScriptTagJsApi([
        'callback_then' => 'callbackThen',
    
        'callback_catch' => 'callbackCatch',
    ]) !!}
    @if (isset($titleDesc) && $titleDesc != '' && isset($acr))
        <title>{{ $acr . ' - ' . $titleDesc }}</title>
    @else
        <title>Sistema Integral de Logueo</title>
    @endif

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset(mix('vendors/css/bootstrap/bootstrap.min.css')) }}" rel="stylesheet" id="bootstrap-css">
    <link href="{{ asset(mix('vendors/css/bootstrap/bootstrap-multiselect.css')) }}" rel="stylesheet">
    <script src="{{ asset(mix('vendors/js/bootstrap/bootstrap.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/charts/chart.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/jquery/jquery.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/jquery/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('vendors/js/jquery/jquery-3.7.0.js') }}"></script>
    <script src="{{ asset(mix('vendors/js/bootstrap/bootstrap.bundle.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/bootstrap/bootstrap-multiselect.js')) }}"></script>
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/bootstrap/bootstrap.css')) }}">
    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    {{-- buttons --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.dataTables.min.css">

    <script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.css" />

    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.bootstrap4.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.jsdelivr.net/npm/datatables-buttons-excel-styles@1.2.0/js/buttons.html5.styles.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.jsdelivr.net/npm/datatables-buttons-excel-styles@1.2.0/js/buttons.html5.styles.templates.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- buttons --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap4.min.js')) }}"></script>
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css')) }}">
    <script src="{{ asset('vendor/sweetalert/sweetalert.all.js') }}"></script>
    <script>
        if (window.location.hostname != 127.0 .0 .1) {
            document.addEventListener("contextmenu", (e) => {
                e.preventDefault()
            });
            document.addEventListener("keydown", (e) => {
                e.preventDefault();
                if (e.keycode == 123) {
                    return false;
                }
            })
        }
    </script>
    <script>
        var tiempo = parseInt("{{ $_ENV['SESSION_INACTIVITYTIME'] }}") * 60;
        var reloj = setInterval(function() {
            if (tiempo <= 0) {
                clearInterval(reloj);
            }

            tiempo -= 1;
            if (tiempo == 0) {
                var urlacctual = "{{ Request::path() }}";
                if (urlacctual != 'login') {
                    Swal.fire({
                        title: 'Su sesión ha expirado',
                        text: '¿Desea iniciar sesión nuevamente?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, iniciar sesión',
                        cancelButtonText: 'No, cerrar sesión',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('login') }}";
                        } else {
                            $.ajax({
                                type: "POST",
                                url: "{{ route('logout') }}",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: `{
                                    "c": 78912,
                                    "Customer": "Jason Sweet",
                                    csrf: "{{ csrf_token() }}"
                                }`,
                                success: function(result) {
                                    window.location.href = "{{ route('login') }}";
                                },
                                dataType: "json"
                            });
                            // window.location.href = "{{ route('logout') }}";
                            // window.location.href = "{{ route('logout') }}";
                        }
                    });
                }
            }
        }, 1000);
    </Script>
    {{-- Page Scripts --}}
    @yield('page_scripts')

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/customStyle.css') }}" rel="stylesheet">

    {{-- Page Styles --}}
    @yield('page_styles')
</head>

<body>
    <div id="app" style="">
        @if (isset(Auth::user()->id))
            <nav class="navbar navbar-expand-md navbar-dark shadow-sm colorMorado">
                <div class="container">
                    <a class="navbar-brand" href="/" title="Sistema Integral">
                        <img src="{{ asset('img/logoWhite.png') }}"
                            style="max-height: 45px; margin-left:10px; margin-right:10px; pointer-events: none !important;"
                            alt="logo">

                        <b>Sistema Integral</b>
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
                                            aria-expanded="false" v-pre title="Mi Usuario">
                                            {{ $menu->nombre_menu }}
                                        </a>

                                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown"
                                            style="text-align: center;">
                                            @foreach ($hijos as $hijo)
                                                <a class="dropdown-item" href="{{ $hijo->ruta }}">
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

                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre
                                    title="Mi Usuario">
                                    <i class="fas fa-user-circle" aria-hidden="true"></i>
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown"
                                    style="text-align: center;">
                                    <a class="dropdown-item" href="{{ route('cambiar_contrasenia') }}">
                                        {{ __('Cambiar contraseña') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('Cerrar Sesión') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        class="d-none">
                                        @csrf
                                    </form>
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
    <footer class="text-center text-lg-start text-white colorMorado footer fixed-bottom footerClassMain" style="">
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

        {{-- @if (Request::is('/', 'home', 'login', 'password/reset', 'cambiar-contrasenia', ''))

    <footer class="text-center text-lg-start text-white colorMorado footer fixed-bottom footerClassMain" style="">
        <div class="gobiernoDigitalDIV" style=""></div>
     <div class="container pb-0"></div>
     <div class="text-center">
            <label class="footerMessageMain" style="">
                © {{date("Y")}} Dirección General de Gobierno Digital | Secretaría de Finanzas y Administración |
                <a class="customFooterA" href="https://www.michoacan.gob.mx">
                    Gobierno del Estado de Michoacán
                </a>
            </label>
        </div>
        @else

    <footer class="text-center text-lg-start text-white colorMorado footer footerClassMain" style="">
@endif --}}



        <div class="container pb-0"></div>

    </footer>



    <!-- Footer -->

    <!-- Footer -->
@endif

</html>
