let namePer = ['leer', 'escribir', 'editar', 'eliminar'];
var dao = {
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
                par.append(new Option(val.fullname, val.id));
            });
            var par = $('#id_userPE');
            par.html('');
            par.append(new Option("-- Selecciona usuario --", ""));
            document.getElementById("id_userPE").options[0].disabled = true;
            $.each(data, function (i, val) {
                par.append(new Option(val.fullname, val.id));
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
            par.append(new Option("-- Selecciona Permiso --", ""));
            document.getElementById("id_permiso").options[0].disabled = true;
            $.each(data, function (i, val) {
                par.append(new Option(val.nombre, val.id));
            });
        });
    },
    editarUp: function (id,user, permiso) {
        $('#permisosModalLabel').text('Editar permiso adicional');
        $('#id').val(id)
        $("#id_userPE option[value='" + user + "']").attr("selected", true);
        $("#id_userPE").find('option').not(':selected').remove();
        $('#id_userPE').prop('disabled', 'disabled');
        $("#id_permiso option[value='"+ permiso+"']").attr("selected",true);
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
            const {mensaje } = response;
            Swal.fire({
                icon: mensaje.icon,
                title: mensaje.title,
                text: mensaje.text,
            });
        });
    },
    EditPermisos: function () {
        var form = $('#frm_permisosE')[0];
        var data = new FormData(form);
        data.append('id', $("#id").val());
        data.append('id_userP', $("#id_userPE").val());
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
            $('#cerrarE').trigger('click');
            const {mensaje } = response;
            Swal.fire({
                icon: mensaje.icon,
                title: mensaje.title,
                text: mensaje.text,
            });
            getData();
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
            const {mensaje } = response;
            Swal.fire({
                icon: mensaje.icon,
                title: mensaje.title,
                text: mensaje.text,
            });
            getData();
        });
    },
    limpiarFormularioCrear: function () {
        dao.getUsers();
        dao.getPermisos();
        $('#createPermisoLabel').text('Agregar permiso adicional');
        $('#descripcion').val('');
        $('#id_userP').prop('disabled', false);


    },
};
var init = {
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
    init.validatePermiso($('#frm_permisosE'));
    dao.getUsers();
    dao.getPermisos();

    $('#createPermiso').modal({
        backdrop: 'static',
        keyboard: false
    })
    $('#btnSaveP').click(function (e) {
        if ($('#frm_permisos').valid()) {
            dao.assignPermisos();
        }

    });
    $('#btnSavePE').click(function (e) {
        if ($('#frm_permisosE').valid()) {
            dao.EditPermisos();
        }

    });
/*funcion para agregar permisos al catalogo     
$('#btnSaveCreate').click(function (e) {
        dao.crearPermisos();

    }); */
});