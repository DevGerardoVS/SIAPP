const inputs = ['sel_actividad', 'sel_fondo', 'tipo_Ac', 'beneficiario', 'tipo_Be', 'medida'];
let clv_subprograma = null;
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
    optionTabs: function (id, anio, upp, ur) {
        console.log('optionTabs',upp);
        ur = ur != null ? ur : '0';
        upp = upp != null ? upp : '0';
        switch (id) {
            case 'metas-tab':
                $('.botones_exportar').attr("style", "display:none;");
                if (upp != 0 && upp != null) {
                    $('.CargaMasiva').removeAttr('style');
                    dao.getData(upp, ur, anio);
                }
                break;
            case 'capturadas-tab':
                dao.limpiarCrear();
                $('.CargaMasiva').attr("style", "display:none;");
                if (upp != 0 && upp != null) {
                    $('.botones_exportar').removeAttr('style');
                    dao.getDataCapturadas(upp, ur, anio);
                }
                break;
        }
    },
    getDataCapturadas: function (upp, ur, anio) {
        $.ajax({
            type: "GET",
            url: "/actividades/data/" + upp + "/" + ur + "/" + anio,
            dataType: "json"
        }).done(function (_data) {
            const { dataSet, confirmado } = _data;
            if (confirmado == 1) {
                $('.confirmacion').attr("style", "display:none;");
                $('#validMetas').addClass(" alert alert-danger").addClass("text-center");
                $('#validMetas').text("Las metas ya fueron confirmadas para la UPP: " + upp);
            } else {
                $('.botones_exportar').removeAttr('style');
                $('#validMetas').removeClass(" alert alert-danger").removeClass("text-center");
                $('#validMetas').text('');
            }
            _table = $("#proyectoM");
            _columns = [
                { "aTargets": [0], "mData": [0] },
                { "aTargets": [1], "mData": [1] },
                { "aTargets": [2], "mData": [2] },
                { "aTargets": [3], "mData": [3] },
                { "aTargets": [4], "mData": [4] },
                { "aTargets": [5], "mData": [5] },
                { "aTargets": [6], "mData": [6] },
                { "aTargets": [7], "mData": [7] },
                { "aTargets": [8], "mData": [8] },
                { "aTargets": [9], "mData": [9] },
                { "aTargets": [10], "mData": [10] },
                { "aTargets": [11], "mData": [11] },
                { "aTargets": [12], "mData": [12] },
                { "aTargets": [13], "mData": [13] },
                { "aTargets": [14], "mData": [14] },
                { "aTargets": [15], "mData": [15] },
                { "aTargets": [16], "mData": [16] },
                { "aTargets": [17], "mData": [17] },
                { "aTargets": [18], "mData": [18] },
                { "aTargets": [19], "mData": [19] }
            ];
            _height = '1px';
            _pagination = 15;
            _gen.setTableScrollFotter(_table, _columns, dataSet);
        });
    },
    getData: function (upp, ur, anio) {
        console.log('getData',{upp, ur, anio});
        $.ajax({
            type: "GET",
            url: "/calendarizacion/data/" + upp + "/" + ur+"/"+anio,
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        }).done(function (_data) {
            const { dataSet, response } = _data;
            console.log(response);
            if (!response.status) {
                $('#metasVista').attr('style', 'display: none;');
                $('#incomplete').removeAttr('style');
                $("#icono").addClass("fa fa-info-circle fa-5x d-flex justify-content-center");
                let text = response?.mensaje ? response.mensaje : response.text;
                Swal.fire({
                    icon: 'warning',
                    title: response?.title ? response.title : 'Información incompleta',
                    text: response.mensaje,
                    confirmButtonText: 'Aceptar',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (response?.estado && !response.estado) {
                            window.location.href = response.url
                        }
                    }

                });
                $('#texto').text(text);
            } else {
                $('#incomplete').attr('style', 'display: none;');
                $('#metasVista').removeAttr('style');
                $("#icono").removeClass("fa fa-info-circle fa-5x d-flex justify-content-center");
                $('#texto').text('');
                
            }
            if (response.confirmado == 1) {

                $('#validMetas').addClass(" alert alert-danger").addClass("text-center");
                $('#validMetas').text("Las metas ya fueron confirmadas para la UPP: " + upp);
            } else {
                $('#validMetas').removeClass(" alert alert-danger").removeClass("text-center");
                $('#validMetas').text('');
            }

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
                { "aTargets": [10], "mData": [10] },
                { "aTargets": [11], "mData": [11] },
                { "aTargets": [12], "mData": [12] }
                ]
            }
            ];
            _gen.setTableScrollFotter(_table, _columns,dataSet);  
        });
    },
    getUrs: function (anio, upp) {
        $('#ur_filter').empty();
        $("#ur_filter").removeAttr('disabled');
        $.ajax({
            type: "GET",
            url: '/calendarizacion/urs/' + anio + '/' + upp,
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        }).done(function (data) {
            console.log(data);
            const { urs } = data;
            var par = $('#ur_filter');
            par.html('');
            par.append(new Option('TODAS LAS UR',0,true,true));
            $.each(urs, function (i, val) {
                if (i==0) {
                    par.append(new Option(urs[0].ur,urs[0].clv_ur,true,true));
                } else {
                    par.append(new Option(val.ur, val.clv_ur));
                }
             
            });
        });
    },
    getUpps: function (anio) {
        $.ajax({
            type: "GET",
            url: '/calendarizacion/upps/' + anio,
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        }).done(function (data) {
            const { upp } = data;
            var par = $('#upp_filter');
            par.html('');
            if (upp.length>2) {
                par.append(new Option("Seleccione una UPP", "upp"));
                document.getElementById("upp_filter").options[0].disabled = true;
            }
            $.each(upp, function (i, val) {
                par.append(new Option(val.upp, val.clv_upp));
            });
            if (upp.length == 1) {
                let clv_upp = upp[0].clv_upp;
                $("#upp_filter option[value=" + clv_upp + "]").attr("selected", true);
                dao.getUrs(anio,clv_upp);
            }
        });
    },
    limpiarCrear: function () {
       $('#tableMetas').find('#sumMetas').val('');
        $('#tableMetas').find('#sumMetas-error').removeClass('has-error');
        $('#tableMetas').find('#sumMetas-error').text('');
        inputs.forEach(e => {
           $('#tableMetas').find('#' + e + '-error').text("").removeClass('#' + e + '-error');
            if (e != 'beneficiario') {
              $('#tableMetas').find('#' + e).selectpicker('destroy');
            }
        });
        dao.getSelect('crear');
        $('#tableMetas').find('.form-group').removeClass('has-error');
        for (let i = 1; i <= 12; i++) {
            $('#tableMetas').find('#' + i).val(0);
        }
       $('#tableMetas').find('#beneficiario').val("");
        for (let i = 1; i <= 12; i++) {
           $('#tableMetas').find("#" + i).prop('disabled', true);
        }
        $('#tableMetas').find('#inputAc').val('');
        $('#tableMetas').find('#tipo_Ac').empty();
        $('#tableMetas').find('#sel_fondo').empty();
        $('#tableMetas').find('#sel_fondo').append("<option value=''class='text-center' ><b>-- Fondos--</b></option>");
        $('#tableMetas').find('#actividad_id').empty();
        $('#tableMetas').find('#actividad_id').append("<option value=''class='text-center' ><b>-- Actividad--</b></option>");
        dao.limpiarErrors();
    },
    getSelect: function (view) {
        let viewBlade = '';
        switch (view) {
            case 'crear':
                viewBlade = 'tableMetas';
                break;
            case 'editar':
                viewBlade = 'addActividad';
                break;
        }
        if (clv_subprograma != null) {
           $('#'+viewBlade).find('#medida').empty();
           $('#'+viewBlade).find('#tipo_Be').empty();
            $.ajax({
                type: "GET",
                url: '/calendarizacion/selects',
                dataType: "JSON",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
            }).done(function (data) {
                const { unidadM, beneficiario } = data;
                var med = $('#'+viewBlade).find('#medida');
                med.html('');
                var tipo_be =$('#'+viewBlade).find('#tipo_Be');
                tipo_be.html('');
                if (clv_subprograma == 'UUU') {
                    med.append(new Option("Pago de nómina", 829, true, true));
                    tipo_be.append(new Option("Empleados", 12, true, true));
                } else {
                    med.append(new Option(" U. Medida", "", true, true));
                    $('#' + viewBlade).find("#medida option:selected").attr('disabled', true);
                    $.each(unidadM, function (i, val) {
                        med.append(new Option(val.unidad_medida, val.clave));
                    });
                    tipo_be.append(new Option(" Beneficiarios", ""));
                    $('#'+viewBlade).find("#tipo_Be option:selected").attr('disabled', true);
                    $.each(beneficiario, function (i, val) {
                        tipo_be.append(new Option(beneficiario[i].beneficiario, beneficiario[i].id));
                    });
                }
            });
        }
    },
    limpiarErrors: function () {
        $('#tableMetas').find("#meses-error").text("").removeClass('has-error');
        $('#tableMetas').find("#conmir").val(true);
        $('#tableMetas').find('#actividad_id').attr('disabled', 'disabled');
        $('#tableMetas').find(".inputAc").hide().removeClass('has-error');
        $('#tableMetas').find("#medida-error").text("").removeClass('has-error');
        $('#tableMetas').find("#inputAc-error").text("").removeClass('has-error');
        $('#tableMetas').find("#tipo_Be-error").text("").removeClass('has-error');
        $('#tableMetas').find("#beneficiario-error").text("").removeClass('has-error');
        $('#tableMetas').find("#tipo_Ac-error").text("").removeClass('has-error');
        $('#tableMetas').find("#fondo_id-error").text("").removeClass('has-error');
        $('#tableMetas').find("#actividad_id-error").text("").removeClass('has-error');
        $('#tableMetas').find("#sel_fondo-error").text("").removeClass('has-error');
        $('#tableMetas').find("#sel_actividad-error").text("").removeClass('has-error');
        $('#tableMetas').find('#sumMetas-error').text("").removeClass('has-error');
        $('#tableMetas').find("#idAct").addClass("col-md-6").removeClass("col-md-4");
        $('#tableMetas').find("#idFond").addClass("col-md-6").removeClass("col-md-4");
        $('#tableMetas').find('#sel_actividad').val('');
        $('#tableMetas').find('#fondo_id').val('');
        $('#tableMetas').find("#inputAc").val('');
        $('#tableMetas').find("#sel_fondo").val('');
        $('#tableMetas').find("#beneficiario").val('');
        $('#tableMetas').find('#tableMetas').find("#sumMetas").val('');
        $('#tableMetas').find('#addActividad').find("#sumMetas").val('');
        $('#tableMetas').find("#sel_fondo").empty('');
        $('#tableMetas').find('#fondo_id').empty('');
        $('#tableMetas').find('#actividad_id').empty();
        $('#tableMetas').find('#sel_actividad').empty();
        $('#tableMetas').find('.form-group').removeClass('has-error');
    },
    newGetFyA: function (area, enti) {
        dao.limpiarCrear();
        for (let i = 1; i <= 12; i++) {
            $('#tableMetas').find("#" + i).val(0);
            $('#tableMetas').find("#" + i).prop('disabled', true);
        }
        $('#tableMetas').find("#sumMetas").val('');
        clv_subprograma = area.substring(13, 10);
        console.log(clv_subprograma);
        $("#activiMir").val(`${area}$${enti}`);
        let ar = area.split('-'); 
        $("#calendar").val(ar[8]); 
        let clave = `${area}$${enti}$${$('#anio_filter').val()}`;
        $("#area").val(clave);
        $("#sel_fondo").removeAttr('disabled');
        $.ajax({
            type: "GET",
            url: '/calendarizacion/fondos/' + area + '/' + enti+'/'+$('#upp_filter').val()+'/'+$('#anio_filter').val(),
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        }).done(function (data) {
            dao.getSelect('crear');
            console.log(data);
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
                let fondo = '';
                if ($('#sel_fondo').val() != '' || $('#sel_fondo').val() != null) {
                    fondo = $('#sel_fondo').val();
                } else {
                    fondo = $('#fondo_id').val();
                }
                dao.getMeses(clave, fondo,'tableMetas');
                dao.getActividasdesMir(fondo)
            }
            $('#tableMetas').find("#tipo_Ac").removeAttr('disabled');
            var tipo_AC = $('#tableMetas').find('#tipo_Ac');
            tipo_AC.html('');
            let tm =Object.keys(tAct).length;
            if (tAct.Acumulativa == 0 && $('#calendar').val()!='UUU') {
                tipo_AC.append(new Option("Tipo Actividad", ""));
                $('#tableMetas').find("#tipo_Ac option:selected").attr('disabled', true);
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
             url: '/actividades/metas/actividades-mir/' + mir[0] + '/' + mir[1]+'/'+fondo+'/'+$("#anio_filter").val(),
             dataType: "JSON",
             headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
         }).done(function (data) {
             console.log(data);
             const { con_mir, activids,tipoAc } = data;
             let conmir = con_mir == 1 ? true : false
             
             $("#conmir").val(conmir);
                 $("#tipoAct").val(tipoAc);
                 var act = $('#actividad_id');
                 act.html('');
                 if (activids.length == 2) {
                     act.append(new Option("Actividad", "true", true, true));
                     document.getElementById("actividad_id").options[0].disabled = true;
                 }
                 $.each(activids, function (i, val) {
                     act.append(new Option(val.actividad, val.id));
                 });
             
             if ($("#actividad_id").val() == 'ot') {
                 $("#conmir").val(false);
                 $("#tipo_Ac").removeAttr('disabled');
                         $("#inputAc").removeAttr('disabled');
                         $('#actividad_id').prop('disabled', false);
                         $(".inputAc").show(); 
                         $(".fondodiv").removeClass("col-md-6").addClass("col-md-4"); 
                         $(".actividaddiv").removeClass("col-md-6").addClass("col-md-4"); 
                         $(".acOt").show();
             } else {
                 $("#actividad_id").removeAttr('disabled');
                 $("#tipo_Ac").removeAttr('disabled');
                         $(".acOt").hide();
                         $(".inputAc").val('');
                         $("#inputAc").attr('disabled', 'disabled');
                         $(".inputAc").hide();
                         $(".fondodiv").removeClass("col-md-4").addClass("col-md-6");
                         $(".actividaddiv").removeClass("col-md-4").addClass("col-md-6"); 
 
                     }
         });
    },
    getMeses: function (idA, idF,viewBlade) {
       
        for (const key in mesesV) {
            if (Object.hasOwnProperty.call(mesesV, key)) {
                mesesV[key] = false;
            }
        }


        $.ajax({
            type: "GET",
            url: '/actividades/meses-activos/' + idA + "/" + idF+"/"+$('#anio_filter').val(),
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        }).done(function (data) {
            let { mese } = data;
            if (clv_subprograma != 'UUU') {
             
                for (const key in mese) {
                    if (Object.hasOwnProperty.call(mese, key)) {
                        const e = mese[key];
                        switch (key) {
                            case 'enero':
                                if (e != 0.0 || e != 0) {
                                    mesesV.enero = true;
                                    $('#'+viewBlade).find("#1").prop('disabled', false);
                                    $('#'+viewBlade).find("#1").prop('required', true);
                                } else {
                                $('#'+viewBlade).find("#1").prop('disabled', 'disabled');
                                }
                                break;
                            case 'febrero':
                                if (e != 0.0 || e != 0) {
                                    mesesV.febrero = true;
                                    $('#'+viewBlade).find("#2").prop('disabled', false);
                                    $('#'+viewBlade).find("#2").prop('required', true);
                                } else {
                                    $('#'+viewBlade).find("#2").prop('disabled', 'disabled');
                                }
                                break;
                            case 'marzo':
                                if (e != 0.0 || e != 0) {
                                    mesesV.marzo = true;
                                    $('#'+viewBlade).find("#3").prop('disabled', false);
                                    $('#'+viewBlade).find("#3").prop('required', true);
                                } else {
                                    $('#'+viewBlade).find("#3").prop('disabled', 'disabled');

                                }
                                break;
                            case 'abril':
                                if (e != 0.0 || e != 0) {
                                    mesesV.abril = true;
                                    $('#'+viewBlade).find("#4").prop('disabled', false);
                                    $('#'+viewBlade).find("#4").prop('required', true);
                                } else {
                                    $('#'+viewBlade).find("#4").prop('disabled', 'disabled');
                                }
                                break;
                            case 'mayo':
                                if (e != 0.0 || e != 0) {
                                    mesesV.mayo = true;
                                    $('#'+viewBlade).find("#5").prop('disabled', false);
                                    $('#'+viewBlade).find("#5").prop('required', true);
                                } else {
                                    $('#'+viewBlade).find("#5").prop('disabled', 'disabled');
                                }
                                break;
                            case 'junio':
                                if (e != 0.0 || e != 0) {
                                    mesesV.junio = true;
                                    $('#'+viewBlade).find("#6").prop('disabled', false);
                                    $('#'+viewBlade).find("#6").prop('required', true);
                                } else {
                                    $('#'+viewBlade).find("#6").prop('disabled', 'disabled');
                                }
                                break;
                            case 'julio':
                                if (e != 0.0 || e != 0) {
                                    mesesV.julio = true;
                                    $('#'+viewBlade).find("#7").prop('disabled', false);
                                    $('#'+viewBlade).find("#7").prop('required', true);
                                } else {
                                    $('#'+viewBlade).find("#7").prop('disabled', 'disabled');
                                }
                                break;
                            case 'agosto':
                                if (e != 0.0 || e != 0) {
                                    mesesV.agosto = true;
                                    $('#'+viewBlade).find("#8").prop('disabled', false);
                                    $('#'+viewBlade).find("#8").prop('required', true);
                                } else {
                                    $('#'+viewBlade).find("#8").prop('disabled', 'disabled');
                                }
                                break;
                            case 'septiembre':
                                if (e != 0.0 || e != 0) {
                                    mesesV.septiembre = true;
                                    $('#'+viewBlade).find("#9").prop('disabled', false);
                                    $('#'+viewBlade).find("#9").prop('required', true);
                                } else {
                                    $('#'+viewBlade).find("#9").prop('disabled', 'disabled');
                                }
                                break;
                            case 'octubre':
                                if (e != 0.0 || e != 0) {
                                    mesesV.octubre = true;
                                    $('#'+viewBlade).find("#10").prop('disabled', false);
                                    $('#'+viewBlade).find("#10").prop('required', true);
                                } else {
                                    $('#'+viewBlade).find("#10").prop('disabled', 'disabled');
                                }
                                break;
                            case 'noviembre':
                                if (e != 0.0 || e != 0) {
                                    mesesV.noviembre = true;
                                    $('#'+viewBlade).find("#11").prop('disabled', false);
                                    $('#'+viewBlade).find("#11").prop('required', true);
                                } else {
                                    $('#'+viewBlade).find("#11").prop('disabled', 'disabled');
                                }
                                break;
                            case 'diciembre':

                                if (e != 0.0 || e != 0) {
                                    mesesV.diciembre = true;
                                $('#'+viewBlade).find("#12").prop('disabled', false);
                                $('#'+viewBlade).find("#12").prop('required', true);
                                    console.log('diciembre hay baro');
                                } else {
                                $('#'+viewBlade).find("#12").prop('disabled', 'disabled');
                                }
                                break;

                            default:
                                break;
                        }

                    }
                }
            } else {
                console.log('es triple U');
                for (let i = 1; i <= 11; i++) {
                    $('#'+viewBlade).find("#" + i).val(2);
                    $('#'+viewBlade).find("#" + i).prop('disabled', 'disabled');
                }
                $('#'+viewBlade).find("#12").val(3);
                $('#'+viewBlade).find("#12").prop('disabled', 'disabled');
                $('#'+viewBlade).find("#sumMetas").val(25);
                $('#'+viewBlade).find("#sumMetas").prop('disabled', 'disabled')


            }
        });
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
    validateAcu: function (view) {
        let e = 0;
        for (let i = 1; i <= 12; i++) {
            let mes = $('#' + view).find('#' + i).val();
            let suma = parseInt(mes != "" ? mes : 0);
            e += suma;
        }
        return e;
    },
    validatEspe: function (view) {
        let e = [];
        for (let i = 1; i <= 12; i++) {
            let mes = $('#' + view).find('#' + i).val();
            let suma = parseInt(mes != "" ? mes : 0);
            e.push(suma)
        }
        return Math.max(...e);
    },
    validatCont: function (view) {
        let e = [];
        for (let i = 1; i <= 12; i++) {
            let mes = $('#' + view).find('#' + i).val();
            if (mes != "") {
                let suma = parseInt(mes);
                if (mes != 0 && mes != "" && mes != "null" && mes != null) {
                    e.push(suma);
                }

            }
        }
        if (dao.arrEquals(e)) {
            return e[0];
        } else {
            $('#' + view).find('#sumMetas').val("");
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
    sumar: function (view) {
        let actividad = $('#'+view).find("#tipo_Ac option:selected").text();
        switch (actividad) {
            case 'Acumulativa':
                $('#'+view).find('#sumMetas').val(dao.validateAcu(view) != 0 ? dao.validateAcu(view) : '');
                break;
            case 'Continua':
                $('#'+view).find('#sumMetas').val(dao.validatCont(view) != 0 ? dao.validatCont(view) : '');
                break;
            case 'Especial':
                $('#'+view).find('#sumMetas').val(dao.validatEspe(view) != 0 ? dao.validatEspe(view) : '');
                break;
        }
    },
    getPlantillaCmUpp: function () {
        let upp = $('#upp').val() != '' ? $('#upp').val() : $('#upp_filter').val();
        const url = "/actividades/proyecto_calendario/" + upp;
        window.location.href = url;
    },
    crearMeta: function () {
        var form = $('#createMeta')[0];
        var data = new FormData(form);
        data.append('sumMetas', $('#tableMetas').find('#sumMetas').val());
        data.append('upp', $('#upp_filter').val());
        data.append('anio', $('#anio_filter').val());
        let aOld = $('#area').val()
        let area = aOld.replace('$', '/')
        data.append('area', area);
        data.append('conmir', $("#conmir").val());
        data.append('tipoAct', $("#tipoAct").val());
        data.append('tipo_Ac', $('#tipo_Ac').val());
        if ($('#tipo_Ac').val() == 'Continua') {
            let index = 1;
            for (const key in mesesV) {
                if (Object.prototype.hasOwnProperty.call(mesesV, key)) {
                    if (Object.values(mesesV[key])) {
                        data.append(index, contValue);
                    }
                }
                index++;
            }

/*             if (mesesV.enero) {
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
                data.append(11, contValue);
            } 
            if (mesesV.diciembre) {
                data.append(12, contValue);
            }  */

            data.append('sumMetas', contValue);
        } else {
            for (let i = 1; i < 13; i++) {
                data.append(i,$('#tableMetas').find('#'+i).val());
                
            }
            data.append('sumMetas', $('#tableMetas').find('#sumMetas').val());
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
            let id = $(".active")[0].id;
            let anio = $("#anio_filter").val();
            let upp = $("#upp_filter").val();
            let ur = $("#ur_filter").val();
            dao.optionTabs(id, anio, upp, ur);
            dao.limpiarCrear();
        });
    },
    limpiarUpdate: function () {
        $('#addActividad').find("#meses-error").text("").removeClass('has-error');
        inputs.forEach(e => {
            $('#addActividad').find('#' + e + '-error').text("").removeClass('#' + e + '-error');
            if (e != 'beneficiario') {
                $('#addActividad').find('#' + e).selectpicker('destroy');
            }
        });
        dao.getSelect('editar');
        $('#addActividad').find('.form-group').removeClass('has-error');
        for (let i = 1; i <= 12; i++) {
            $('#addActividad').find('#' + i).val(0);
        }
        $('#addActividad').find('#beneficiario').val("");
        for (let i = 1; i <= 12; i++) {
            $('#addActividad').find("#" + i).prop('disabled', true);
        }
        $('#addActividad').find('#sumMetas').val('');
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
                text: response.text,
                showCancelButton: false,
                confirmButtonColor: "#3085d6",
                confirmButtonText: "Aceptar"
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
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
    getActiv: function (upp,sub) {
       $('#addActividad').find("#tipo_Ac").empty();
        $.ajax({
            type: "GET",
            url: '/calendarizacion/tcalendario/'+upp,
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        }).done(function (data) {       
            if (Object.keys(data).length == 2&& sub!='UUU') {
                $('#addActividad').find("#tipo_Ac").append(new Option("--Tipo Actividad--", ""));
                $('#addActividad').find("#tipo_Ac option:selected").attr('disabled', true);

            }
            if (sub=='UUU') {
              $('#addActividad').find("#tipo_Ac").append(new Option('Acumulativa', 'Acumulativa',true,true));
            } else {
                $.each(data, function (i, val) {
                    if (val == 1) {
                       $('#addActividad').find('#tipo_Ac').append("<option value='" + i + "'>" +i+"</option>");
                    }
                });
            }

        });
    },
    editarMeta: function (id) {
        $("#addActividad").modal("show");
        $.ajax({
            type: "GET",
            url: "/calendarizacion/update/" + id,
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        }).done(function (data) {
            clv_subprograma = data.subprograma;
            dao.getActiv(data.clv_upp, data.subprograma); 
            dao.getSelect('editar');
            $('#subp').val(data.subprograma);
            $('#proyectoMD').empty();
            $('#proyectoMD').append("<thead><tr class='colorRosa'>"
                + "<th class= 'vertical' > UPP</th >"
                + "<th class='vertical'>UR</th>"
                + "<th class='vertical'>Programa</th>"
                + "<th class='vertical'>Subprograma</th>"
                + "<th class='vertical'>Proyecto</th>"
                + "<th class='vertical'>Fondo</th>"
                + "<th class='vertical'>Actividad</th>"
                + "</tr>thead");
            $('#proyectoMD').append('<tbody class="text-center"><tr>'
                + '<th scope="row">' + data.clv_upp + '</th> <th>  '
                + data.clv_ur + '</th> <th>' + data.clv_programa
                + '</th><th>' + data.subprograma + '</th><th>'
                + data.proyecto + '</th><th>' + data.clv_fondo
                + '</th><th>' + data.actividad + '</th>' +
                '</tr></tbody>');
            $('#id_meta').text(data.id);
            $('#Nactividad').text(data.actividad);
            $('#Nfondo').text(data.clv_fondo);
            $('#addActividad').find('#beneficiario').val(data.cantidad_beneficiarios);
            $('#addActividad').find("#tipo_Be option[value='" + data.beneficiario_id + "']").attr("selected", true);
            $('#addActividad').find("#medida option[value='" + data.unidad_medida_id + "']").attr("selected", true);
            $('#addActividad').find("#tipo_Ac option[value='"+ data.tipo +"']").attr("selected",true);
            $('#addActividad').find('#1').val(data.enero);
            $('#addActividad').find('#2').val(data.febrero);
            $('#addActividad').find('#3').val(data.marzo);
            $('#addActividad').find('#4').val(data.abril);
            $('#addActividad').find('#5').val(data.mayo);
            $('#addActividad').find('#6').val(data.junio);
            $('#addActividad').find('#7').val(data.julio);
            $('#addActividad').find('#8').val(data.agosto);
            $('#addActividad').find('#9').val(data.septiembre);
            $('#addActividad').find('#10').val(data.octubre);
            $('#addActividad').find('#11').val(data.noviembre);
            $('#addActividad').find('#12').val(data.diciembre);
            $('#addActividad').find('#sumMetas').val(data.total);
            $('#ar').val(data.ar);
            $('#fondo').val(data.clv_fondo);
            let edit = false;
            const mese = data.meses;
            for (const key in mesesV) {
                if (Object.hasOwnProperty.call(mesesV, key)) {
                    mesesV[key]=false;
                }
            }
            if ( data.subprograma!='UUU') {
                for (const key in mese) {
                    if (Object.hasOwnProperty.call(mese, key)) {
                        const e = mese[key];
                        switch (key) {
                            case 'enero':
                                if (e != 0.0 || e != 0) {
                                    mesesV.enero = true;
                                    $('#addActividad').find("#1").prop('disabled', false);
                                    $('#addActividad').find("#1").prop('required', true);
                                } else {
                                    if (data.enero !=0) {
                                        $('#addActividad').find('#1').val(0);
                                        let sumE = parseInt($('#addActividad').find('#sumMetas').val());
                                        $('#addActividad').find('#sumMetas').val(sumE -data.enero);
                                    }
                                    $('#addActividad').find("#1").prop('disabled', 'disabled');
                                }
                                break;
                            case 'febrero':
                                if (e != 0.0 || e != 0) {
                                    mesesV.febrero = true;
                                    $('#addActividad').find("#2").prop('disabled', false);
                                    $('#addActividad').find("#2").prop('required', true);
                                } else {
                                    if (data.febrero !=0) {
                                        $('#addActividad').find('#2').val(0);
                                        let sumF = parseInt($('#addActividad').find('#sumMetas').val());
                                        $('#addActividad').find('#sumMetas').val(sumF -data.febrero);
                                    }
                                    $('#addActividad').find("#2").prop('disabled', 'disabled');
                                }
                                break;
                            case 'marzo':
                                if (e != 0.0 || e != 0) {
                                    mesesV.marzo = true;
                                    $('#addActividad').find("#3").prop('disabled', false);
                                    $('#addActividad').find("#3").prop('required', true);
                                } else {
                                    if (data.marzo !=0) {
                                        $('#addActividad').find('#3').val(0);
                                        let sumM = parseInt($('#addActividad').find('#sumMetas').val());
                                $('#addActividad').find('#sumMetas').val(sumM-data.marzo);
                                    }
                                    $('#addActividad').find("#3").prop('disabled', 'disabled');

                                }
                                break;
                            case 'abril':
                                if (e != 0.0 || e != 0) {
                                    mesesV.abril = true;
                                    $('#addActividad').find("#4").prop('disabled', false);
                                    $('#addActividad').find("#4").prop('required', true);
                                } else {
                                    if (data.abril !=0) {
                                        $('#addActividad').find('#4').val(0);
                                        let sumA = parseInt($('#addActividad').find('#sumMetas').val());
                                        $('#addActividad').find('#sumMetas').val(sumA -data.abril);
                                    }
                                    $('#addActividad').find("#4").prop('disabled', 'disabled');
                                }
                                break;
                            case 'mayo':
                                if (e != 0.0 || e != 0) {
                                    mesesV.mayo = true;
                                    $('#addActividad').find("#5").prop('disabled', false);
                                    $('#addActividad').find("#5").prop('required', true);
                                } else {
                                    if (data.mayo !=0) {
                                        $('#addActividad').find('#5').val(0);
                                        let sumMY = parseInt($('#addActividad').find('#sumMetas').val());
                                        $('#addActividad').find('#sumMetas').val(sumMY-data.mayo);
                                    }
                                    $('#addActividad').find("#5").prop('disabled', 'disabled');
                                }
                                break;
                            case 'junio':
                                if (e != 0.0 || e != 0) {
                                    mesesV.junio = true;
                                $('#addActividad').find("#6").prop('disabled', false);
                                $('#addActividad').find("#6").prop('required', true);
                                } else {
                                    if (data.junio !=0) {
                                        $('#addActividad').find('#6').val(0);
                                        let sumJ = parseInt($('#addActividad').find('#sumMetas').val());
                                        $('#addActividad').find('#sumMetas').val(sumJ-data.junio);
                                    }
                                    $('#addActividad').find("#6").prop('disabled', 'disabled');
                                }
                                break;
                            case 'julio':
                                if (e != 0.0 || e != 0) {
                                    mesesV.julio = true;
                                    $('#addActividad').find("#7").prop('disabled', false);
                                    $('#addActividad').find("#7").prop('required', true);
                                } else {
                                    if (data.julio !=0) {
                                        $('#addActividad').find('#7').val(0);
                                        let sumJJ = parseInt($('#addActividad').find('#sumMetas').val());
                                        $('#addActividad').find('#sumMetas').val(sumJJ-data.julio);
                                    }
                                    $('#addActividad').find("#7").prop('disabled', 'disabled');
                                }
                                break;
                            case 'agosto':
                                if (e != 0.0 || e != 0) {
                                    mesesV.agosto = true;
                                    $('#addActividad').find("#8").prop('disabled', false);
                                    $('#addActividad').find("#8").prop('required', true);
                                } else {
                                    if (data.agosto !=0) {
                                        $('#addActividad').find('#8').val(0);
                                        let sumAG = parseInt($('#addActividad').find('#sumMetas').val());
                                        $('#addActividad').find('#sumMetas').val(sumAG-data.agosto);
                                    }
                                    $('#addActividad').find("#8").prop('disabled', 'disabled');
                                }
                                break;
                            case 'septiembre':
                                if (e != 0.0 || e != 0) {
                                    mesesV.septiembre = true;
                                    $('#addActividad').find("#9").prop('disabled', false);
                                    $('#addActividad').find("#9").prop('required', true);
                                } else {
                                    if (data.septiembre !=0) {
                                        $('#addActividad').find('#9').val(0);
                                        let sumS = parseInt($('#addActividad').find('#sumMetas').val());
                                        $('#addActividad').find('#sumMetas').val(sumS-data.septiembre);
                                    }
                                    $('#addActividad').find("#9").prop('disabled', 'disabled');
                                }
                                break;
                            case 'octubre':
                                if (e != 0.0 || e != 0) {
                                    mesesV.octubre = true;
                                    $('#addActividad').find("#10").prop('disabled', false);
                                    $('#addActividad').find("#10").prop('required', true);
                                } else {
                                    if (data.octubre !=0) {
                                        $('#10').val(0);
                                        let sumO = parseInt($('#addActividad').find('#sumMetas').val());
                                        $('#addActividad').find('#sumMetas').val(sumO-data.octubre);
                                    }
                                    $('#addActividad').find("#10").prop('disabled', 'disabled');
                                }
                                break;
                            case 'noviembre':
                                if (e != 0.0 || e != 0) {
                                    mesesV.noviembre = true;
                                    $('#addActividad').find("#11").prop('disabled', false);
                                    $('#addActividad').find("#11").prop('required', true);
                                } else {
                                    if (data.noviembre !=0) {
                                        $('#addActividad').find('#11').val(0);
                                        let sumN = parseInt($('#addActividad').find('#sumMetas').val());
                                        $('#addActividad').find('#sumMetas').val(sumN-data.noviembre);
                                    }
                                    $("#11").prop('disabled', 'disabled');
                                }
                                break;
                            case 'diciembre':
                                if (e != 0.0 || e != 0) {
                                    mesesV.diciembre = true;
                                    $('#addActividad').find("#12").prop('disabled', false);
                                    $('#addActividad').find("#12").prop('required', true);
                                } else {
                                    if (data.diciembre !=0) {
                                        $('#12').val(0);
                                        let sumD = parseInt($('#addActividad').find('#sumMetas').val());
                                       $('#addActividad').find('#sumMetas').val(sumD-data.diciembre);
                                    }
                                    $('#addActividad').find("#12").prop('disabled', 'disabled');
                                }
                                break;
                        }
                    
                    }
                }
            } else {
                $('#addActividad').find("#sumMetas").prop('disabled', 'disabled');
                for (let i = 1; i <= 12; i++) {
                    $('#addActividad').find("#" + i).prop('disabled', 'disabled');
                }
            }
            if (edit) {
                $('#editMetas').addClass(" alert alert-danger").addClass("text-center");
                $('#editMetas').text("Las claves presupuestarias fueron modificadas y es posible que un mes no tenga presupuesto lo cual fue modificado a CERO");
            }
        });
    },
    editarPutMeta: function () {
        var form = $('#actividad')[0];
        var data = new FormData(form);
        if ($('#subp').val() == 'UUU') {
            data.append('subp', 'UUU');
        }
        data.append('sumMetas', $('#addActividad').find('#sumMetas').val());
        $.ajax({
            type: "POST",
            url: '/calendarizacion/put',
            data: data,
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000
        }).done(function (response) {
            dao.limpiarUpdate();
            const {mensaje } = response;
            Swal.fire({
                icon: mensaje.icon,
                title: mensaje.title,
                text: mensaje.text,
            });
            $('#addActividad').find('#cerrarUpdate').trigger('click');
            let id = $(".active")[0].id;
            let anio = $("#anio_filter").val();
            let upp = $("#upp_filter").val();
            let ur = $("#ur_filter").val();
            dao.optionTabs(id, anio, upp, ur);
        });
    },
    eliminar: function (id) {
        Swal.fire({
            title: '¿Seguro que quieres eliminar este registro?',
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
                    const { mensaje } = data;
                    Swal.fire({
                        icon: mensaje.icon,
                        title: mensaje.title,
                        text: mensaje.text,
                    });
                    let id = $(".active")[0].id;
                    let anio = $("#anio_filter").val();
                    let upp = $("#upp_filter").val();
                    let ur = $("#ur_filter").val();
                    dao.optionTabs(id, anio, upp, ur);
                
                });
            }
        });
    },
    exportJasper: function () {
        let tipo = 0;
        let upp;
        if ($('#upp').val() == '') {
            upp = $('#upp_filter').val();
        } else {
            upp = $('#upp').val();
        }
        let anio = $('#anio_filter').val();
        Swal.fire({
            title: 'Eliga que tipo de firma desea.',
            icon: 'info',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'E-firma',
            denyButtonText: `Autografa`,
            denyButtonColor:'#8CD4F5',
          }).then((result) => {
            if (result.isConfirmed) {
                tipo = 1;
                $.ajax({
                    type: 'get',
                    url: "/actividades/jasper/" + upp + "/" + anio + "/" + tipo,
                    dataType: "JSON",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                }).done(function (params) {
                    document.getElementById('tipoReporte').value = 1;
                    $('#firmaModal').modal('show');
                });
            } else if (result.isDenied) {
                let url = "/actividades/jasper/" + upp + "/" + anio + "/" + tipo;
                window.location.href = url; 
            }
          })
        
    },
    exportJasperMetas: function () {
        let upp;
        if ($('#upp').val() == '') {
            upp = $('#upp_filter').val();
        } else {
            upp = $('#upp').val();
        }
        let anio = $('#anio_filter').val();
        let tipo = 0;
        Swal.fire({
            title: 'Eliga que tipo de firma desea.',
            icon: 'info',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'E-firma',
            denyButtonText: `Autografa`,
            denyButtonColor:'#8CD4F5',
          }).then((result) => {
            if (result.isConfirmed) {
                tipo = 1;
                $.ajax({
                    type: 'get',
                    url: "/actividades/jasper-metas/" + upp + "/" + anio+ "/" + tipo,
                    dataType: "JSON",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                }).done(function (params) {
                    document.getElementById('tipoReporte').value = 2;
                    $('#firmaModal').modal('show');
                });
            } else if (result.isDenied) {
                let url =  "/actividades/jasper-metas/" + upp + "/" + anio+ "/" + tipo;
                window.location.href = url; 
            }
          })
        
    },
    exportExcel: function () {
        _url = "/actividades/exportExcel/" + $('#upp_filter').val() + "/"+$('#ur_filter').val() +"/"+ $('#anio_filter').val();
        window.open(_url, '_blank');
    },
    exportPdf: function () {
        _url = "/actividades/exportPdf/" + $('#upp_filter').val() + "/"+$('#ur_filter').val() +"/"+ $('#anio_filter').val();
        window.open(_url, '_blank');
    },
    ConfirmarMetas: function () {
        Swal.fire({
            icon: 'question',
            title: '¿Estás seguro que quieres confirmar las metas?',
            showDenyButton: true,
            confirmButtonText: 'Confirmar',
            denyButtonText: `Cancelar`,
          }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "GET",
                    url: '/actividades/confirmar-metas/'+$('#upp').val()+"/"+$('#anio_filter').val(),
                    dataType: "JSON",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                }).done(function (data) {
                    const { mensaje } = data;
                    Swal.fire({
                        icon: mensaje.icon,
                        title: mensaje.title,
                        text: mensaje.text,
                        footer: mensaje?.footer,
                    });
                    let id = $(".active")[0].id;
                    let anio = $("#anio_filter").val();
                    let upp = $("#upp_filter").val();
                    let ur = $("#ur_filter").val();
                    dao.optionTabs(id, anio, upp, ur);
                });
            }
          })
       
    },
    firmarReporte : function () {
        let timerInterval
        Swal.fire({
          title: 'Preparando',
          html: 'Espere un momento',
          timer: 5000,
          timerProgressBar: true,
          didOpen: () => {
            Swal.showLoading()
            const b = Swal.getHtmlContainer().querySelector('b')
            timerInterval = setInterval(() => {
              b.textContent = Swal.getTimerLeft()
            }, 100)
          },
          willClose: () => {
            clearInterval(timerInterval)
          }
        }).then(() => {
            var form = $('#frm_eFirma')[0];
            var data = new FormData(form);
            $.ajax({
                type: "POST",
                url: '/calendarizacion-metas-reporte',
                data: data,
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false,
                cache: false,
            }).done(function (params) {
            if (params.estatus == 'done') {
                const containerFile = document.querySelector('#containerFile');
                const tempLink = document.createElement('a');
                tempLink.href = `data:application/pdf;base64,${params.data}`;
                tempLink.setAttribute('download', 'Reporte_Calendario_UPP.pdf');
                tempLink.click();
                dao.limpiarFormFirma();
            }else{
                Swal.fire(
                    'Error!',
                    'Hubo un problema al querer realizar la acción, contacte a soporte',
                    'Error'
                );
            }
            });
        });
    },
    limpiarFormFirma: function () {
        $('#firmaModal').modal('hide');
        document.getElementById("frm_eFirma").reset(); 
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
    validateFirmaE: function (form) {

        let rm =
        {
            rules: {
                cer: { required: true },
                key: { required: true },
            },
            messages: {
                cer: { required: "Este campo es requerido" },
                key: { required: "Este campo es requerido" },
            }
        }
        _gen.validate(form, rm);

    },
};
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $("#ur_filter").select2({
        maximumSelectionLength: 10
    });
    $("#upp_filter").select2({
        maximumSelectionLength: 10
    });
    $("#anio_filter").select2({
        maximumSelectionLength: 10
    });
    $("#beneficiario").on('paste', function (e) {
        e.preventDefault();
    });
    $("#sumMetas").on('paste', function (e) {
        e.preventDefault();
    });
    $('#exampleModal').modal({
        backdrop: 'static',
        keyboard: false
    });
    $('#continua').modal({
        backdrop: 'static',
        keyboard: false
    });
    dao.getUpps($("#anio_filter").val());
    let id = $(".active")[0].id;
    let anio = $("#anio_filter").val();
    let upp = $("#upp_filter").val();
    let ur = $("#ur_filter").val();
    dao.optionTabs(id, anio, upp, ur);

    $(".BorderNavPink").on("click", function () {
        let id = $(".active")[0].id;
        let anio = $("#anio_filter").val();
        let upp = $("#upp_filter").val();
        let ur = $("#ur_filter").val();
        dao.optionTabs(id, anio, upp, ur);
    });


    $('#anio_filter').change(() => {
        let id = $(".active")[0].id;
        let anio = $("#anio_filter").val();
        let upp = $("#upp_filter").val();
        let ur = $("#ur_filter").val();
        dao.optionTabs(id, anio, upp, ur);
    });
    $('#upp_filter').change(() => {
        $("#ur_filter").removeAttr('disabled');
        let id = $(".active")[0].id;
        let anio = $("#anio_filter").val();
        let upp = $("#upp_filter").val();
        dao.getUrs(anio, upp);
        let ur = $("#ur_filter").val();
       dao.optionTabs(id, anio, upp, ur);

    });
    $('#ur_filter').change(() => {
        console.log('click ur');
        let id = $(".active")[0].id;
        let anio = $("#anio_filter").val();
        let upp = $("#upp_filter").val();
        let ur = $("#ur_filter").val();
       dao.optionTabs(id, anio, upp, ur);
        $('#sel_fondo').empty();
        $('#sel_fondo').append("<option value=''class='text-center' ><b>-- Fondos--</b></option>");
    });
    $('#sel_fondo').change(() => {
        dao.getActividasdesMir($('#sel_fondo').val())
        dao.getMeses($('#area').val(), $('#sel_fondo').val(),'tableMetas');
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
        if ($('#sel_fondo').val() != '' && $('#sel_fondo').val() != null) {
            dao.getMeses($('#area').val(), $('#sel_fondo').val(),'tableMetas');
        }
    });
    for (let i = 1; i <= 12; i++) {
        $("#" + i).val(0);
        $("#" + i).on('paste', function (e) {
            e.preventDefault();
        });
    }
    $('input[type=search]').attr('id', 'serchUr');

    $('#btnSave').click(function (e) {
        e.preventDefault();
        let flag = false;
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
            init.validateCreate($('#createMeta'));
            if ($('#createMeta').valid() && flag) {
               dao.crearMeta();
            }
        } else {
            init.validateCreateN($('#createMeta'));
            if ($('#createMeta').valid()) {
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
    $("#cerrarUpdate").click(function(){
        $("#addActividad").modal('hide');
        dao.limpiarUpdate();
    });
    $("#cancelarUpdate").click(function(){
        $("#addActividad").modal('hide');
        dao.limpiarUpdate();
      });
    $('#btnUpdate').click(function (e) {
        e.preventDefault();
        if ($('#tipo_Ac').val() != 'Continua') {
            if ($('#actividad').valid()) {
                dao.editarPutMeta();
            }
        } else {
            if (dao.validatCont() != 0) {
                if ($('#actividad').valid()) {
                    dao.editarPutMeta();
                }
            }
        }
    });
    $('#btnSaveFirma').click(function (e) {
        init.validateFirmaE($('#frm_eFirma'));

        if ($('#frm_eFirma').valid()) {
            dao.firmarReporte();
        }

    });
});