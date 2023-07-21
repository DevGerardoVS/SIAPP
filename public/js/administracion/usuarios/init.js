let namePer = ['leer', 'escribir', 'editar', 'eliminar'];
var dao = {
    setStatus: function (id, estatus) {
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
                }).done(function (data) {
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
                        getData();
                    }
                });
            }
        });
    },
    eliminarUsuario: function (id) {
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
                }).done(function (data) {
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
                        getData();
                    }
                });

            }
        });
    },
    getPerfil: function () {
        $.ajax({
            type: "GET",
            url: 'grupos',
            dataType: "JSON"
        }).done(function (data) {
            var par = $('#id_grupo');
            par.html('');
            par.append(new Option("-- Selecciona Grupo --", ""));
            document.getElementById("id_grupo").options[0].disabled = true;
            $.each(data, function (i, val) {
                par.append(new Option(val.nombre_grupo, val.id));
            });
        });
    },
    getUsers: function () {
        $.ajax({
            type: "GET",
            url: '/users/permissos',
            dataType: "JSON"
        }).done(function (data) {
            var par = $('#id_userP');
            par.html('');
            par.append(new Option("-- Selecciona usuario --", ""));
            document.getElementById("id_userP").options[0].disabled = true;
            $.each(data, function (i, val) {
                par.append(new Option(val.username, val.id));
            });
        });
    },
    getPermisos: function () {
        $.ajax({
            type: "GET",
            url: '/users/permissos/get',
            dataType: "JSON"
        }).done(function (data) {
            var par = $('#id_permiso');
            par.html('');
            par.append(new Option("-- Selecciona usuario --", ""));
            document.getElementById("id_permiso").options[0].disabled = true;
            $.each(data, function (i, val) {
                par.append(new Option(val.nombre, val.id));
            });
        });
    },
    getUpp: function () {
        $.ajax({
            type: "GET",
            url: '/upp/get',
            dataType: "JSON"
        }).done(function (data) {
            var par = $('#clv_upp');
            par.html('');
            par.append(new Option("-- Selecciona Grupo --", ""));
            document.getElementById("clv_upp").options[0].disabled = true;
            $.each(data, function (i, val) {
                par.append(new Option(`${val.clave} - ${val.descripcion}`, val.clave));
            });
        });
    },
    crearUsuario: function () {
        var form = $('#frm_create')[0];
        var data = new FormData(form);
        if ($("#id_grupo").val() != '4') {
            data.append('clv_upp', null);
        }

        $.ajax({
            type: "POST",
            url: 'adm-usuarios/store',
            data: data,
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000
        }).done(function (response) {
            $('#cerrar').trigger('click');
            Swal.fire({
                icon: response.icon,
                title: response.title,
                showConfirmButton: false,
                timer: 1500
            });

            dao.limpiarFormularioCrear();
            getData();
        });
    },
    assignPermisos: function () {
        var form = $('#frm_permisos')[0];
        var data = new FormData(form);
        $.ajax({
            type: "POST",
            url: '/users/permissos/assign',
            data: data,
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000
        }).done(function (response) {
            $('#cerrar').trigger('click');
            Swal.fire({
                icon: 'success',
                title: 'Your work has been saved',
                showConfirmButton: false,
                timer: 1500
            });
        });
    },
    crearPermisos: function () {
        var form = $('#permisos_frm')[0];
        var data = new FormData(form);
        $.ajax({
            type: "POST",
            url: '/users/permissos/create',
            data: data,
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000
        }).done(function (response) {
            $('#cerrar').trigger('click');
            dao.getPermisos();
            Swal.fire({
                icon: 'success',
                title: 'Your work has been saved',
                showConfirmButton: false,
                timer: 1500
            });
        });
    },
    editarUsuario: function (id) {
        $.ajax({
            type: "GET",
            url: 'adm-usuarios/update/' + id,
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000
        }).done(function (response) {
            console.log("response", response)
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
                perfil,
                nombre_grupo
            } = response;
            $('#id_user').val(id);
            $('#username').val(username);
            $('#nombre').val(nombre);
            $('#p_apellido').val(p_apellido);
            $('#s_apellido').val(s_apellido);
            $('#email').val(email);
            $('#in_celular').val(celular);
            $('#label_idGrupo').text(perfil).show();

            $("#id_grupo").hide();
            $("#labelGrupo").hide();

        });
    },
    limpiarFormularioCrear: function () {

        inps = [
            'id_user',
            'username',
            'nombre',
            'p_apellido',
            's_apellido',
            'email',
            'password',
            'in_pass_conf',
            'in_celular',
            'id_grupo',
            'clv_upp',
            'id_permiso',
            'id_userP'
        ];
        inps.forEach(e => {
            $('#' + e).val('').removeClass('has-error').removeClass('d-block');
            $('#' + e + '-error').text("").removeClass('has-error').removeClass('d-block');

        });
        $("#id_grupo").find('option').remove();
        $("#clv_upp").find('option').remove();
        $('#divUpp').hide();
        dao.getPerfil();
        dao.getUsers();
        dao.getPermisos();
        $('.form-group').removeClass('has-error');
        $("#id_grupo").show();
        $("#labelGrupo").show();
        $("#label_idGrupo").text("").hide();

    },
};
var init = {
    validateCreate: function (form) {

        let rm =
        {
            rules: {
                username: { required: true },
                nombre: { required: true },
                p_apellido: { required: true },
                s_apellido: { required: true },
                email: { required: true, email: true },
                password: { required: true },
                in_pass_conf: { required: true, equalTo: "#password" },
                in_celular: {
                    required: true,
                    phoneUS: true
                },
                id_grupo: { required: true }

            },
            messages: {
                username: { required: "Este campo es requerido" },
                nombre: { required: "Este campo es requerido" },
                p_apellido: { required: "Este campo es requerido" },
                s_apellido: { required: "Este campo es requerido" },
                email: { required: "Este campo es requerido" },
                password: { required: "Este campo es requerido" },
                in_pass_conf: { required: "Este campo es requerido" },
                in_celular: { required: "Este campo es requerido" },
                id_grupo: { required: "Este campo es requerido" }
            }
        }
        _gen.validate(form, rm);

    },
    valUpp: function (form) {

        let rmUPP =
        {
            rules: {
                username: { required: true },
                nombre: { required: true },
                p_apellido: { required: true },
                s_apellido: { required: true },
                email: { required: true, email: true },
                password: { required: true },
                in_pass_conf: { required: true, equalTo: "#password" },
                in_celular: {
                    required: true,
                    phoneUS: true
                },
                id_grupo: { required: true },
                clv_upp: { required: true }

            },
            messages: {
                username: { required: "Este campo es requerido" },
                nombre: { required: "Este campo es requerido" },
                p_apellido: { required: "Este campo es requerido" },
                s_apellido: { required: "Este campo es requerido" },
                email: { required: "Este campo es requerido" },
                password: { required: "Este campo es requerido" },
                in_pass_conf: { required: "Este campo es requerido" },
                in_celular: { required: "Este campo es requerido" },
                id_grupo: { required: "Este campo es requerido" },
                clv_upp: { required: "Este campo es requerido" }

            }
        }
        _gen.validate(form, rmUPP);

    },
    validatePermiso: function (form) {

        let rm =
        {
            rules: {
                id_userP: { required: true },
                id_permiso: { required: true },
                descripcion: { required: false }
            },
            messages: {
                id_userP: { required: "Este campo es requerido" },
                id_permiso: { required: "Este campo es requerido" },
                descripcion: { required: "Este campo es requerido" }
            }
        }
        _gen.validate(form, rm);

    },
};

