@extends('configuracion.index')

@section('content_configuraciones')
    <!--Tabla de resultados-->
    <div class="container w-100 mt-4 p-4">
        <h5 style="text-align: left; font-weight: bold;">{{ __('messages.funciones') }}</h5>
        <form action="{{ route('get_funciones') }}" id="buscarForm" method="post">
            @csrf
            <div class="row">
                <div class="col-sm-1"></div>
                <div class="col-sm-2"></div>
                <div class="col-sm-6"></div>
                <div class="col-sm-3"></div>
            </div>
        </form>
        <br>
        <button type="button" id="btn_new_registro" class="btn" style="color:#0d6efd"><i class="fas fa-plus">
                {{ __('messages.nuevo_registro') }}</i></a></button>
        <div class="row">
            <div class="col-sm-12">
                <table id="catalogo" class="table table-striped table-bordered text-center " style="width:100%">
                    <thead>
                        <tr class="colorMorado">
                            <th>{{ __('messages.funcion') }}</th>
                            <th>{{ __('messages.ruta') }}</th>
                            <th>{{ __('messages.icono') }}</th>
                            <th>{{ __('messages.modulo') }}</th>
                            <th>{{ __('messages.acciones') }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @isset($dataSet)
        @include('panels.datatable')
    @endisset

    <script type="text/javascript">
        //inicializamos el data table
        let datatable;
        let acciones = [];
        let actions_delete = [];
        $(document).ready(function() {
            getData();

            datatable = $("#datatable").DataTable({
                paging: false,
                scrollCollapse: true,
                searching: false,
                autoWidth: false,
                info: false,
                ordering: false,
                language: {
                    processing: "Procesando...",
                    lengthMenu: "Mostrar _MENU_ registros",
                    zeroRecords: "No se encontraron resultados",
                    emptyTable: "Ningún dato disponible en esta tabla",
                    info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                    infoFiltered: "(filtrado de un total de _MAX_ registros)",
                    search: "Búsqueda:",
                    infoThousands: ",",
                    loadingRecords: "Cargando...",
                    buttonText: "Imprimir",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior",
                    },
                    buttons: {
                        copyTitle: 'Copiado al portapapeles',
                        copySuccess: {
                            _: '%d registros copiados',
                            1: 'Se copio un registro'
                        }
                    },
                },
            });

            $(".container").on('click', '#btn_new_registro', function() {
                $.ajax({
                    url: "{{ route('get_modulos_sistema') }}",
                    type: "GET",
                    data: {},
                    dataType: 'json',
                    success: function(response) {
                        $('#modalNuevoF').find("#modulo_id").find(".opt_mod_sis").remove();
                        $("#modalNuevoF").find("#staticBackdropLabel").html(
                            '{{ __('messages.alta_funciones') }}');
                        $.each(response.modulos_sistema, function(key, value) {
                            $('#modalNuevoF').find("#modulo_id").append(
                                '<option class="opt_mod_sis" id="mod_sis_' + value
                                .id + '" value="' + value.id + '">' + value
                                .modulo_padre + ' - ' + value.modulo + '</option>');
                        });
                        $('#modalNuevoF').modal('show');
                    },
                    error: function(response) {
                        console.log(response);
                    }
                });
            });

            $('#modalNuevoF').on('change', '.permisos', function(e) {
                accion = $('#modalNuevoF').find('#accion').val();
                descripcion = $('#modalNuevoF').find('#descripcion').val();
                if (accion != "" && descripcion != "") {
                    $('#modalNuevoF').find('#btn_agregar').removeAttr('disabled');
                } else {
                    $('#modalNuevoF').find('#btn_agregar').attr('disabled', 'disabled');
                }
            });

            $('#modalNuevoF').on('click', '#btn_agregar', function(e) {
                //obtenemos los datos del permiso
                accion = $('#modalNuevoF').find('#accion').val();
                descripcion = $('#modalNuevoF').find('#descripcion').val();
                //agregamos los datos al datatable
                datatable.row.add([accion, descripcion,
                    '<button type="button" class="btn btn-sm btn-danger btn_eliminar"><i class="fas fa-trash"></i></button>'
                ]).draw(false);
                //lo agregamos a la lista de acciones
                acciones.push({
                    'accion': accion,
                    'descripcion': descripcion
                });
                //en caso de que el elemento agregado coincida con uno eliminado anteriormente, lo quitamos de la lista de eliminados
                index = actions_delete.findIndex(e => e.accion === accion);
                removedAction = actions_delete.splice(index, index + 1);
                //vaciamos campos y asignamos los valores en el campo de acciones
                $('#modalNuevoF').find('#acciones').val(JSON.stringify({
                    'acciones': acciones
                }));
                $('#modalNuevoF').find('#acciones_delete').val(JSON.stringify({
                    'acciones': actions_delete
                }));
                $('#modalNuevoF').find('#accion').val("");
                $('#modalNuevoF').find('#descripcion').val("");
                $('#modalNuevoF').find('#btn_agregar').attr('disabled', 'disabled');
            });

            $('#modalNuevoF').on('click', '.btn_eliminar', function(e) {
                //obtenemos el permiso eliminado
                permiso = $(this).parents('tr').find('td').first().html();
                index = acciones.findIndex(e => e.accion === permiso);
                //borramos el permiso de la lista de acciones
                removedAction = acciones.splice(index, index + 1);
                //lo agregamos a la lista de permisos eliminados
                actions_delete.push(removedAction[0]);
                //borramos el permiso del datatable
                datatable.row($(this).parents('tr')).remove().draw();
                //actualizamos los campos de acciones
                $('#modalNuevoF').find('#acciones').val(JSON.stringify({
                    'acciones': acciones
                }));
                $('#modalNuevoF').find('#acciones_delete').val(JSON.stringify({
                    'acciones': actions_delete
                }));
            });

            $("#catalogo").on('click', '.btn_editar', function() {
                $.ajax({
                    url: $(this).attr('data-route'),
                    type: "GET",
                    data: {},
                    dataType: 'json',
                    success: function(response) {
                        $("#modalNuevoF").find("#staticBackdropLabel").html(
                            '{{ __('messages.edicion_funciones') }}');
                        $('#modalNuevoF').find("#formRegistro").append(
                            '<input type="hidden" id="id" name="id" class="form-control">');
                        $('#modalNuevoF').find("#modulo_id").find(".opt_mod_sis").remove();
                        $.each(response.modulos_sistema, function(key, value) {
                            $('#modalNuevoF').find("#modulo_id").append(
                                '<option class="opt_mod_sis" id="mod_sis_' + value
                                .id + '" value="' + value.id + '">' + value
                                .modulo_padre + ' - ' + value.modulo + '</option>');
                        });
                        $.each(response.objEditar, function(key, value) {
                            $('#modalNuevoF').find("#" + key).val(value);
                        });
                        acciones = JSON.parse($('#modalNuevoF').find("#acciones").val())
                            .acciones;
                        $.each(acciones, function(key, value) {
                            datatable.row.add([value.accion, value.descripcion,
                                '<button type="button" class="btn btn-sm btn-danger btn_eliminar"><i class="fas fa-trash"></i></button>'
                            ]).draw(false);
                        });
                        $('#modalNuevoF').modal('show');
                    },
                    error: function(response) {
                        console.log(response);
                    }
                });
            });

            $("#catalogo").on('click', '.btn_delete', function() {
                form_url = $(this).attr('data-route');
                $('#modal_delete').find("#message_revert").remove();
                $('#modal_delete').find("#message_confirm").remove();
                $('#modal_delete').find("#table_data").remove();
                $('#modal_delete').find("#staticBackdropLabel").html(
                    "{{ __('messages.eliminar_funcion') }}");
                $('#modal_delete').find("#modal_message").append(
                    '<div class="col-md-12" id="message_confirm">' +
                    '<p>' +
                    '{{ __('messages.msg_eliminar_funcion') }}<br/>' +
                    '{{ __('messages.msg_desea_continuar') }}' +
                    '</p>' +
                    '</div>');
                $('#modal_delete').find("#form_modal_delete").attr('action', form_url);
                $('#modal_delete').modal('show');
            });

            $("#modal_delete").on('click', '#confirmDelete', function(e) {
                e.preventDefault();
                form = $(this).closest('#form_modal_delete');
                $.ajax({
                    url: form.attr('action'),
                    type: "GET",
                    data: {},
                    dataType: 'json',
                    beforeSend: function() {
                        let timerInterval
                        Swal.fire({
                            title: '{{ __('messages.msg_guardando_datos') }}',
                            html: ' <b></b>',
                            allowOutsideClick: false,
                            timer: 2000000,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });
                    },
                    success: function(response) {
                        getData();
                        Swal.close();
                        Swal.fire({
                            icon: response.status,
                            title: response.title,
                            text: response.message,
                            confirmButtonText: "{{ __('messages.aceptar') }}",
                        });
                        $("#modal_delete").modal('toggle');
                    },
                    error: function(response) {
                        console.log(response);
                    }
                });
            });

            $("#modalNuevoF").on('click', '#btn_guardar', function(e) {
                e.preventDefault();
                form = $(this).closest('#formRegistro');
                $.ajax({
                    url: form.attr('action'),
                    type: "POST",
                    data: form.serializeArray(),
                    dataType: 'json',
                    beforeSend: function() {
                        let timerInterval
                        Swal.fire({
                            title: '{{ __('messages.msg_guardando_datos') }}',
                            html: ' <b></b>',
                            allowOutsideClick: false,
                            timer: 2000000,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });
                    },
                    success: function(response) {
                        getData();
                        Swal.close();
                        Swal.fire({
                            icon: response.status,
                            title: response.title,
                            text: response.message,
                            confirmButtonText: "{{ __('messages.aceptar') }}",
                        });
                        limpiarCampos();
                        $("#modalNuevoF").modal('toggle');
                    },
                    error: function(response) {
                        mensage = response.message;
                        $.each(response.responseJSON.errors, function(key, value) {
                            mensaje =
                                'Hubo un error, no se pudo registrar el módulo. Debe revisar que los datos capturados sean válidos.'
                            $("#modalNuevoF").find("#" + key).addClass('is-invalid');
                            $("#modalNuevoF").find("#" + key + '_error').addClass(
                                'd-block');
                            $("#modalNuevoF").find("#" + key + '_error').html(
                                '<strong>' + value + '</strong>');
                        });
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('messages.error') }}',
                            text: mensage,
                            confirmButtonText: "{{ __('messages.aceptar') }}",
                        });
                    }
                });
            });
        });

        function limpiarCampos() {
            $("#modalNuevoF").find('#funcion').val("").removeClass('is-invalid');
            $("#modalNuevoF").find('#ruta').val("").removeClass('is-invalid');
            $("#modalNuevoF").find('#icono').val("").removeClass('is-invalid');
            $("#modalNuevoF").find('#acciones').val("");
            $("#modalNuevoF").find('#acciones_delete').val("");
            $("#modalNuevoF").find('#modulo_id').val("").removeClass('is-invalid');

            $("#modalNuevoF").find('#funcion_error').removeClass('d-block').attr('style', 'display: none;');
            $("#modalNuevoF").find('#ruta_error').removeClass('d-block').attr('style', 'display: none;');
            $("#modalNuevoF").find('#icono_error').removeClass('d-block').attr('style', 'display: none;');
            $("#modalNuevoF").find('#modulo_id_error').removeClass('d-block').attr('style', 'display: none;');

            $("#modalNuevoF").find("#id").remove();
            datatable.rows().remove().draw();
            acciones = [];
            actions_delete = [];
        }
    </script>
@endsection
