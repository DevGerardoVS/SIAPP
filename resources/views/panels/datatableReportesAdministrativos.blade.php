    {{--datatableReportesAdministrativos--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#genericDataTable').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.12.0/i18n/es-MX.json"
                },
                searching: true,
                ordering: true,
                pageLength: 10,
                dom: 'frltip',
                scrollX: true,
                "lengthMenu": [10, 25, 50, 75, 100, 150, 200],
            });
        });
    </script>

    <style>
        .custom-select{
            min-width: 4em;
        }
    </style>