$(document).ready(function () {
    getData();
    init.validatePermiso($('#frm_permisos'));
    dao.getPerfil();
    dao.getUsers();
    dao.getPermisos();
    $("#id_grupo").change(function () {
        if (this.value == '4') {
            $('#divUpp').show();
            dao.getUpp();
            init.valUpp($('#frm_create'));
        } else {
            $('#divUpp').hide();
            $("#clv_upp option[value='']").attr("selected", true);
            init.validateCreate($('#frm_create'));
        }
    });

    $('#exampleModal').modal({
        backdrop: 'static',
        keyboard: false
    })
    $('#btnSave').click(function () {
        init.validateCreate($('#frm_create'));
        let selectValue = $("#id_grupo").find("option:selected").val();
        if (selectValue != 4) {
            init.validateCreate($('#frm_create'));
            if ($('#frm_create').valid()) {
                dao.crearUsuario();
            }
        }
        if (selectValue == 4) {
                if ($("#clv_upp").val() == null) {
                init.valUpp($('#frm_create'));
                if ($('#frm_create').valid()) {
                    dao.crearUsuario();
                }
            } else {
                $('#clv_upp-error').text("Este campo es requerido").addClass('has-error');
            }
            
        }
        $('#email-error').text("Este campo es requerido").addClass('has-error');
        $('#in_celular-error').text("Este campo es requerido").addClass('has-error');
        $('#clv_upp-error').text("Este campo es requerido").addClass('has-error');
    });
    $('#btnSaveP').click(function (e) {
        if ($('#frm_permisos').valid()) {
            dao.assignPermisos();
        }

    });
/*funcion para agregar permisos al catalogo     
$('#btnSaveCreate').click(function (e) {
        dao.crearPermisos();

    }); */
    $("#email").change(function () {
        var regex = /^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
        regex.test($("#email").val());
        var text = "Ingresa un correo electrónico válido";
        if (regex.test($("#email").val())) {
            $('#email-error').text("").removeClass('d-block').removeClass('has-error');
            $('#email').removeClass('has-error').removeClass('d-block');
        } else {
            $('#email-error').text(text).addClass('d-block').addClass('has-error');
            $('#email').addClass('has-error').addClass('d-block');
        }
    });
    $("#in_celular").change(function () {
        var regex = /^[a-zA-Z ]+$/;
        var bol = regex.test($("#in_celular").val());
        if ($("#in_celular").val() == '') {
            $('#in_celular-error').text("Este campo es requerido").addClass('d-block').addClass('has-error');
            $('#in_celular').addClass('d-block').addClass('has-error');
        }
        else {
            if (bol != true) {
                if ($("#in_celular").val().length != 14) {
                    $('#in_celular-error').text("El Telefono debe contar con 10 digitos").addClass('d-block').addClass('has-error');
                    $('#in_celular').addClass('d-block').addClass('has-error');
                } else {
                    $('#in_celular-error').text("").removeClass('d-block').removeClass('has-error');
                    $('#in_celular').removeClass('d-block').removeClass('has-error');
                }
            } else {
                $('#in_celular-error').text("El telefono no puede llevar letras").addClass('d-block').addClass('has-error');
                $('#in_celular').addClass('d-block').addClass('has-error');
            }
        }
    });
    $('#in_celular').mask('00-00-00-00-00');
});