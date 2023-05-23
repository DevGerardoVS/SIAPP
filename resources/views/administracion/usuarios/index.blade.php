@extends('layouts.app')
@section('content')
    @include('administracion.usuarios.modalCreate')
    <div class="container">

        <section id="widget-grid" class="conteiner">
            <div class="row">
                <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                    <div color="darken" class="jarviswidget" id="wid-id-1" data-widget-editbutton="false"
                        data-widget-colorbutton="false" data-widget-deletebutton="false">
                        <header>
                            <h2>Usuarios</h2>
                        </header>
                        <div>
                            <div class="jarviswidget-editbox">
                            </div>
                            <div class="widget-body-toolbar">
                                <div class="row">
                                    <div class="col-xs-9 col-sm-5 col-md-5 col-lg-5">
                                    </div>
                                    <div class="col-xs-3 col-sm-7 col-md-7 col-lg-7 text-right">
                                        <button type="button" class="btn btn-success" data-toggle="modal" id="btnNew"
                                            data-target=".bd-example-modal-lg">Agregar Usuario</button>
                                    </div>
                                </div>
                            </div>
                            <br><br>
                            <div class="widget-body no-padding ">
                                <div class="table-responsive ">
                                    <table id="catalogo" class="table table-hover table-striped ">
                                        <thead>
                                            <tr class="colorMorado">
                                                <th data-hide="phone">Nombre Usuario</th>
                                                <th data-hide="phone">Correo</th>
                                                <th data-hide="phone">Nombre Completo</th>
                                                <th data-hide="phone">Celular</th>
                                                <th data-hide="phone">Perfil</th>
                                                <th data-hide="phone">Estatus</th>
                                                <th class="th-administration">Acciones</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </section>
    </div>
    @include('panels.datatable')
