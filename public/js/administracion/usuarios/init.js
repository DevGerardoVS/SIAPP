let flag = false;
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
                par.append(new Option(data[i].nombre_grupo, data[i].id));
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
                icon: 'success',
                title: 'Your work has been saved',
                showConfirmButton: false,
                timer: 1500
            });
           
            dao.limpiarFormularioCrear();
            getData();
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
            console.log("response",response)
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
            'clv_upp'
        ];
        inps.forEach(e => {
            $('#' + e).val('').removeClass('has-error').removeClass('d-block');
            $('#' + e + '-error').text("").removeClass('has-error').removeClass('d-block');

        });
        $("#id_grupo").find('option').remove();
        $("#sudo").find('option').remove();
        $("#sudo").append(new Option("-Selecciona perfil-", ""));
        $("#sudo").append(new Option("Administrador", "1"));
        $("#sudo").append(new Option("UPP", "0"));
        $("#clv_upp").find('option').remove();
        $('#divUpp').hide();
        dao.getPerfil();
        $('.form-group').removeClass('has-error');
        $("#id_grupo").show();
        $("#labelGrupo").show();
        $("#label_idGrupo").text("").hide();

    },
};
var init = {
    validateCreate: function (form,flag) {

        let rm =
        {
            rules: {
                username: { required: true },
                nombre: { required: true },
                p_apellido: { required: true },
                s_apellido: { required: true },
                email: {required: true, email: true},
                password: { required: true },
                in_pass_conf: { required: true, equalTo: "#password" },
                in_celular: { required: true,
                    phoneUS: true},
                id_grupo: { required: true },
                sudo: { required: true },
                clv_upp: { required: flag }

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
                sudo: { required: "Este campo es requerido" },
                clv_upp: { required: "Este campo es requerido" }

            }
        }
        console.log("rm",rm)
        _gen.validate(form,rm );

    },
};

$(document).ready(function () {
    getData();
    init.validateCreate($('#frm_create'),flag);
    dao.getPerfil();
    $("#sudo").change(function () {
        if (this.value != '1') {
            $('#divUpp').show();
            dao.getUpp();
            flag = true;
        } else {
            $('#divUpp').hide();
            $("#clv_upp option[value='"+ NULL +"']").attr("selected",true);
        }
     });
    
    $('#exampleModal').modal({
        backdrop: 'static',
        keyboard: false
    })
    $('#btnSave').click(function (e) {
        e.preventDefault();
        init.validateCreate($('#frm_create'),flag);
        if ($('#frm_create').valid()) {
            dao.crearUsuario();
        }
        $('#email-error').text("Este campo es requerido").addClass('has-error');;
        $('#in_celular-error').text("Este campo es requerido").addClass('has-error');;
    });
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