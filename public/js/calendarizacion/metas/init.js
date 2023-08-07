
const inputs = ['sel_actividad', 'sel_fondo', 'tipo_Ac', 'beneficiario', 'tipo_Be', 'medida'];

let actividades = [];
let bandera=false
var dao = {
    checkCombination: function (upp) {
        $.ajax({
            type: "GET",
            url: '/calendarizacion/check/' + upp,
            dataType: "JSON"
        }).done(function (data) {
            if (data.status) {
                $("#ur_filter").removeAttr('disabled');
                $('#incomplete').hide(); 
                $("#icono").removeClass("fa fa-info-circle fa-5x d-flex justify-content-center");
                $('#texto').text('');
                if ($('#upp').val() == '') {
                    dao.getUrs($('#upp_filter').val());
                } else {
                    dao.getUrs($('#upp').val());
                }
                $('#metasVista').show();
                
            } else {
                dao.getUrs('0');
                dao.getData('000', '000');
                $("#ur_filter").attr('disabled', 'disabled');
                $('#incomplete').show(); 
                $("#icono").addClass("fa fa-info-circle fa-5x d-flex justify-content-center");
                $('#texto').text(data.mensaje);
                $('#metasVista').hide(); 
                Swal.fire({
                    icon: 'warning',
                    title: 'Información incompleta',
                    text: data.mensaje,
                    confirmButtonText: 'Aceptar',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (!data.estado) {
                            window.location.href = "/calendarizacion/claves";
                        }
                    }
                    
                });
            }

        });
    },
    getData: function (upp, ur) {
        var data = new FormData();
        if ($('#upp').val() != '') {
            data.append('ur_filter', ur);
        } else {
            data.append('ur_filter', ur);
            data.append('upp_filter', upp);
        }
        $.ajax({
            type: "POST",
            url: "/calendarizacion/data/",
            data: data,
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            dataType: "json"
        }).done(function (_data) {
            _table = $("#catalogo");
            _columns = [{
                width: "0rem",
                targets: [{ "aTargets": [0], "mData": [0] },
                { "aTargets": [1], "mData": [1] },
                { "aTargets": [2], "mData": [2] },
                { "aTargets": [3], "mData": [3] },
                { "aTargets": [4], "mData": [4] },
                { "aTargets": [5], "mData": [5] },
                { "aTargets": [6], "mData": [6] },
                { "aTargets": [7], "mData": [7] },
                { "aTargets": [8], "mData": [8] },
                { "aTargets": [9], "mData": [9] },
                { "aTargets": [10], "mData": [10] }]
            }
            ];
       /*  let columns={ "width": "0%", "targets":  _columns } */
            _gen.setTableScrollFotter(_table, _columns, _data.dataSet);
            let index = _data.dataSet;
            if (index.length==0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Esta unidad responsable no cuenta con presupuesto',
                    text: $('#ur_filter').find('option:selected').text(),
                });
                if ($('#upp').val() == '') {
                    dao.getUrs($('#upp_filter').val());
                } else {
                    dao.getUrs($('#upp').val());
                }
            }
        });
    },
    getUrs: function (upp) {
        $('#ur_filter').empty();
        $.ajax({
            type: "GET",
            url: '/calendarizacion/urs/' + upp,
            dataType: "JSON"
        }).done(function (data) {
            const { urs, tAct } = data;
            console.log("activids",tAct);
            var par = $('#ur_filter');
            par.html('');
            par.append(new Option("-- URS--", ""));
            document.getElementById("ur_filter").options[0].disabled = true;
            $.each(urs, function (i, val) {
                par.append(new Option(val.ur, val.clv_ur));
            });

            var tipo_AC = $('#tipo_Ac');
            tipo_AC.html('');
            tipo_AC.append(new Option("--Tipo Actividad--", ""));
            document.getElementById("tipo_Ac").options[0].disabled = true;
            $.each(tAct, function (i, val) {
                if (val == 1) {
                    tipo_AC.append(new Option(i, i));
                }
            });
            tipo_AC.select2({
                maximumSelectionLength: 10
            });

        });
    },
    getUpps: function () {
        $.ajax({
            type: "GET",
            url: '/calendarizacion/upps/',
            dataType: "JSON"
        }).done(function (data) {
            var par = $('#upp_filter');
            par.html('');
            par.append(new Option("-- UPPS--", ""));
            document.getElementById("upp_filter").options[0].disabled = true;
            $.each(data, function (i, val) {
                par.append(new Option(data[i].upp, data[i].clv_upp));
            });


        });
    },
    getProg: function (ur) {
        $.ajax({
            type: "GET",
            url: '/calendarizacion/programas/' + ur,
            dataType: "JSON"
        }).done(function (data) {
            var par = $('#pr_filter');
            par.html('');
            par.append(new Option("-- Programa--", ""));
            document.getElementById("pr_filter").options[0].disabled = true;
            $.each(data, function (i, val) {
                par.append(new Option(val.clv_programa, val.programa));
            });
            par.select2({
                maximumSelectionLength: 10
            });

        });
    },
    crearMeta: function () {
        var form = $('#actividad')[0];
        var data = new FormData(form);
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
            dao.limpiar();
            const {mensaje } = response;
            Swal.fire({
                icon: mensaje.icon,
                title: mensaje.title,
                text: mensaje.text,
            });
            if ($('#upp').val() == '') {
                dao.getUpps();
            } else {
                dao.checkCombination($('#upp').val())
            }
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
        }).done(function (response) {
            $('#cerrar').trigger('click');
            Swal.fire({
                icon: response.icon,
                title: response.title,
                text: response.text
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
            contentType: "application/x-www-form-urlencoded;charset=utf-8",
            cache: false,
            timeout: 600000
        }).success(function (response) {
            Swal.fire({
                icon: response.status,
                title: response.title,
                message: response.message,
                showConfirmButton: false,
                timer: 1500
            });
            $('#cerrar').trigger('click');
        }).fail(function (error, status, err) {
            console.log("error-", error);
        });
    },
     getSelect: function () {
        $.ajax({
            type: "GET",
            url: '/calendarizacion/selects',
            dataType: "JSON"
        }).done(function (data) {
            const { unidadM, beneficiario } = data;

            var med = $('#medida');
            med.html('');
            med.append(new Option("-- Medida--", ""));
            document.getElementById("medida").options[0].disabled = true;
            $.each(unidadM, function (i, val) {
                med.append(new Option(val.unidad_medida, val.clave));
            });
            med.select2({
                maximumSelectionLength: 10
            });

            var tipo_be = $('#tipo_Be');
            tipo_be.html('');
            tipo_be.append(new Option("--U. Beneficiarios--", ""));
            document.getElementById("tipo_Be").options[0].disabled = true;
            $.each(beneficiario, function (i, val) {
                tipo_be.append(new Option(beneficiario[i].beneficiario, beneficiario[i].clave));
            });
            tipo_be.select2({
                maximumSelectionLength: 10
            });


        });
    },
    getFyA: function (area,enti) {
        $.ajax({
            type: "GET",
            url: '/calendarizacion/fondos/' + area+'/'+enti,
            dataType: "JSON"
        }).done(function (data) {
            $('#sel_actividad').prop('disabled', false);
            $('#sel_fondo').prop('disabled', false);
            const { fondos, activids } = data;
            console.log("tipo Calendario",activids);

            var fond = $('#sel_fondo');
            fond.html('');
            fond.append("<option value=''class='text-center' ><b>-- Fondos--</b></option>");
            document.getElementById("sel_fondo").options[0].disabled = true;
            $.each(fondos, function (i, val) {
                fond.append(new Option(fondos[i].ramo, fondos[i].clv_fondo_ramo));
            });
            fond.select2({
                maximumSelectionLength: 10
            });
            var act = $('#sel_actividad');
            act.html('');
            act.append(new Option("--Actividad--", "true", true, true));
            document.getElementById("sel_actividad").options[0].disabled = true;
            $.each(activids, function (i, val) {
                act.append(new Option(val.actividad, val.id));
            });
            act.select2({
                maximumSelectionLength: 10
            });

        });
    },
    limpiar: function () {
        inputs.forEach(e => {
            $('#' + e + '-error').text("").removeClass('#' + e + '-error');
            if (e != 'beneficiario') {
                $('#' + e).selectpicker('destroy');
            }
        });
        dao.getSelect();
        $('.form-group').removeClass('has-error');
        for (let i = 1; i <= 12; i++) {
            $('#' + i).val(0);
        }
        $('#beneficiario').val("");
        for (let i = 1; i <= 12; i++) {
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
        if (bool != duplicados.length) { return false } else { return true }
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
            if ($('#' + i).val() != "") {
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
                $('#sumMetas').val(dao.validateAcu()!=0?dao.validateAcu():'');
                break;
            case 'Continua':
                $('#sumMetas').val(dao.validatCont()!=0?dao.validatCont():'');
                break;
            case 'Especial':
                $('#sumMetas').val(dao.validatEspe()!=0?dao.validatEspe():'');
                break;

            default:
                break;
        }
    },
    valMeses: function () {
        let e = [];
        for (let i = 1; i <= 12; i++) {
            if ($('#' + i).val() != "") {
                let suma = parseInt($('#' + i).val());
                e.push(suma);
            }
        }
        if (e >= 1) {
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
                sel_actividad: { required: true },
                sel_fondo: { required: true },
                tipo_Ac: { required: true },
                beneficiario: { required: true },
                tipo_Be: { required: true },
                medida: { required: true },
                sumMetas: {
                    required: true,
                }
            },
            messages: {
                sel_actividad: { required: "Este campo es requerido" },
                sel_fondo: { required: "Este campo es requerido" },
                tipo_Ac: { required: "Este campo es requerido" },
                beneficiario: { required: "Este campo es requerido" },
                tipo_Be: { required: "Este campo es requerido" },
                medida: { required: "Este campo es requerido" },
                sumMetas: { required: "Este campo es requerido  y mayor a CERO" }
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
$(document).ready(function () {
    
    $('#incomplete').hide();
    if ($('#upp').val() == '') {
        dao.getUpps();
    } else {
        dao.checkCombination($('#upp').val())
    }
    $('#upp_filter').change(() => {
        dao.checkCombination($('#upp_filter').val())
    });
    $('#ur_filter').change(() => {
        dao.getData($('#upp_filter').val(), $('#ur_filter').val());
        $('#sel_actividad').empty();
        $('#sel_fondo').empty();
    });

    dao.getSelect();
    $("#ur_filter").select2({
        maximumSelectionLength: 10
    });
    $("#upp_filter").select2({
        maximumSelectionLength: 10
    });
    $("#sel_fondo").select2({
        maximumSelectionLength: 10
    });
    $("#sel_actividad").select2({
        maximumSelectionLength: 10
    });
    $("#tipo_Ac").select2({
        maximumSelectionLength: 10
    });



    for (let i = 1; i <= 12; i++) {
        $("#" + i).val(0);
    }
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

    });


});