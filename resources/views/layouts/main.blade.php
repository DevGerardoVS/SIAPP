<!DOCTYPE html>
<html lang="en-us" class="smart-style-0">
    <head>
        <meta charset="utf-8">
        <title>@yield('tittle')</title>
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="csrf-token" content="{{{ csrf_token() }}}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <link rel="stylesheet" type="text/css" media="screen" href="/assets/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" media="screen" href="/assets/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" media="screen" href="/assets/css/smartadmin-production-plugins.min.css">
        <link rel="stylesheet" type="text/css" media="screen" href="/assets/css/smartadmin-production.min.css">
        <link rel="stylesheet" type="text/css" media="screen" href="/assets/css/smartadmin-skins.min.css">
        <link rel="stylesheet" type="text/css" media="screen" href="/assets/css/smartadmin-rtl.min.css">
        <link rel="stylesheet" type="text/css" media="screen" href="/assets/css/demo.min.css">
        <link rel="stylesheet" type="text/css" media="screen" href="/assets/css/upbtn.css">
        <link rel="stylesheet" type="text/css" media="screen" href="/assets/css/your_style.css">
        <link rel="stylesheet" type="text/css" media="screen" href="/assets/css/bootstrap-datepicker.min.css">
        <link rel="stylesheet" type="text/css" media="screen" href="/assets/css/ion.rangeSlider.css">
        <link rel="stylesheet" type="text/css" media="screen" href="/assets/css/ion.rangeSlider.skinFlat.css">
        <link rel="stylesheet" type="text/css" media="screen" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
        <link rel="shortcut icon" href="/assets/img/favicon/favicon.png" type="image/x-icon">
        <link rel="icon" href="/assets/img/favicon/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">
        <link rel="apple-touch-icon" href="/assets/img/splash/sptouch-icon-iphone.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/assets/img/splash/touch-icon-ipad.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/assets/img/splash/touch-icon-iphone-retina.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/assets/img/splash/touch-icon-ipad-retina.png">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <link rel="apple-touch-startup-image" href="/assets/img/splash/ipad-landscape.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)">
        <link rel="apple-touch-startup-image" href="/assets/img/splash/ipad-portrait.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)">
        <link rel="apple-touch-startup-image" href="/assets/img/splash/iphone.png" media="screen and (max-device-width: 320px)">
        <style>
            .select2-hidden-accessible{
                display:none;
            }

                /* Start by setting display:none to make this hidden.
               Then we position it in relation to the viewport window
               with position:fixed. Width, height, top and left speak
               for themselves. Background we set to 80% white with
               our animation centered, and no-repeating */
            .modal {
                display:    none;
                position:   fixed;
                z-index:    1000;
                top:        0;
                left:       0;
                height:     100%;
                width:      100%;
                background: rgba( 255, 255, 255, .8 )
                            url('http://i.stack.imgur.com/FhHRx.gif')
                            50% 50%
                            no-repeat;
            }

            /* When the body has the loading class, we turn
               the scrollbar off with overflow:hidden */
            body.loading {
                overflow: hidden;
            }

            /* Anytime the body has the loading class, our
               modal element will be visible */
            body.loading .modal {
                display: block;
            }
        </style>
        <!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
        <script src="/assets/js/libs/jquery-2.1.1.min.js"></script>
        <script src="/assets/js/libs/jquery-ui.min.js"></script>

        <!-- BOOTSTRAP JS -->
        <script src="/assets/js/bootstrap/bootstrap.min.js"></script>
    </head>

    <body class="smart-style-8 desktop-detected fixed-header minified">
        <header id="header">
            <div id="logo-group">
                <span id="logo">@yield('logo')</span>
            </div>

            <div>
                <h1 style="color: white; font-weight: 600; font-size: 20px;">Sistema de Expedientes Digitales Jurídicos</h2>
            </div>

            <div class="pull-right">
                <div id="hide-menu" class="btn-header pull-right">
                    <span><a href="javascript:void(0);" data-action="toggleMenu" title="Ocultar Menu"><i class="fa fa-reorder"></i></a></span>
                </div>
                <div id="logout" class="btn-header transparent pull-right">
                    <span> <a href="/logout" title="Cerrar Sesion" data-action="userLogout" data-logout-msg="Recuerde siempre cerrar la sesion de su cuenta para evitar accesos indebidos."><i class="fa fa-sign-out"></i></a></span>
                </div>
                <div id="user" class="btn-header transparent pull-right">
                    <span> <a title="Perfil"><i class="fa fa-user"></i></a> </span>
                </div>
                <div id="fullscreen" class="btn-header transparent pull-right">
                    <span> <a href="javascript:void(0);" data-action="launchFullscreen" title="Pantalla Completa"><i class="fa fa-arrows-alt"></i></a> </span>
                </div>    
            </div>
        </header>

        @yield('main-content')
    
        <!--<div class="page-footer">
            © 2023 Dirección General de Gobierno Digital | Secretaría de Finanzas y Administración | Gobierno del Estado de Michoacán
        </div>-->

            <div class="page-footer">
                <!-- Grid container -->
                {{-- <div class="container pb-0"></div> --}}
                <!-- Grid container -->

                <!-- Copyright -->
                <div class="gobiernoDigitalDIV" style=""></div>

                <div class="text-center-specialF">
                    © {{date('Y')}} Dirección General de Gobierno Digital | Secretaría de Finanzas y Administración |
                    <a target="_blank" class="customFooterA" href="https://www.michoacan.gob.mx" style="color: white; text-decoration: underline;">
                        Gobierno del Estado de Michoacán
                    </a>
                </div>
                <!-- Copyright -->

                <div class="container pb-1"></div>
            </div>

        <!-- IMPORTANT: APP CONFIG -->
        <script src="/assets/js/app.config.js"></script>

        <!-- JS TOUCH : include this plugin for mobile drag / drop touch events-->
        <script src="/assets/js/plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script>

        <!-- CUSTOM NOTIFICATION -->
        <script src="/assets/js/notification/SmartNotification.min.js"></script>

        <!-- JARVIS WIDGETS -->
        <script src="/assets/js/smartwidgets/jarvis.widget.min.js"></script>

        <!-- EASY PIE CHARTS -->
        <script src="/assets/js/plugin/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>

        <!-- SPARKLINES -->
        <script src="/assets/js/plugin/sparkline/jquery.sparkline.min.js"></script>

        <!-- JQUERY VALIDATE -->
        <script src="/assets/js/plugin/jquery-validate/jquery.validate.min.js"></script>

        <!-- JQUERY MASKED INPUT -->
        <script src="/assets/js/plugin/masked-input/jquery.maskedinput.min.js"></script>

        <!-- JQUERY SELECT2 INPUT -->
        <script src="/assets/js/plugin/select2/select2.min.js"></script>

        <!-- JQUERY UI + Bootstrap Slider -->
        <script src="/assets/js/plugin/bootstrap-slider/bootstrap-slider.min.js"></script>

        <!-- browser msie issue fix -->
        <script src="/assets/js/plugin/msie-fix/jquery.mb.browser.min.js"></script>

        <!-- FastClick: For mobile devices: you can disable this in app.js -->
        <script src="/assets/js/plugin/fastclick/fastclick.min.js"></script>


        <!--[if IE 8]>
            <h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>
        <![endif]-->

        <script src="/assets/js/plugin/datatables/jquery.dataTables.min.js"></script>
        <script src="/assets/js/plugin/datatables/dataTables.colVis.min.js"></script>
        <script src="/assets/js/plugin/datatables/dataTables.tableTools.min.js"></script>
        <script src="/assets/js/plugin/datatables/dataTables.bootstrap.min.js"></script>
        <script src="/assets/js/plugin/datatables/dataTables.buttons.min.js"></script>
        <script src="/assets/js/plugin/datatable-responsive/datatables.responsive.min.js"></script>
        <script src="/assets/js/plugin/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
        <script src="/assets/js/plugin/bootstrap-datepicker/bootstrap-datepicker.es.min.js"></script>
        <script src="/assets/js/plugin/bootstrap-timepicker/bootstrap-timepicker.min.js"></script>
        <script src="/assets/js/plugin/password-generator/gpw.js"></script>
        <script src="/assets/js/plugin/superbox/superbox.min.js"></script>
        <script src="/assets/js/plugin/dropzone/dropzone.min.js"></script>
        <script src="/assets/js/plugin/bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
        <script src="/assets/js/plugin/fuelux/wizard/wizard.min.js"></script>
        <script src="/assets/js/plugin/ion-slider/ion.rangeSlider.min.js"></script>
        <script src="/assets/js/libs/jquery.mask.min.js"></script>
        <!-- MAIN APP JS FILE -->
        <script src="/assets/js/app.min.js"></script>

        <!--<script src="/js/administracion/init.js"></script>-->
        <script src="/js/utilerias.js"></script>
        
        <script type="text/javascript">
            pageSetUp();
        </script>
        @yield('script-content')
        
    </body>
</html>