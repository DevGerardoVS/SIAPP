    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <!-- Fonts -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset(mix('vendors/js/bootstrap/bootstrap.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/charts/chart.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/jquery/jquery.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/jquery/jquery.mask.min.js') }}"></script>
    <script src="{{ asset('vendors/js/jquery/jquery-3.7.0.js') }}"></script>
    <script src="{{ asset(mix('vendors/js/bootstrap/bootstrap.bundle.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/bootstrap/bootstrap-multiselect.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/datatable-responsive/datatables.responsive.min.js') }}"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.15.3/xlsx.full.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>
    <script src="{{ asset('vendors/js/tables/datatable/datatable-responsive/datatables.responsive.min.js') }}"></script>
    <!-- Latest compiled and minified CSS -->



    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

    <!-- (Optional) Latest compiled and minified JavaScript translation files -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-*.min.js"></script> --}}

    {{-- buttons --}}

    <script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
        integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="{{asset('vendors/js/jquery/jquery.validate.min.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
        integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    {{-- buttons --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap4.min.js')) }}"></script>
    <script src="{{ asset('vendor/sweetalert/sweetalert.all.js') }}"></script>
    <script src="/js/appInit.js"></script>
    <script >
        
          function callbackThen(response) {
            // read HTTP status
            // read Promise object
            response.json().then(function(data) {
                // console.log(data.action);
            });
        }
    
        function callbackCatch(error) {
            console.error('Error:', error)
        }
     
        var tiemporestante2 = new Date("{{Session::get('last_activity')}}");
        var cargamasiva="{{Session::get('status')??'3'}}";
 


       

       
        _gen.essential(tiemporestante2,cargamasiva);
    </script>
    <script>
        $(document).ready(function() {
            _gen.block();
            
        });
    </Script>
