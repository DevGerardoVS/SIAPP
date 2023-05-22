@extends('layouts.app')
@section('content')
    <div class="container">
        @include('administracion.usuarios.modalCreate')
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
                                                <th class="th-administration">Administración</th>
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
        getData: function() {
            $.ajax({
                type: "GET",
                url: "administracion/usuarios/adm-usuarios/data",
                dataType: "json"
            }).done(function(_data) {
                $.each(_data, function(key, value) {
                    var accion =
                        '<a data-toggle="tooltip" title="Modificar Usuario" class="btn btn-sm btn-success" href="/adm-usuarios/update/' +
                        value.id + '">' +
                        '<i class="glyphicon glyphicon-pencil"></i></a>&nbsp;' +
                        '<a data-toggle="tooltip" title="Inhabilitar/Habilitar Usuario" class="btn btn-sm btn-warning" onclick="dao.setStatus(' +
                        value.id + ', ' + value.estatus + ')">' +
                        '<i class="glyphicon glyphicon-lock"></i></a>&nbsp;' +
                        '<a data-toggle="tooltip" title="Eliminar Usuario" class="btn btn-sm btn-danger" onclick="dao.eliminarUsuario(' +
                        value.id + ')">' +
                        '<i class="glyphicon glyphicon-trash"></i></a>&nbsp;';
                    $('#tbl-usuarios').append('<tr><th scope="row">' + value.username +
                        '</th> <th>  ' + value.email +
                        '</th> <th>' + value.celular +
                        '</th><th>' + value.perfil +
                        '</th><th>' + value.estatus +
                        '</th><th>' + value.id_grupo +
                        '</th></th><th>' + accion + '</th></tr>')

                });
            });
        },

        setStatus: function(id, estatus) {
            $.SmartMessageBox({
                title: 'Confirmar Activación/Desactivación',
                buttons: '[No][Continuar]',
            }, function(btn, input) {
                if (btn === "Continuar" && input != "") {
                    $.ajax({
                        type: "POST",
                        url: "/adm-usuarios/status",
                        data: {
                            id: id,
                            estatus: estatus
                        }
                    }).done(function(data) {
                        if (data != "done") {
                            _gen.notificacion_min('Error',
                                'Hubo un problema al querer realizar la acción, contacte a soporte',
                                4);
                        } else {
                            _gen.notificacion_min('Éxito',
                                'La acción se ha realizado correctamente', 1);
                            dao.getData();
                        }
                    });
                }
            });
        },

        eliminarUsuario: function(id) {
            $.SmartMessageBox({
                title: 'Confirmar Eliminación',
                buttons: '[No][Continuar]',
            }, function(btn, input) {
                if (btn === "Continuar" && input != "") {
                    $.ajax({
                        type: "POST",
                        url: "/adm-usuarios/eliminar",
                        data: {
                            id: id
                        }
                    }).done(function(data) {
                        if (data != "done") {
                            _gen.notificacion_min('Error',
                                'Hubo un problema al querer realizar la acción, contacte a soporte',
                                4);
                        } else {
                            _gen.notificacion_min('Éxito',
                                'La acción se ha realizado correctamente', 1);
                            dao.getData();
                        }
                    });
                }
            });
        },



        getPerfil: function(id) {
            $.ajax({
                type: "GET",
                url: '/adm-grupos/data',
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
                })
                dao.limpiarFormularioCrear();
            }).fail(function(response) {
                const {
                    errors
                } = response.responseJSON;
                console.log("responseJSON", errors);
                for (const key in errors) {
                    if (Object.hasOwnProperty.call(errors, key)) {
                        const element = errors[key];
                        $(`#error_${key}`).text(element[0]).addClass('has-error').addClass('d-block');
                        $(`#error_${key}`).addClass('is-invalid');
                        $(`#${key}`).addClass('is-invalid').addClass('d-block');
                    }
                }


            });
        },

        editarUsuario: function() {
            var form = $('#frm_update')[0];
            var data = new FormData(form);
            $.ajax({
                type: "POST",
                url: '/adm-usuarios/put-usuario',
                data: data,
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000
            }).done(function(response) {
                if (response == "done") {
                    _gen.notificacion_min('Éxito', 'La acción se ha realizado correctamente', 1);
                    window.location.href = '/#/adm-usuarios';
                }
            });
        },

        limpiarFormularioCrear: function() {
            $('#in_username').val('');
            $('#in_pass').val('');
            $('#in_pass_conf').val('');
            $('#in_email').val('');
            $('#in_nombre').val('');
            $('#in_p_apellido').val('');
            $('#in_s_apellido').val('');
            $('#in_email').val('');
            $('#in_celular').val('');
            $("#id_grupo").find('option').remove();
            $(".has-error span").remove();
            $('.form-group').removeClass('is-invalid');
            dao.getPerfil();
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
        validarFormulario: function(form) {
            for (const key in form) {
                if (Object.hasOwnProperty.call(form, key)) {
                    const element = form[key];
                    console.log("element", element);
                    if (document.getElementById(element?.id)?.value == "") {
                        $(`#error_${element.name}`).text("Este campo es requerido").addClass('has-error')
                            .addClass('d-block');
                        $(`#error_${element.name}`).addClass('is-invalid');
                        $(`#${element.name}`).addClass('is-invalid').addClass('d-block');
                    }
                }
            }
            /*    form.forEach(element => {
                   if($(element?.id).val() == ""){

                   }
               }); */

        }


    };

    $(document).ready(function() {

        getData();
        dao.getPerfil();

        $('#btnSave').click(function(e) {
            e.preventDefault();
           /*  dao.validarFormulario($('#frm_create'));  */
            dao.crearUsuario();

            /*   console.log("Form", $('#frm_create'));
              if ($('#frm_create').valid()) {
                  dao.crearUsuario();
              } */
        });

        $('#btnUpdate').click(function(e) {
            e.preventDefault();
            if ($('#frm_update').valid()) {
                dao.editarUsuario();
            }
        });

        /* $('#in_celular').mask('00-00-00-00-00'); */
    });
</script>
