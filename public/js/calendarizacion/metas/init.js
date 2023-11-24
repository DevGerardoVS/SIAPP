const inputs = ['sel_actividad', 'sel_fondo', 'tipo_Ac', 'beneficiario', 'tipo_Be', 'medida'];
let mesesV = {
    enero: false,
    febrero: false,
    marzo: false,
    abril: false,
    mayo: false,
    junio: false,
    julio: false,
    agosto: false,
    septiembre: false,
    octubre: false,
    noviembre: false,
    diciembre: false
};
let contValue = 0;
let mesesName = [
    'enero',
    'febrero',
    'marzo',
    'abril',
    'mayo',
    'junio',
    'julio',
    'agosto',
    'septiembre',
    'octubre',
    'noviembre',
    'diciembre'
];
let actividades = [];
let bandera = false
var dao = {
    checkCombination: function (upp) {
        $.ajax({
            type: "GET",
            url: '/calendarizacion/check/' + upp,
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        }).done(function (data) {
            if (data.status) {
                $("#ur_filter").removeAttr('disabled');
                
                dao.getSelect();
                $('#incomplete').hide();
                $("#icono").removeClass("fa fa-info-circle fa-5x d-flex justify-content-center");
                $('#texto').text('');
                if ($('#upp').val() != '') {
                    dao.getUrs($('#upp').val());
                } else {
                    if ($('#upp_filter').val() != '') {
                        dao.getUrs($('#upp_filter').val());
                    } else {
                        dao.getUrs('0');
                    }

                }
                $('#metasVista').show();
                $(".CargaMasiva").show();
                $(".btnSave").show();
            } else {
                $(".CargaMasiva").hide();
                $(".btnSave").hide();
                dao.getUrs('0');
                $('#carga').hide();
                $("#ur_filter").attr('disabled', 'disabled');
                $("#tipo_Ac").attr('disabled', 'disabled');
                $("#sel_fondo").attr('disabled', 'disabled');
                $("#sel_actividad").attr('disabled', 'disabled');

                $('#incomplete').show();
                $("#icono").addClass("fa fa-info-circle fa-5x d-flex justify-content-center");
                $('#texto').text(data.mensaje);
                $('#metasVista').hide();
                Swal.fire({
                    icon: 'warning',
                    title: data?.title ? data.title : 'Información incompleta',
                    text: data.mensaje,
                    confirmButtonText: 'Aceptar',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (!data.estado) {
                            window.location.href = data.url
                        }
                    }

                });
            }

        });
    },
    getData: function (upp, ur) {
        /*   var data = new FormData();
          if ($('#upp').val() != '') {
              data.append('ur_filter', ur);
          } else {
              data.append('ur_filter', ur);
              data.append('upp_filter', upp);
          } */
        $.ajax({
            type: "GET",
            url: "/calendarizacion/data/" + upp + "/" + ur,
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        }).done(function (_data) {
            _table = $("#entidad");
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
            if (index.length == 0) {
                if (upp != 'upp' && ur != 'ur') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Esta unidad responsable no cuenta con presupuesto',
                        text: $('#ur_filter').find('option:selected').text(),
                    });
                    $('#incomplete').show();
                    $("#icono").addClass("fa fa-info-circle fa-5x d-flex justify-content-center");
                    $('#texto').text('Esta unidad responsable no cuenta con presupuesto');
                    $('#metasVista').hide();
                }
                dao.limpiar();
                $('.btnSave').hide();
                $(".CargaMasiva").hide();
                if ($('#upp').val() == '') {
                    dao.getUrs($('#upp_filter').val());
                } else {
                    dao.getUrs($('#upp').val());
                }

            } else {
                $('.btnSave').show();
                $('#incomplete').hide();
                $("#icono").removeClass("fa fa-info-circle fa-5x d-flex justify-content-center");
                $('#texto').text('');
                $('#metasVista').show();
                $(".CargaMasiva").show();
            }

        });
    },
    getUrs: function (upp) {
        $('#ur_filter').empty();
        $.ajax({
            type: "GET",
            url: '/calendarizacion/urs/' + upp,
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        }).done(function (data) {
            const { urs, tAct } = data;
            var par = $('#ur_filter');
            par.html('');
            par.append(new Option("-- URS--", ""));
            document.getElementById("ur_filter").options[0].disabled = true;
            $.each(urs, function (i, val) {
                par.append(new Option(val.ur, val.clv_ur));
            });
        });
    },
    nCont: function () {
        if ($('#nContinua').val()!='') {
            contValue = $('#nContinua').val();
            let fondo = $('#sel_fondo').val() != null ? $('#sel_fondo').val() : $('#fondo_id').val();
            dao.getMesesCont($('#area').val(),fondo,contValue);
            $('#sumMetas').val(contValue);
            $('#sumMetas').attr('disabled', 'disabled');
            dao.clearCont('aceptar');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Este campo es requerido',
              })
        }
      
    },
    clearCont: function (tipo) {
        if (tipo!='aceptar') {
            $("#tipo_Ac option[value='']").attr("selected", true);
        }
        $('#nContinua').val("");
        $('#continua').modal('hide');
    },
    getMeses: function (idA, idF) {
        let arr = idA.split('-');
        for (const key in mesesV) {
            if (Object.hasOwnProperty.call(mesesV, key)) {
                mesesV[key] = false;
            }
        }
        $.ajax({
            type: "GET",
            url: '/actividades/meses-activos/' + idA + "/" + idF,
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        }).done(function (data) {
            let { mese } = data;
            if (arr[8] != 'UUU') {
                for (const key in mese) {
                    if (Object.hasOwnProperty.call(mese, key)) {
                        const e = mese[key];
                        switch (key) {
                            case 'enero':
                                if (e != 0.0 || e != 0) {
                                    mesesV.enero = true;
                                    $("#1").prop('disabled', false);
                                    $("#1").prop('required', true);
                                } else {
                                    $("#1").prop('disabled', 'disabled');
                                }
                                break;
                            case 'febrero':
                                if (e != 0.0 || e != 0) {
                                    mesesV.febrero = true;
                                    $("#2").prop('disabled', false);
                                    $("#2").prop('required', true);
                                } else {
                                    $("#2").prop('disabled', 'disabled');
                                }
                                break;
                            case 'marzo':
                                if (e != 0.0 || e != 0) {
                                    mesesV.marzo = true;
                                    $("#3").prop('disabled', false);
                                    $("#3").prop('required', true);
                                } else {
                                    $("#3").prop('disabled', 'disabled');

                                }
                                break;
                            case 'abril':
                                if (e != 0.0 || e != 0) {
                                    mesesV.abril = true;
                                    $("#4").prop('disabled', false);
                                    $("#4").prop('required', true);
                                } else {
                                    $("#4").prop('disabled', 'disabled');
                                }
                                break;
                            case 'mayo':
                                if (e != 0.0 || e != 0) {
                                    mesesV.mayo = true;
                                    $("#5").prop('disabled', false);
                                    $("#5").prop('required', true);
                                } else {
                                    $("#5").prop('disabled', 'disabled');
                                }
                                break;
                            case 'junio':
                                if (e != 0.0 || e != 0) {
                                    mesesV.junio = true;
                                    $("#6").prop('disabled', false);
                                    $("#6").prop('required', true);
                                } else {
                                    $("#6").prop('disabled', 'disabled');
                                }
                                break;
                            case 'julio':
                                if (e != 0.0 || e != 0) {
                                    mesesV.julio = true;
                                    $("#7").prop('disabled', false);
                                    $("#7").prop('required', true);
                                } else {
                                    $("#7").prop('disabled', 'disabled');
                                }
                                break;
                            case 'agosto':
                                if (e != 0.0 || e != 0) {
                                    mesesV.agosto = true;
                                    $("#8").prop('disabled', false);
                                    $("#8").prop('required', true);
                                } else {
                                    $("#8").prop('disabled', 'disabled');
                                }
                                break;
                            case 'septiembre':
                                if (e != 0.0 || e != 0) {
                                    mesesV.septiembre = true;
                                    $("#9").prop('disabled', false);
                                    $("#9").prop('required', true);
                                } else {
                                    $("#9").prop('disabled', 'disabled');
                                }
                                break;
                            case 'octubre':
                                if (e != 0.0 || e != 0) {
                                    mesesV.octubre = true;
                                    $("#10").prop('disabled', false);
                                    $("#10").prop('required', true);
                                } else {
                                    $("#10").prop('disabled', 'disabled');
                                }
                                break;
                            case 'noviembre':
                                if (e != 0.0 || e != 0) {
                                    mesesV.noviembre = true;
                                    $("#11").prop('disabled', false);
                                    $("#11").prop('required', true);
                                } else {
                                    $("#11").prop('disabled', 'disabled');
                                }
                                break;
                            case 'diciembre':

                                if (e != 0.0 || e != 0) {
                                    mesesV.diciembre = true;
                                    $("#12").prop('disabled', false);
                                    $("#12").prop('required', true);
                                } else {
                                    $("#12").prop('disabled', 'disabled');
                                }
                                break;

                            default:
                                break;
                        }

                    }
                }
            } else {
                for (let i = 1; i <= 11; i++) {
                    $("#" + i).val(2);
                    $("#" + i).prop('disabled', 'disabled');
                }
                $("#12").val(3);
                $("#12").prop('disabled', 'disabled');
                $("#sumMetas").val(25);
                $("#sumMetas").prop('disabled', 'disabled')


            }



        });
    },
    getMesesCont: function (idA, idF,value) {
        for (const key in mesesV) {
            if (Object.hasOwnProperty.call(mesesV, key)) {
                mesesV[key] = false;
            }
        }
        $.ajax({
            type: "GET",
            url: '/actividades/meses-activos/' + idA + "/" + idF,
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        }).done(function (data) {
            let { mese } = data;
                for (const key in mese) {
                    if (Object.hasOwnProperty.call(mese, key)) {
                        const e = mese[key];
                        switch (key) {
                            case 'enero':
                                if (e != 0.0 || e != 0) {
                                    mesesV.enero = true;
                                    $("#1").val(value);
                                } else {
                                    $("#1").prop('disabled', 'disabled');
                                }
                                break;
                            case 'febrero':
                                if (e != 0.0 || e != 0) {
                                    mesesV.febrero = true;
                                    $("#2").val(value);
                                } else {
                                    $("#2").prop('disabled', 'disabled');
                                }
                                break;
                            case 'marzo':
                                if (e != 0.0 || e != 0) {
                                    mesesV.marzo = true;
                                    $("#3").val(value);
                                } else {
                                    $("#3").prop('disabled', 'disabled');

                                }
                                break;
                            case 'abril':
                                if (e != 0.0 || e != 0) {
                                    mesesV.abril = true;
                                    $("#4").val(value);
                                } else {
                                    $("#4").prop('disabled', 'disabled');
                                }
                                break;
                            case 'mayo':
                                if (e != 0.0 || e != 0) {
                                    mesesV.mayo = true;
                                    $("#5").val(value);
                                } else {
                                    $("#5").prop('disabled', 'disabled');
                                }
                                break;
                            case 'junio':
                                if (e != 0.0 || e != 0) {
                                    mesesV.junio = true;
                                    $("#6").val(value);
                                } else {
                                    $("#6").prop('disabled', 'disabled');
                                }
                                break;
                            case 'julio':
                                if (e != 0.0 || e != 0) {
                                    mesesV.julio = true;
                                    $("#7").val(value);
                                } else {
                                    $("#7").prop('disabled', 'disabled');
                                }
                                break;
                            case 'agosto':
                                if (e != 0.0 || e != 0) {
                                    mesesV.agosto = true;
                                    $("#8").val(value);
                                } else {
                                    $("#8").prop('disabled', 'disabled');
                                }
                                break;
                            case 'septiembre':
                                if (e != 0.0 || e != 0) {
                                    mesesV.septiembre = true;
                                    $("#9").val(value);
                                } else {
                                    $("#9").prop('disabled', 'disabled');
                                }
                                break;
                            case 'octubre':
                                if (e != 0.0 || e != 0) {
                                    mesesV.octubre = true;
                                    $("#10").val(value);
                                } else {
                                    $("#10").prop('disabled', 'disabled');
                                }
                                break;
                            case 'noviembre':
                                if (e != 0.0 || e != 0) {
                                    mesesV.noviembre = true;
                                    $("#11").val(value);
                                } else {
                                    $("#11").prop('disabled', 'disabled');
                                }
                                break;
                            case 'diciembre':

                                if (e != 0.0 || e != 0) {
                                    mesesV.diciembre = true;
                                    $("#12").val(value);
                                } else {
                                    $("#12").prop('disabled', 'disabled');
                                }
                                break;

                            default:
                                break;
                        }

                    }
                }

        });
    },
    validMeses: function () {
        let mesesfs = 0;
        for (const key in mesesV) {
            if (Object.hasOwnProperty.call(mesesV, key)) {
                const e = mesesV[key];
                switch (key) {
                    case 'enero':
                        if (e) {

                            if ($("#1").val() == 0) {
                                $('#1-error').text("Este campo es requerido").addClass('has-error').css({ 'display': '' });
                                mesesfs++;
                            } else {

                                $('#1-error').text("").removeClass('has-error');
                            }
                        }
                        break;
                    case 'febrero':
                        if (e) {

                            if ($("#2").val() == 0) {
                                $('#2-error').text("Este campo es requerido").addClass('has-error').css({ 'display': '' });
                                mesesfs++;
                            } else {

                                $('#2-error').text("").removeClass('has-error');
                            }
                        }
                        break;
                    case 'marzo':
                        if (e) {

                            if ($("#3").val() == 0) {
                                $('#3-error').text("Este campo es requerido").addClass('has-error').css({ 'display': '' });
                                mesesfs++;
                            } else {

                                $('#3-error').text("").removeClass('has-error');
                            }
                        }
                        break;
                    case 'abril':
                        if (e) {

                            if ($("#4").val() == 0) {
                                $('#4-error').text("Este campo es requerido").addClass('has-error').css({ 'display': '' });
                                mesesfs++;
                            } else {

                                $('#4-error').text("").removeClass('has-error');
                            }
                        }
                        break;
                    case 'mayo':
                        if (e) {

                            if ($("#5").val() == 0) {
                                $('#5-error').text("Este campo es requerido").addClass('has-error').css({ 'display': '' });
                                mesesfs++;
                            } else {

                                $('#5-error').text("").removeClass('has-error');
                            }
                        }
                        break;
                    case 'junio':
                        if (e) {
                            if ($("#6").val() == 0 || $("#6").val() == '0') {
                                $('#6-error').text("Este campo es requerido").addClass('has-error').show();
                                mesesfs++;
                            } else {

                                $('#6-error').text("").removeClass('has-error');
                            }
                        }
                        break;
                    case 'julio':
                        if (e) {

                            if ($("#7").val() == 0) {
                                $('#7-error').text("Este campo es requerido").addClass('has-error').css({ 'display': '' });
                                mesesfs++;
                            } else {

                                $('#7-error').text("").removeClass('has-error');
                            }
                        }
                        break;
                    case 'agosto':
                        if (e) {

                            if ($("#8").val() == 0) {
                                $('#8-error').text("Este campo es requerido").addClass('has-error').css({ 'display': '' });
                                mesesfs++;
                            } else {

                                $('#8-error').text("").removeClass('has-error');
                            }
                        }
                        break;
                    case 'septiembre':
                        if (e) {

                            if ($("#9").val() == 0) {
                                $('#9-error').text("Este campo es requerido").addClass('has-error').css({ 'display': '' });
                                mesesfs++;
                            } else {

                                $('#9-error').text("").removeClass('has-error');
                            }
                        }
                        break;
                    case 'octubre':
                        if (e) {

                            if ($("#10").val() == 0) {
                                $('#10-error').text("Este campo es requerido").addClass('has-error').css({ 'display': '' });
                                mesesfs++;
                            } else {

                                $('#10-error').text("").removeClass('has-error');
                            }
                        }
                        break;
                    case 'noviembre':
                        if (e) {

                            if ($("#11").val() == 0) {
                                $('#11-error').text("Este campo es requerido").addClass('has-error').css({ 'display': '' });
                                mesesfs++;
                            } else {

                                $('#11-error').text("").removeClass('has-error');
                            }
                        }
                        break;
                    case 'diciembre':

                        if (e) {

                            if ($("#12").val() == 0) {
                                $('#12-error').text("Este campo es requerido").addClass('has-error').css({ 'display': '' });
                                mesesfs++;
                            } else {

                                $('#1-error').text("").removeClass('has-error');
                            }
                        }
                        break;

                    default:
                        break;
                }

            }
        }
        console.log("meses sin llenar:", mesesfs);
        if (mesesfs >= 1) {
            $("#meses-error").text("Debes de llenar los meses desbloqueados").addClass('has-error');
            return false;

        } else {
            $("#meses-error").text("").removeClass('has-error');
            return true;
        }
    },
    getUpps: function () {
        $.ajax({
            type: "GET",
            url: '/calendarizacion/upps',
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        }).done(function (data) {
            const { upp } = data;
            var par = $('#upp_filter');
            par.html('');
            par.append(new Option("-- UPPS--", ""));
            document.getElementById("upp_filter").options[0].disabled = true;
            $.each(upp, function (i, val) {
                par.append(new Option(val.upp, val.clv_upp));
            });


        });
    },
    getPlantillaCmUpp: function () {
        let upp = $('#upp').val() != '' ? $('#upp').val() : $('#upp_filter').val();
        const url = "/actividades/proyecto_calendario/" + upp;
        window.location.href = url;
    },
    getProg: function (ur) {
        $.ajax({
            type: "GET",
            url: '/calendarizacion/programas/' + ur,
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
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
        data.append('upp', $('#upp').val() != '' ? $('#upp').val() : $('#upp_filter').val());
        let aOld = $('#area').val()
        let area = aOld.replace('$', '/')
        data.append('area', area);
        data.append('conmir', $("#conmir").val());
        if ($('#tipo_Ac').val() == 'Continua') {
            if (mesesV.enero) {
                    data.append(1, contValue);
            }
            if (mesesV.febrero) {
                data.append(2, contValue);
            } 
            if (mesesV.marzo) {
                data.append(3, contValue);
            } 
            if (mesesV.abril) {
                data.append(4, contValue);
            } 
            if (mesesV.mayo) {
                data.append(5, contValue);
            } 
            if (mesesV.junio) {
                data.append(6, contValue);
            } 
            if (mesesV.julio) {
                data.append(7, contValue);
            } 
            if (mesesV.agosto) {
                data.append(8, contValue);
            } 
            if (mesesV.septiembre) {
                data.append(9, contValue);
            } 
            if (mesesV.octubre) {
                data.append(10, contValue);
            } 
            if (mesesV.noviembre) {
                data.append(12, contValue);
            } 
            if (mesesV.diciembre) {
                data.append(12, contValue);
            } 

            data.append('sumMetas', contValue);
        }
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
            const { mensaje } = response;
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
            dao.getData('upp', 'ur');
            dao.limpiar();
        });
    },
    rCMetasUpp: function (upp) {
        $.ajax({
            type: "GET",
            url: '/agregar-actividades/confirmacion-metas-upp/' + upp,
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        }).done(function (data) {
            if (!data.status) {
                $(".cmupp").show();
                $('#validMetas').addClass(" alert alert-danger").addClass("text-center");
                $('#validMetas').text("Las metas ya fueron confirmadas");
                $(".CargaMasiva").hide();
            } else {
                $(".cmupp").hide();
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
            $("#cmFile").val("");
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
        });
    },
    getSelect: function () {
        $.ajax({
            type: "GET",
            url: '/calendarizacion/selects',
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        }).done(function (data) {
            const { unidadM, beneficiario } = data;
            var med = $('#medida');
            med.html('');
            var tipo_be = $('#tipo_Be');
            tipo_be.html('');
            let sub = $('#area').val();
             if (sub.includes('UUU')) {
                 med.append(new Option("Pago de nómina", 829));
                 tipo_be.append(new Option("Empleados", 12));
             } else {
                med.append(new Option("-- Medida--", ""));
                document.getElementById("medida").options[0].disabled = true;
                $.each(unidadM, function (i, val) {
                    med.append(new Option(val.unidad_medida, val.clave));
                });
                 
                tipo_be.append(new Option("--U. Beneficiarios--", ""));
                document.getElementById("tipo_Be").options[0].disabled = true;
                $.each(beneficiario, function (i, val) {
                    tipo_be.append(new Option(beneficiario[i].beneficiario, beneficiario[i].id));
                });
            }
        });
    },
    getFyA: function (area, enti, mir, anio) {
        dao.limpiarErrors();
        $('#tipo_Ac').empty();
        for (let i = 1; i <= 12; i++) {
            $("#" + i).val(0);
            $("#" + i).prop('disabled', true);
        }
        $("#activiMir").val(`${area}$${enti}`);
        let ar = area.split('-'); 
        $("#calendar").val(ar[8]); 
        let clave = `${area}$${enti}$${anio}`;
        $("#area").val(clave);
        $("#sel_fondo").removeAttr('disabled');
        dao.getSelect();
        $.ajax({
            type: "GET",
            url: '/calendarizacion/fondos/' + area + '/' + enti,
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        }).done(function (data) {
            const { fondos, tAct } = data;
                var fond = $('#sel_fondo');
                fond.html('');
                if (fondos.length >= 2) {
                    fond.append("<option value=''class='text-center' ><b>-- Fondos--</b></option>");
                    document.getElementById("sel_fondo").options[0].disabled = true;
                }
                $.each(fondos, function (i, val) {
                    fond.append(new Option(val.ramo, val.clave));
                });
            
            if (fondos.length == 1) {
                $("#tipo_Ac").removeAttr('disabled');
                let fondo = '';
                if ($('#sel_fondo').val() != '' || $('#sel_fondo').val() != null) {
                    fondo = $('#sel_fondo').val();
                } else {
                    fondo = $('#fondo_id').val();
                }
                $('#actividad_id').prop('disabled', false);
                dao.getMeses(clave, fondo);
                dao.getActividasdesMir(fondo)
            }

            var tipo_AC = $('#tipo_Ac');
            tipo_AC.html('');
            let tm =Object.keys(tAct).length;
            if (tAct.Acumulativa == 0 && $('#calendar').val()!='UUU') {
                tipo_AC.append(new Option("--Tipo Actividad--", ""));
                document.getElementById("tipo_Ac").options[0].disabled = true;

            }
            if ($('#calendar').val()=='UUU') {
                tipo_AC.append(new Option('Acumulativa','Acumulativa'));
            } else {
                $.each(tAct, function (i, val) {
                    if (val == 1) {
                        tipo_AC.append(new Option(i, i));
                    }
                });
            }

        });
    },
    getActividasdesMir: function (fondo) {
       let clave= $("#activiMir").val();
        let mir = clave.split('$');
        $.ajax({
            type: "GET",
            url: '/actividades/metas/actividades-mir/' + mir[0] + '/' + mir[1]+'/'+fondo,
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        }).done(function (data) {
            const { con_mir, activids } = data;
            let conmir = con_mir == 1 ? true : false;
                $("#conmir").val(conmir);
                var act = $('#actividad_id');
                act.html('');
                if (activids.length == 2) {
                    act.append(new Option("--Actividad--", "true", true, true));
                    document.getElementById("actividad_id").options[0].disabled = true;
                }
                $.each(activids, function (i, val) {
                    act.append(new Option(val.actividad, val.id));
                });
            
            if ($("#actividad_id").val() == 'ot') {
                    $("#conmir").val(false);
                        $("#inputAc").removeAttr('disabled');
                        $('#actividad_id').prop('disabled', false);
                        $(".inputAc").show(); 
                        $(".fondodiv").removeClass("col-md-6").addClass("col-md-4"); 
                        $(".actividaddiv").hide();
                        $(".acOt").show();
                    } else {
                        $(".actividaddiv").show();
                        $(".acOt").hide();
                        $(".inputAc").val('');
                        $("#inputAc").attr('disabled', 'disabled');
                        $(".inputAc").hide();
                        $(".fondodiv").removeClass("col-md-4").addClass("col-md-6");
                    }
    
                
     

        });
    },
    limpiar: function () {
        $('#sumMetas').val('');
        $('#sumMetas-error').removeClass('has-error');
        $('#sumMetas-error').text('');
        inputs.forEach(e => {
            $('#' + e + '-error').text("").removeClass('#' + e + '-error');
            if (e != 'beneficiario') {
                $('#' + e).selectpicker('destroy');
            }
        });
        if ($('#upp').val() == '') {
            dao.getUrs(0);
        } else {
            upp = $('#upp').val();
            dao.getUrs(upp);
        }
       
        dao.getSelect();
        $('.form-group').removeClass('has-error');
        for (let i = 1; i <= 12; i++) {
            $('#' + i).val(0);
        }
        $('#beneficiario').val("");
        for (let i = 1; i <= 12; i++) {
            $("#" + i).prop('disabled', true);
        }
        $('#inputAc').val('');
        $('#sel_actividad').empty();
        $('#sel_actividad').append("<option value=''class='text-center' ><b>-- Actividad--</b></option>");
        $('#sel_fondo').empty();
        $('#sel_fondo').append("<option value=''class='text-center' ><b>-- Fondos--</b></option>");
        $('#actividad_id').empty();
        $('#actividad_id').append("<option value=''class='text-center' ><b>-- Actividad--</b></option>");
        $('#fondo_id').empty();
        $('#fondo_id').append("<option value=''class='text-center' ><b>-- Fondos--</b></option>");
    },
    clearUR: function () {
        $('#sumMetas').val('');
        $('#sumMetas-error').removeClass('has-error');
        $('#sumMetas-error').text('');
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
        $('#inputAc').val('');
        $('#sel_actividad').empty();
        $('#sel_actividad').append("<option value=''class='text-center' ><b>-- Actividad--</b></option>");
        $('#sel_fondo').empty();
        $('#sel_fondo').append("<option value=''class='text-center' ><b>-- Fondos--</b></option>");
        $('#actividad_id').empty();
        $('#actividad_id').append("<option value=''class='text-center' ><b>-- Actividad--</b></option>");
        $('#fondo_id').empty();
        $('#fondo_id').append("<option value=''class='text-center' ><b>-- Fondos--</b></option>");
        $('#tipo_Ac').empty();
        $('#tipo_Ac').append("<option value=''class='text-center' ><b>-- calendario --</b></option>");
    },
    limpiarErrors: function () {
        $("#meses-error").text("").removeClass('has-error');
        $("#conmir").val(true);
        $('#actividad_id').attr('disabled', 'disabled');
        $(".inputAc").hide().removeClass('has-error');
        $("#medida-error").text("").removeClass('has-error');
        $("#inputAc-error").text("").removeClass('has-error');
        $("#tipo_Be-error").text("").removeClass('has-error');
        $("#beneficiario-error").text("").removeClass('has-error');
        $("#tipo_Ac-error").text("").removeClass('has-error');
        $("#fondo_id-error").text("").removeClass('has-error');
        $("#actividad_id-error").text("").removeClass('has-error');
        $("#sel_fondo-error").text("").removeClass('has-error');
        $("#sel_actividad-error").text("").removeClass('has-error');
        $('#sumMetas-error').text("").removeClass('has-error');
        $("#idAct").addClass("col-md-6").removeClass("col-md-4");
        $("#idFond").addClass("col-md-6").removeClass("col-md-4");
        $('#sel_actividad').val('');
        $('#fondo_id').val('');
        $("#inputAc").val('');
        $("#sel_fondo").val('');
        $("#beneficiario").val('');
        $("#sumMetas").val('');
        $("#sel_fondo").empty('');
        $('#fondo_id').empty('');
        $('#actividad_id').empty();
        $('#sel_actividad').empty();
        $('.form-group').removeClass('has-error');
    },
    DesConfirmarMetas: function () {
        let anio = $('#anio_filter').val();
        let upp = "";
        if ($('#upp').val() == '') {
            upp = $('#upp_filter').val();

        } else {
            upp = $('#upp').val();
        }
        Swal.fire({
            icon: 'question',
            title: '¿Estás de quieres desconfirmar las metas?',
            showDenyButton: true,
            confirmButtonText: 'Confirmar',
            denyButtonText: `Cancelar`,
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                $.ajax({
                    type: "GET",
                    url: '/actividades/desconfirmar-metas/' + upp + "/" + anio,
                    dataType: "JSON",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                }).done(function (data) {
                    const { mensaje } = data;
                    Swal.fire({
                        icon: mensaje.icon,
                        title: mensaje.title,
                        text: mensaje.text,
                    });
                });
            } /* else if (result.isDenied) {
              Swal.fire('Changes are not saved', '', 'info')
            } */
        })

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
                if ($('#' + i).val() != 0 && $('#' + i).val() != "" && $('#' + i).val() != "null" && $('#' + i).val() != null) {
                    e.push(suma);
                }

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
                $('#sumMetas').val(dao.validateAcu() != 0 ? dao.validateAcu() : '');
                break;
            case 'Continua':
                $('#sumMetas').val(dao.validatCont() != 0 ? dao.validatCont() : '');
                break;
            case 'Especial':
                $('#sumMetas').val(dao.validatEspe() != 0 ? dao.validatEspe() : '');
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
                actividad_id: { required: true },
                inputAc: { required: true },
                fondo_id: { required: true },
                sel_actividad: { required: true },
                sel_fondo: { required: true },
                tipo_Ac: { required: true },
                beneficiario: { required: true },
                tipo_Be: { required: true },
                medida: { required: true },
                sumMetas: {
                    required: true,
                },
            },
            messages: {
                actividad_id: { required: "Este campo es requerido" },
                inputAc: { required: "Este campo es requerido" },
                fondo_id: { required: "Este campo es requerido" },
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
    validateCreateN: function (form) {
        _gen.validate(form, {
            rules: {
                actividad_id: { required: true },
                inputAc: { required: true },
                fondo_id: { required: true },
                tipo_Ac: { required: true },
                beneficiario: { required: true },
                tipo_Be: { required: true },
                medida: { required: true },
                sumMetas: {
                    required: true,
                }
            },
            messages: {
                actividad_id: { required: "Este campo es requerido" },
                inputAc: { required: "Este campo es requerido" },
                fondo_id: { required: "Este campo es requerido" },
                tipo_Ac: { required: "Este campo es requerido" },
                beneficiario: { required: "Este campo es requerido" },
                tipo_Be: { required: "Este campo es requerido" },
                medida: { required: "Este campo es requerido" },
                sumMetas: { required: "Este campo es requerido  y mayor a CERO" },
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
    validateCont: function (form) {
        _gen.validate(form, {
            rules: {
                nContinua: { required: true }
            },
            messages: {
                nContinua: { required: "Este campo es requerido" }
            }
        });
    },
};
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(".CargaMasiva").hide();
    $(".btnSave").hide();
    $("#beneficiario").on('paste', function (e) {
        e.preventDefault();
    });
    $("#sumMetas").on('paste', function (e) {
        e.preventDefault();
    });
    if ($('#upp').val() == '') {
        dao.getUpps();
    } else {
        dao.checkCombination($('#upp').val())
    }
    $('#upp_filter').change(() => {
        dao.checkCombination($('#upp_filter').val());
        dao.rCMetasUpp($('#upp_filter').val());
    });
    $('#ur_filter').change(() => {
        dao.clearUR()
        dao.getData($('#upp_filter').val(), $('#ur_filter').val());
        
        $('#sel_actividad').empty();
        $('#sel_actividad').append("<option value=''class='text-center' ><b>-- Actividad--</b></option>");
        $('#sel_fondo').empty();
        $('#sel_fondo').append("<option value=''class='text-center' ><b>-- Fondos--</b></option>");


    });
    $('#sel_fondo').change(() => {
        dao.getActividasdesMir($('#sel_fondo').val())
        dao.getMeses($('#area').val(), $('#sel_fondo').val());

    });
    $('#sel_actividad').change(() => {
        if ($('#sel_fondo').val() != '' && $('#sel_fondo').val() != null) {
            dao.getMeses($('#area').val(), $('#sel_fondo').val());
        }
    });
    $('#fondo_id').change(() => {
        dao.getActividasdesMir($('#fondo_id').val())
        dao.getMeses($('#area').val(), $('#fondo_id').val());
    });

    $('#tipo_Ac').change(() => {
        for (let i = 1; i <= 12; i++) {
              $('#' + i).val(0);
        }
        let fondo = $('#sel_fondo').val() != null ? $('#sel_fondo').val() : $('#fondo_id').val();
        dao.getMeses($('#area').val(),fondo);
        $("#sumMetas").val("");
        if ($('#tipo_Ac').val() == 'Continua') {
            $('#continua').modal('show')
        }
    });
    $('#actividad_id').change(() => {

        if ($('#actividad_id').val() == 'ot') {
            $("#inputAc").removeAttr('disabled');
            $(".inputAc").show();

            $("#idAct").addClass("col-md-4").removeClass("col-md-6");
            $("#idFond").addClass("col-md-4").removeClass("col-md-6");

        } else {
            $(".inputAc").val('');
            $("#inputAc").attr('disabled', 'disabled');
            $(".inputAc").hide();
            $("#idAct").addClass("col-md-6").removeClass("col-md-4");
            $("#idFond").addClass("col-md-6").removeClass("col-md-4");
        }
        if ($('#fondo_id').val() != '' && $('#fondo_id').val() != null) {
            dao.getMeses($('#area').val(), $('#fondo_id').val());
        }
    });

    if ($('#sel_actividad').val() != '' && $('#sel_fondo').val() != '' && $('#sel_actividad').val() != null && $('#sel_fondo').val() != null) {
        dao.getMeses($('#area').val(), $('#sel_fondo').val());
    }


    dao.getSelect();
    $("#ur_filter").select2({
        maximumSelectionLength: 10
    });
    $("#upp_filter").select2({
        maximumSelectionLength: 10
    });
    for (let i = 1; i <= 12; i++) {
        $("#" + i).val(0);
        $("#" + i).on('paste', function (e) {
            e.preventDefault();
        });
    }
    $('input[type=search]').attr('id', 'serchUr');
    $('#exampleModal').modal({
        backdrop: 'static',
        keyboard: false
    })
    $('#btnSave').click(function (e) {
        e.preventDefault();
        let flag = false;
        console.log($('#actividad_id').val());
        if ($('#actividad_id').val() == 'ot') {
            let nombre = $("#inputAc").val();
            let nuevo = nombre.trim();
           // let n=nombre.replace(/ /g, "")
            if (nuevo == "") {
                Swal.fire({
                    title: "Campo vacío",
                    text: "El campo nombre no puede ir vacío",
                    icon: "info"
                  });
                flag = false;
            } else {
                flag = true;
            }

        } else {
            flag = true;
        }
        if ($('#conmir').val()) {
            init.validateCreate($('#actividad'));
            if ($('#actividad').valid() && flag) {
               dao.crearMeta();
            }
        } else {
            init.validateCreateN($('#actividad'));
            if ($('#actividad').valid()) {
                dao.crearMeta();
            }

        }

    });
    $('#btnSaveM').click(function (e) {
        e.preventDefault();
        if ($('#formFile').valid()) {
            dao.crearMetaImp();
        }
    });
    $('#continua').modal({
        backdrop: 'static',
        keyboard: false
    });



});