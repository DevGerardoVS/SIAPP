@extends('configuracion.index')

@section('content_configuraciones')
    <!--Tabla de resultados-->
    <div class="container w-100 mt-4 p-4">
        <h5 style="text-align: left; font-weight: bold;">{{ __('messages.perfiles') }}</h5>
        <form action="{{ route('get_perfiles') }}" id="buscarForm" method="post">
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
                            <th>{{ __('messages.nombre') }}</th>
                            <th>{{ __('messages.tipo_perfil') }}</th>
                            <th>{{ __('messages.estatus') }}</th>
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
        $(document).ready(function() {
            getData();

            $("#catalogo").on('click', '.btn_editar', function() {
                $.ajax({
                    url: $(this).attr('data-route'),
                    type: "GET",
                    data: {},
                    dataType: 'json',
                    success: function(response) {
                        $('#modalNuevoP').find(".ocultar").hide();
                        $("#modalNuevoP").find("#staticBackdropLabel").html(
                            '{{ __('messages.edicion_perfiles') }}');
                        $('#modalNuevoP').find("#formRegistro").append(
                            '<input type="hidden" id="id" name="id" class="form-control">');

                        $("#modalNuevoP").find('#permisos').multiselect({
                            includeSelectAllOption: true,
                            enableClickableOptGroups: true,
                            nonSelectedText: '{{ __('messages.elige_opcion') }}',
                            allSelectedText: '{{ __('messages.todos_seleccionados') }}',
                            selectAllText: '{{ __('messages.elegir_todos') }}',
                            buttonWidth: '400px',
                            maxHeight: 200
                        });
                        $("#modalNuevoP").find('#permisos').multiselect('dataprovider', response
                            .query);

                        $.each(response.objEditar, function(key, value) {
                            if (key == 'permisos') {
                                $('#modalNuevoP').find("#" + key).val(JSON.parse(
                                value));
                                $('#modalNuevoP').find("#" + key).multiselect(
                                'refresh');
                            } else {
                                $('#modalNuevoP').find("#" + key).val(value);
                            }

                        });
                        $('#modalNuevoP').modal('show');
                    },
                    error: function(response) {
                        console.log(response);
                    }
                });
            });

            $(".container").on('click', '#btn_new_registro', function() {
                $.ajax({
                    url: "{{ route('get_permisos_sistema') }}",
                    type: "GET",
                    data: {},
                    dataType: 'json',
                    success: function(response) {
                        $('#modalNuevoP').find(".ocultar").hide();
                        $("#modalNuevoP").find("#staticBackdropLabel").html(
                            '{{ __('messages.alta_perfiles') }}');

                        $("#modalNuevoP").find('#permisos').multiselect({
                            includeSelectAllOption: true,
                            enableClickableOptGroups: true,
                            nonSelectedText: '{{ __('messages.elige_opcion') }}',
                            allSelectedText: '{{ __('messages.todos_seleccionados') }}',
                            selectAllText: '{{ __('messages.elegir_todos') }}',
                            buttonWidth: '400px',
                            maxHeight: 200
                        });
                        $("#modalNuevoP").find('#permisos').multiselect('dataprovider', response
                            .items);
                        $('#modalNuevoP').modal('show');
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
                    "{{ __('messages.eliminar_perfil') }}");
                $('#modal_delete').find("#modal_message").append(
                    '<div class="col-md-12" id="message_confirm">' +
                    '<p>' +
                    '{{ __('messages.msg_deshabilitar_perfil') }}<br/>' +
                    '{{ __('messages.msg_desea_continuar') }}' +
                    '</p>' +
                    '</div>');
                $('#modal_delete').find("#form_modal_delete").attr('action', form_url);
                $('#modal_delete').modal('show');
            });

            $("#catalogo").on('click', '.btn_enabled', function() {
                form_url = $(this).attr('data-route');
                $('#modal_delete').find("#message_revert").remove();
                $('#modal_delete').find("#message_confirm").remove();
                $('#modal_delete').find("#table_data").remove();
                $('#modal_delete').find("#staticBackdropLabel").html(
                    "{{ __('messages.habilitar_perfil') }}");
                $('#modal_delete').find("#modal_message").append(
                    '<div class="col-md-12" id="message_confirm">' +
                    '<p>' +
                    '{{ __('messages.msg_habilitar_perfil') }}<br/>' +
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

            $("#modalNuevoP").on('click', '#btn_guardar', function(e) {
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
                        $("#modalNuevoP").modal('toggle');
                    },
                    error: function(response) {
                        mensage = response.message;
                        $.each(response.responseJSON.errors, function(key, value) {
                            mensaje =
                                'Hubo un error, no se pudo registrar el perfil. Debe revisar que los datos capturados sean válidos.'
                            $("#modalNuevoP").find("#" + key).addClass('is-invalid');
                            $("#modalNuevoP").find("#" + key + '_error').addClass(
                                'd-block');
                            $("#modalNuevoP").find("#" + key + '_error').html(
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
            $("#modalNuevoP").find('#nombre').val("").removeClass('is-invalid');
            $("#modalNuevoP").find('#tipo_perfil').removeClass('is-invalid');
            $("#modalNuevoP").find('#permisos').val();
            $("#modalNuevoP").find('#permisos').multiselect('refresh');
            $("#modalNuevoP").find('#permisos').removeClass('is-invalid');

            $("#modalNuevoP").find('#perfil_error').removeClass('d-block').attr('style', 'display: none;');
            $("#modalNuevoP").find('#permisos_error').removeClass('d-block').attr('style', 'display: none;');

            $("#modalNuevoP").find("#id").remove();
        }
    </script>
@endsection
