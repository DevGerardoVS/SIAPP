

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>



    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{csrf_token()}}">
    <meta name="X-CSRF-TOKEN" content="{{csrf_token()}}">
    
    <script typ1e="text/javascript">
        function callbackThen(response) {
            // read HTTP status
            console.log(response.status);
            // read Promise object
            response.json().then(function(data) {
                console.log(data);
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
    @if (isset($titleDesc) && $titleDesc != '')
        <title>{{ $acr . ' - ' . $titleDesc }}</title>
    @else
        <title>D</title>
    @endif

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset(mix('vendors/css/bootstrap/bootstrap.min.css')) }}" rel="stylesheet" id="bootstrap-css">
    <link href="{{ asset(mix('vendors/css/bootstrap/bootstrap-multiselect.css')) }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/js/all.min.js" />

    <script src="{{ asset(mix('vendors/js/bootstrap/bootstrap.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/charts/chart.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/jquery/jquery.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/popper/popper.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/bootstrap/bootstrap.bundle.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/bootstrap/bootstrap-multiselect.js')) }}"></script>
    {{-- <link rel="stylesheet" href="{{ asset(mix('vendors/css/bootstrap/bootstrap.css')) }}"> --}}
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
    {{-- buttons --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap4.min.js')) }}"></script>
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css')) }}">
    <script src="{{ asset('vendor/sweetalert/sweetalert.all.js') }}"></script>
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
                                    console.log(result);
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

<body >
    <div id="app" style="">
            <nav class="navbar navbar-expand-md navbar-dark shadow-sm colorMorado">
                <div class="container">
                    <a class="navbar-brand" href="{{ route('home') }}" title="D">
                        <img src="{{ asset('img/logoWhite.png') }}"
                            style="max-height: 45px; margin-left:10px; margin-right:10px; pointer-events: none !important;"
                            alt="logo">

                        <b>Titulo</b>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <!-- Left Side Of Navbar -->
                        <ul class="navbar-nav me-auto">
                            <li> <a href="{{ route('logout') }}">logout</a></li>

                        </ul>
                        <!-- Right Side Of Navbar -->
                    </div>
                </div>
            </nav>
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
        <footer class="text-center text-lg-start text-white colorMorado footer fixed-bottom footerClassMain"
            style="">
            <div class="gobiernoDigitalDIV" style=""></div>
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


</html>
