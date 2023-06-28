let actividades = [];
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
        })



    },
    getAnio: function () {
        let anio = [2022, 2023, 2024, 2025];
        var par = $('#anio_filter');
        par.html('');
        par.append(new Option("-- Año--", ""));
        document.getElementById("anio_filter").options[0].disabled = true;
        $.each(anio, function (i, val) {
            par.append(new Option(anio[i], anio[i]));
        });
        /*      $.ajax({
                 type: "GET",
                 url: 'grupos',
                 dataType: "JSON"
             }).done(function (data) {
                 var par = $('#id_grupo');
                 par.html('');
                 par.append(new Option("-- Selecciona Perfil --", ""));
                 document.getElementById("id_grupo").options[0].disabled = true;
                 $.each(data, function (i, val) {
                     par.append(new Option(data[i].nombre_grupo, data[i].id));
                 });
             }); */
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
            'id_grupo'
        ];
        inps.forEach(e => {
            $('#' + e).val('').removeClass('has-error').removeClass('d-block');
            $('#' + e + '-error').text("").removeClass('has-error').removeClass('d-block');

        });
        $("#id_grupo").find('option').remove();
        dao.getAnio();
        $('.form-group').removeClass('has-error');
        $("#id_grupo").show();
        $("#labelGrupo").show();
        $("#label_idGrupo").text("").hide();

    },
     edit_row:function (no){
    document.getElementById("edit_button" + no).style.display = "none";
    document.getElementById("save_button" + no).style.display = "block";

    var name = document.getElementById("name_row" + no);
    var country = document.getElementById("country_row" + no);
    var age = document.getElementById("age_row" + no);

    var name_data = name.innerHTML;
    var country_data = country.innerHTML;
    var age_data = age.innerHTML;

    name.innerHTML = "<input type='text' id='name_text" + no + "' value='" + name_data + "'>";
    country.innerHTML = "<input type='text' id='country_text" + no + "' value='" + country_data + "'>";
    age.innerHTML = "<input type='text' id='age_text" + no + "' value='" + age_data + "'>";
    },
    save_row:function(no) {
    var name_val = document.getElementById("name_text" + no).value;
    var country_val = document.getElementById("country_text" + no).value;
    var age_val = document.getElementById("age_text" + no).value;

    document.getElementById("name_row" + no).innerHTML = name_val;
    document.getElementById("country_row" + no).innerHTML = country_val;
    document.getElementById("age_row" + no).innerHTML = age_val;

    document.getElementById("edit_button" + no).style.display = "block";
    document.getElementById("save_button" + no).style.display = "none";
}, delete_row:function(no) {
    document.getElementById("row" + no + "").outerHTML = "";
},

    add_row: function () {
        var form = $('#actividad')[0];
        var data = new FormData(form);
        const f = {};
        data.forEach((value, key) => (f[key] = value));
        console.log(f);

    var table = document.getElementById("actividades");
    var table_len = (table.rows.length) - 1;
        var row = table.insertRow(table_len).outerHTML = "<tr'><td>" + f.actividades + "</td><td>" + f.metas + "</td><td>" + f.tipo_AC +
        "</td><td><input type='button' id='edit_button" + table_len + "' value='Edit' class='edit' onclick='edit_row(" + table_len + ")'> <input type='button' id='save_button" + table_len + "' value='Save' class='save' onclick='save_row(" + table_len + ")'> <input type='button' value='Delete' class='delete' onclick='delete_row(" + table_len + ")'></td></tr>";

    document.getElementById("new_name").value = "";
    document.getElementById("new_country").value = "";
    document.getElementById("new_age").value = "";
}
};
var init = {
    validateCreate: function (form) {
        _gen.validate(form, {
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
        });
    },
};

$(document).ready(function () {
    getData();
    dao.getAnio();
    $('#exampleModal').modal({
        backdrop: 'static',
        keyboard: false
    })
    $('#btnSave').click(function (e) {
        e.preventDefault();
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