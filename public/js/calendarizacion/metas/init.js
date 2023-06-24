const inputs = ['sel_actividad', 'sel_fondo', 'tipo_Ac', 'beneficiario', 'tipo_Be', 'medida'];

let actividades = [];
var dao = {
    eliminar: function (id) {
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
                    url: "/calendarizacion/detelet",
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
                    }
                });

            }
        })



    },
    getUrs: function () {
        $.ajax({
            type: "GET",
            url: '/calendarizacion/urs',
            dataType: "JSON"
        }).done(function (data) {
            var par = $('#ur_filter');
            par.html('');
            par.append(new Option("-- URS--", ""));
            document.getElementById("ur_filter").options[0].disabled = true;
            $.each(data, function (i, val) {
                par.append(new Option(data[i].ur, data[i].clv_ur));
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
    crearMeta: function () {
        var form = $('#actividad')[0];
        var data = new FormData(form);
        data.append('pMir_id',  $('[name="proyecto"]:checked').val());
        data.append('sumMetas', $('#sumMetas').val());
        $.ajax({
            type: "POST",
            url: '/calendarizacion/create',
            data: data,
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000
        }).done(function (response) {
            $('#cerrar').trigger('click');
            dao.limpiar();
            Swal.fire({
                icon: 'success',
                title: 'Your work has been saved',
                showConfirmButton: false,
                timer: 1500
            });
        });
    },
    crearMetaImp: function () {
        var form = $('#formFile')[0];
        var data = new FormData(form);
        $.ajax({
            type: "POST",
            url: '/actividades/import',
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
    importMeta: function () {
        var form = $('#formFile')[0];
        var data = new FormData(form);
        $.ajax({
            type: "POST",
            url: '/calendarizacion/create',
            data: data,
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000
        }).done(function (response) {
            Swal.fire({
                icon: 'success',
                title: 'Your work has been saved',
                showConfirmButton: false,
                timer: 1500
            });
        });
    },
    editarMeta: function (id) {
        Swal.fire({
            icon: 'success',
            title: 'Your work has been saved',
            showConfirmButton: false,
            timer: 1500
          })
    }, getSelect: function () {
        $.ajax({
            type: "GET",
            url: '/calendarizacion/selects',
            dataType: "JSON"
        }).done(function (data) {
            const { unidadM, fondos, beneficiario, actividades,activids } = data;
            var act = $('#sel_actividad'); 
            act.html('');
            act.append(new Option("--Actividad--","true",true,true));
            document.getElementById("sel_actividad").options[0].disabled = true;
            $.each(actividades, function (i, val) {
                act.append(new Option(val.actividad, i));
            });
            act.selectpicker({ search: true });
            var med = $('#medida');
            med.html('');
            med.append(new Option("-- Medida--", ""));
            document.getElementById("medida").options[0].disabled = true;
            $.each(unidadM, function (i, val) {
                med.append(new Option(val.unidad_medida, val.clave));
            }); 
            med.selectpicker({ search: true });
            var fond = $('#sel_fondo');
            fond.html('');
            fond.append(new Option("-- Fondos--", ""));
            document.getElementById("sel_fondo").options[0].disabled = true;
            $.each(fondos, function (i, val) {
                fond.append(new Option(fondos[i].ramo, fondos[i].clv_fondo_ramo));
            });
            fond.selectpicker({ search: true });
            var tipo_be = $('#tipo_Be');
            tipo_be.html('');
            tipo_be.append(new Option("--U. Beneficiarios--", ""));
            document.getElementById("tipo_Be").options[0].disabled = true;
            $.each(beneficiario, function (i, val) {
                tipo_be.append(new Option(beneficiario[i].beneficiario, beneficiario[i].clave));
            });
            tipo_be.selectpicker({ search: true });
            var tipo_AC = $('#tipo_Ac');
            tipo_AC.html('');
            tipo_AC.append(new Option("--Tipo Actividad--", ""));
            document.getElementById("tipo_Ac").options[0].disabled = true;
            $.each(activids, function (i, val) {
                tipo_AC.append(new Option(val, i));
            }); 
            tipo_AC.selectpicker({ search: true });

        });
    },
    limpiar: function () {
        console.log("limpiando.....")
        inputs.forEach(e => {
            $('#' + e + '-error').text("").removeClass('#' + e + '-error');
            if (e != 'beneficiario') {
                $('#'+e).selectpicker('destroy');
            }
        });
        dao.getSelect();
        $('.form-group').removeClass('has-error');  
        for (let i = 1; i <=12; i++) {
            $('#' + i).val(0);
        }
        $('#sumMetas').val(0);
        $('#beneficiario').val("");
        for (let i = 1; i <=12; i++) {
            $("#" + i).prop('disabled', true); 
        }
    },
    arrEquals: function (numeros) {
        let duplicados = [];
        let bool = numeros.length;
 
        const tempArray = [...numeros].sort();
         
        for (let i = 0; i <= tempArray.length; i++) {
          if (tempArray[i + 1] === tempArray[i]) {
            duplicados.push(tempArray[i]);
          }
        }
        if(bool != duplicados.length)
        {return false}else{return true}
      },
    validateAcu: function () {
        let e = 0;
        for (let i = 1; i <= 12; i++) {           
            let suma = parseInt($('#' + i).val() != "" ? $('#' + i).val() : 0);
            e += suma;
        }
        return e;
    },
    validatEspe: function () {
        let e = [];
        for (let i = 1; i <= 12; i++) {           
            let suma = parseInt($('#' + i).val() != "" ? $('#' + i).val() : 0);
            e.push(suma)
        }
        return Math.max(...e);
    },
    validatCont: function () {
        let e = [];
        for (let i = 1; i <= 12; i++) { 
            if($('#' + i).val() != ""){
                let suma = parseInt($('#' + i).val());
                e.push(suma);
            }
        }
        if (dao.arrEquals(e)) {
            return e[0];
        } else {
            $('#sumMetas').val("");
            //$("#btnSave").prop('disabled', true);
            Swal.fire({
                icon: 'info',
                title: 'Tipo de actividad continua',
                text: 'El valor ingresado de cada mes deben ser iguales',
                showConfirmButton: false,
                timer: 2000
            });
        }
    },
    sumar: function () {
        let actividad = $("#tipo_Ac option:selected").text();
        switch (actividad) {
            case 'Acumulativa':
                  $('#sumMetas').val(dao.validateAcu());
                break;
            case 'Continua':
                $('#sumMetas').val(dao.validatCont());
                break;
            case 'Especial':
                $('#sumMetas').val(dao.validatEspe());
                break;
        
            default:
                break;
        }
    },
    valMeses: function () {
        let e = [];
        for (let i = 1; i <= 12; i++) { 
            if($('#' + i).val() != ""){
                let suma = parseInt($('#' + i).val());
                e.push(suma);
            }
        }
        if (e>=1) {
            return true;
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Campos vacíos',
                text: 'Mínimo un mes debe contener una cantidad valida',
                showConfirmButton: false,
                timer: 2000
            });
        }
    },
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
    validateFile: function (form) {
        _gen.validate(form, {
            rules: {
                cmFile: { required: true }
            },
            messages: {
                cmFile: { required: "Este campo es requerido" }
            }
        });
    },
};

var init = {
    validateCreate: function (form) {
        _gen.validate(form, {
            rules: {
                sel_actividad: { required: true },
                sel_fondo: { required: true },
                tipo_Ac: { required: true },
                beneficiario: { required: true },
                tipo_Be: { required: true },
                medida: { required: true }
            },
            messages: {
                sel_actividad: { required: "Este campo es requerido" },
                sel_fondo: { required: "Este campo es requerido" },
                tipo_Ac: { required: "Este campo es requerido" },
                beneficiario: { required: "Este campo es requerido" },
                tipo_Be: { required: "Este campo es requerido" },
                medida: { required: "Este campo es requerido" }
            }
        });
    },
};
$(document).ready(function () {
    getData();    
    dao.getUrs();
    dao.getSelect();
    for (let i = 1; i <= 12; i++) {
        $("#" + i).val(0);
    }
    $("#sumMetas").val(0);

   
   $('input[type=search]').attr('id', 'serchUr');
    $('#exampleModal').modal({
        backdrop: 'static',
        keyboard: false
    })
    $('#btnSave').click(function (e) {
        e.preventDefault();
        if ($('#actividad').valid()) {
            dao.crearMeta();
        }
    });
    $('#btnSaveM').click(function (e) {
        e.preventDefault();
        if ($('#formFile').valid()) {
            dao.crearMetaImp();
        }
    });

    $('#tipo_Ac').change(() => {
        for (let i = 1; i <= 12; i++) {
            $("#" + i).prop('disabled', false);
        }

    })
});