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
    getUrs: function () {
        $.ajax({
            type: "GET",
            url: '/calendarizacion/urs',
            dataType: "JSON"
        }).done(function (data) {
            console.log("urs",data)
            var par = $('#ur_filter');
            par.html('');
            par.append(new Option("-- URS--", ""));
            document.getElementById("ur_filter").options[0].disabled = true;
            $.each(data, function (i, val) {
                par.append(new Option(data[i].descripcion, data[i].clave));
            });
            par.selectpicker({ search: true });

        });
    },
    getProg: function (ur) {
        $.ajax({
            type: "GET",
            url: '/calendarizacion/programas/'+ur,
            dataType: "JSON"
        }).done(function (data) {
            var par = $('#pr_filter');
            par.html('');
            par.append(new Option("-- Programa--", ""));
            document.getElementById("pr_filter").options[0].disabled = true;
            $.each(data, function (i, val) {
                par.append(new Option(val.clv_programa,val.programa));
            });
            par.selectpicker({ search: true });

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
    editarMeta: function (id) {
        console.log(id)
        Swal.fire({
            icon: 'success',
            title: 'Your work has been saved',
            showConfirmButton: false,
            timer: 1500
          })
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
    dao.getUrs();

    $('#ur_filter').change(function () {
        let ur = $("#ur_filter option:selected").val();
        console.log("saDASd",ur)
        $('#ur').val(ur);
    }) 
    
   $('input[type=search]').attr('id', 'serchUr');
    $('#exampleModal').modal({
        backdrop: 'static',
        keyboard: false
    })
    $('#btnSave').click(function (e) {
        e.preventDefault();
        if ($('#frm_create').valid()) {
            dao.crearUsuario();
        }
    });
   /* $("#serchUr").click(function () {
     console.log("aedsa",$('#serchUr').val());
      });
    */
});