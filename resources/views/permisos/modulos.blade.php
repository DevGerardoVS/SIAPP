@extends('configuracion.index')

@section('content_configuraciones')
    <!--Tabla de resultados-->
    <div class="container w-100 mt-4 p-4">
        <h5 style="text-align: left; font-weight: bold;">{{ __('messages.modulos') }}</h5>
        <form action="{{ route('get_modulos') }}" id="buscarForm" method="post">
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
                            <th>{{ __('messages.ruta') }}</th>
                            <th>{{ __('messages.icono') }}</th>
                            <th>{{ __('messages.tipo') }}</th>
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

            $(".container").on('click', '#btn_new_registro', function() {
                $("#modalNuevoM").find("#lbl_modulo_id").attr('style', 'display:none;');
                $("#modalNuevoM").find("#modulo_id").attr('style', 'display:none;');
                $("#modalNuevoM").find("#staticBackdropLabel").html('{{ __('messages.alta_modulos') }}');
                $('#modalNuevoM').modal('show');
            });

            $("#modalNuevoM").on('change', '#tipo', function(e) {
                if ($(this).val() == "sub") {
                    var id = $('#modalNuevoM').find("#id").val();
                    $('#modalNuevoM').find("#ruta").attr('readonly', 'readonly');
                    $.ajax({
                        url: "{{ route('get_modulos_padre') }}",
                        type: "GET",
                        data: {
                            'id': id == 'undefined' ? '' : id
                        },
                        dataType: 'json',
                        success: function(response) {
                            $('#modalNuevoM').find("#modulo_id").find(".opt_mod_padre")
                                .remove();
                            $.each(response.modulos_padre, function(key, value) {
                                $('#modalNuevoM').find("#modulo_id").append(
                                    '<option class="opt_mod_padre" id="mod_padre_' +
                                    value.id + '" value="' + value.id + '">' + value
                                    .modulo + '</option>');
                            });
                            $("#modalNuevoM").find("#lbl_modulo_id").attr('style',
                                'display:block;');
                            $("#modalNuevoM").find("#modulo_id").attr('style',
                                'display:block;');
                            if ($("#modalNuevoM").find("#modulo_id").hasClass('is-invalid')) {
                                $("#modalNuevoM").find('#modulo_id_error').addClass('d-block')
                                    .attr('style', 'display: block;');
                            }
                        },
                        error: function(response) {
                            console.log(response);
                        }
                    });
                } else {
                    $("#modalNuevoM").find("#lbl_modulo_id").attr('style', 'display:none;');
                    $("#modalNuevoM").find("#modulo_id").attr('style', 'display:none;');
                    $("#modalNuevoM").find('#modulo_id_error').removeClass('d-block').attr('style',
                        'display: none;');
                    $('#modalNuevoM').find("#ruta").removeAttr('readonly');
                }
            });

            $("#catalogo").on('click', '.btn_editar', function() {
                $.ajax({
                    url: $(this).attr('data-route'),
                    type: "GET",
                    data: {},
                    dataType: 'json',
                    success: function(response) {
                        $("#modalNuevoM").find("#staticBackdropLabel").html(
                            '{{ __('messages.edicion_modulos') }}');
                        $('#modalNuevoM').find("#formRegistro").append(
                            '<input type="hidden" id="id" name="id" class="form-control">');
                        $('#modalNuevoM').find("#modulo_id").find(".opt_mod_padre").remove();
                        $("#modalNuevoM").find("#lbl_modulo_id").attr('style', 'display:none;');
                        $("#modalNuevoM").find("#modulo_id").attr('style', 'display:none;');
                        $.each(response.modulos_padre, function(key, value) {
                            $('#modalNuevoM').find("#modulo_id").append(
                                '<option class="opt_mod_padre" id="mod_padre_' +
                                value.id + '" value="' + value.id + '">' + value
                                .modulo + '</option>');
                        });
                        $.each(response.objEditar, function(key, value) {
                            if (key == 'tipo' && value == 'sub') {
                                $('#modalNuevoM').find("#ruta").attr('readonly',
                                    'readonly');
                                $("#modalNuevoM").find("#lbl_modulo_id").attr('style',
                                    'display:block;');
                                $("#modalNuevoM").find("#modulo_id").attr('style',
                                    'display:block;');
                            }
                            $('#modalNuevoM').find("#" + key).val(value);
                        });
                        $('#modalNuevoM').modal('show');
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
                    "{{ __('messages.eliminar_modulo') }}");
                $('#modal_delete').find("#modal_message").append(
                    '<div class="col-md-12" id="message_confirm">' +
                    '<p>' +
                    '{{ __('messages.msg_eliminar_modulo') }}<br/>' +
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

            $("#modalNuevoM").on('click', '#btn_guardar', function(e) {
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
                        $("#modalNuevoM").modal('toggle');
                    },
                    error: function(response) {
                        mensage = response.message;
                        $.each(response.responseJSON.errors, function(key, value) {
                            mensaje =
                                'Hubo un error, no se pudo registrar el módulo. Debe revisar que los datos capturados sean válidos.'
                            $("#modalNuevoM").find("#" + key).addClass('is-invalid');
                            $("#modalNuevoM").find("#" + key + '_error').addClass(
                                'd-block');
                            $("#modalNuevoM").find("#" + key + '_error').html(
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
            $("#modalNuevoM").find('#modulo').val("").removeClass('is-invalid');
            $("#modalNuevoM").find('#ruta').val("").removeClass('is-invalid');
            $("#modalNuevoM").find('#icono').val("").removeClass('is-invalid');
            $("#modalNuevoM").find('#tipo').val("").removeClass('is-invalid');
            $("#modalNuevoM").find('#modulo_id').val("").removeClass('is-invalid');

            $("#modalNuevoM").find('#modulo_error').removeClass('d-block').attr('style', 'display: none;');
            $("#modalNuevoM").find('#ruta_error').removeClass('d-block').attr('style', 'display: none;');
            $("#modalNuevoM").find('#icono_error').removeClass('d-block').attr('style', 'display: none;');
            $("#modalNuevoM").find('#tipo_error').removeClass('d-block').attr('style', 'display: none;');
            $("#modalNuevoM").find('#modulo_id_error').removeClass('d-block').attr('style', 'display: none;');

            $("#modalNuevoM").find("#id").remove();
            $('#modalNuevoM').find("#ruta").removeAttr('readonly');
        }
    </script>
@endsection