@endsection
<script src="js/utilerias.js" type="text/javascript"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script type="text/javascript">
    var dao = {
        setStatus: function(id, estatus) {
            Swal.fire({
                title: 'Confirmar Activación/Desactivación',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirmar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "/adm-usuarios/status",
                        data: {
                            id: id,
                            estatus: estatus
                        }
                    }).done(function(data) {
                        if (data != "done") {
                            Swal.fire(
                                'Error!',
                                'Hubo un problema al querer realizar la acción, contacte a soporte',
                                'Error'
                            );
                        } else {
                            Swal.fire(
                                'Éxito!',
                                'La acción se ha realizado correctamente',
                                'success'
                            );
                        }
                    });
                }
            });
        },

        eliminarUsuario: function(id) {
            Swal.fire({
                title: '¿Seguro que quieres eliminar este usuario?',
                text: "Esta accion es irreversible",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Confirmar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "/adm-usuarios/eliminar",
                        data: {
                            id: id
                        }
                    }).done(function(data) {
                        if (data != "done") {
                            Swal.fire(
                                'Error!',
                                'Hubo un problema al querer realizar la acción, contacte a soporte',
                                'Error'
                            );
                        } else {
                            Swal.fire(
                                'Éxito!',
                                'La acción se ha realizado correctamente',
                                'success'
                            );
                        }
                    });

                }
            })



        },



        getPerfil: function(id) {
            $.ajax({
                type: "GET",
                url: 'administracion/usuarios/grupos',
                dataType: "JSON"
            }).done(function(data) {
                var par = $('#id_grupo');
                par.html('');
                par.append(new Option("-- Selecciona Perfil --", ""));
                document.getElementById("id_grupo").options[0].disabled = true;
                $.each(data, function(i, val) {

                    par.append(new Option(data[i].nombre_grupo, data[i].id));
                });
            });
        },

        crearUsuario: function() {
            var form = $('#frm_create')[0];
            var data = new FormData(form);
            $.ajax({
                type: "POST",
                url: 'administracion/usuarios/adm-usuarios/store',
                data: data,
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000
            }).done(function(response) {
                console.log("response", response);
                Swal.fire({
                    icon: 'success',
                    title: 'Your work has been saved',
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#exampleModal').modal('hide');
                dao.limpiarFormularioCrear();
            });
        },

        editarUsuario: function(id) {
            $('#exampleModal').modal('show');
            $.ajax({
                type: "GET",
                url: 'administracion/usuarios/adm-usuarios/update/' + id,
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000
            }).done(function(response) {
                const {
                    id,
                    username,
                    celular,
                    email,
                    estatus,
                    id_grupo,
                    nombre,
                    p_apellido,
                    s_apellido,
                    perfil
                } = response;
                $('#id_user').val(id);
                $('#in_username').val(username);
                $('#in_nombre').val(nombre);
                $('#in_p_apellido').val(p_apellido);
                $('#in_s_apellido').val(s_apellido);
                $('#in_email').val(email);
                $('#in_celular').val(celular);
                $('#id_grupo').val(id_grupo);

            });
        },

        limpiarFormularioCrear: function() {
           
            inps = [
                'id_user',
                'in_username',
                'in_nombre',
                'in_p_apellido',
                'in_s_apellido',
                'in_email',
                'in_pass',
                'in_pass_conf',
                'in_celular',
                'id_grupo'
            ];
            inps.forEach(e => {
                $('#'+e).val('').removeClass('is-invalid').removeClass('d-block');
                $('#error_' + e).text("");
                $('#error_' + e).removeClass('is-invalid').removeClass('d-block');
                
            });
            $("#id_grupo").find('option').remove();
            dao.getPerfil();
            $('#exampleModal').modal('hide');
        },

        guardarGrupo: function(grupos, id) {
            $.ajax({
                type: "POST",
                url: '/adm-usuarios/grupos',
                data: {
                    id: id,
                    grupos: grupos
                }
            }).done(function(response) {
                if (response == "done") {
                    _gen.notificacion_min('Éxito', 'La acción se ha realizado correctamente', 1);
                } else if (response == "nada") {
                    _gen.notificacion_min('Advertencia', 'Nada que agregar', 3);
                }
            });
        },

        validarFormulario: function() {
            inps = [
                'in_username',
                'in_nombre',
                'in_p_apellido',
                'in_s_apellido',
                'in_email',
                'in_pass',
                'in_pass_conf',
                'in_celular'
            ];
            let bol = 0;
            inps.forEach(key => {
                if ($('#' + key).val() == "") {
                    $('#error_' + key).text("Este campo es requerido").addClass('has-error')
                        .addClass('d-block');
                    $('#error_' + key).addClass('is-invalid');
                    $('#' + key).addClass('is-invalid').addClass('d-block');
                    bol++;
                } else {
                    $('#error_' + key).text("").removeClass('has-error')
                        .removeClass('d-block');
                    $('#error_' + key).removeClass('is-invalid');
                    $('#' + key).removeClass('is-invalid').removeClass('d-block');
                }
            });
            if ($('select[name="id_grupo"] option:selected').text() == "" || $(
                    'select[name="id_grupo"] option:selected').text() == '-- Selecciona Perfil --') {
                bol++;
                $('#error_id_grupo').text("Este campo es requerido").addClass('has-error')
                    .addClass('d-block');
                $('#error_id_grupo').addClass('is-invalid');
                $('#id_grupo').addClass('is-invalid').addClass('d-block');
            }

            if (bol != 0) {
                return false;
            } else {
                return true;
            }
        }


    };

    $(document).ready(function() {

        getData();
        dao.getPerfil();

        $('#btnSave').click(function(e) {
            e.preventDefault();
            if (dao.validarFormulario()) {
                dao.crearUsuario();
            }
        });

        $("#id_grupo").change(function() {
            if ($('select[name="id_grupo"] option:selected').text() == "" || $(
                    'select[name="id_grupo"] option:selected').text() == '-- Selecciona Perfil --')
            {
                $('#error_id_grupo').text("Este campo es requerido").addClass('has-error')
                    .addClass('d-block');
                $('#error_id_grupo').addClass('is-invalid');
                $('#id_grupo').addClass('is-invalid').addClass('d-block');
            }else{
                $('#error_id_grupo').text("").removeClass('has-error')
                    .removeClass('d-block');
                $('#error_id_grupo').removeClass('is-invalid');
                $('#id_grupo').removeClass('is-invalid').removeClass('d-block');

            }
        });

        $("#in_pass_conf").change(function() {
            if ($("#in_pass_conf").val() != $("#in_pass").val()) {
                $('#error_in_pass_conf').text("Las contraseñas no coinciden");
                $('#error_in_pass').text("Las contraseñas no coinciden");
                $('#error_in_pass_conf').addClass('is-invalid');
                $("#in_pass").addClass('is-invalid').addClass('d-block');
                $("#in_pass_conf").addClass('is-invalid').addClass('d-block');
            } else {
                $('#error_in_pass_conf').text("");
                $('#error_in_pass').text("");
                $('#error_in_pass_conf').removeClass('is-invalid');
                $("#in_pass").removeClass('is-invalid').removeClass('d-block');
                $("#in_pass_conf").removeClass('is-invalid').removeClass('d-block');
            }

        });
        $("#in_pass").change(function() {
         if($("#in_pass").val() !=''){
               if ($("#in_pass_conf").val() != $("#in_pass").val()) {
                $('#error_in_pass_conf').text("Las contraseñas no coinciden");
                $('#error_in_pass').text("Las contraseñas no coinciden");
                $('#error_in_pass_conf').addClass('is-invalid');
                $("#in_pass").addClass('is-invalid').addClass('d-block');
                $("#in_pass_conf").addClass('is-invalid').addClass('d-block');
            } else {
                $('#error_in_pass_conf').text("");
                $('#error_in_pass').text("");
                $('#error_in_pass_conf').removeClass('is-invalid');
                $("#in_pass").removeClass('is-invalid').removeClass('d-block');
                $("#in_pass_conf").removeClass('is-invalid').removeClass('d-block');
            }}

        });
        
        /* $('#in_celular').mask('00-00-00-00-00'); */
    });
</script>
