const inputs = ['sel_actividad', 'sel_fondo', 'tipo_Ac', 'beneficiario', 'tipo_Be', 'medida'];
let mesesV = {
    enero:false,
    febrero:false,
    marzo:false,
    abril:false,
    mayo:false,
    junio:false,
    julio:false,
    agosto:false,
    septiembre:false,
    octubre:false,
    noviembre:false,
    diciembre:false
};
var dao = {
    getUpps: function () {
        $.ajax({
            type: "GET",
            url: '/calendarizacion/upps',
            dataType: "JSON"
        }).done(function (data) {
            const { upp } = data;
            var par = $('#upp_filter');
            par.html('');
            $.each(upp, function (i, val) {
                if (val.clv_upp == '001') {
                    par.append(new Option(val.upp, val.clv_upp, true, false));
                } else {
                    par.append(new Option(val.upp, val.clv_upp));
                }
            });
        });
    },
    getAniosM: function () {

        $.ajax({
            type: "GET",
            url: '/actividades/anios-metas/',
            dataType: "JSON"
        }).done(function (data) {
            console.log(data);
            var par = $('#anio_filter');
            par.html('');
            if (data.length >= 1) {
                $.each(data, function (i, val) {
                    par.append(new Option(val.ejercicio, val.ejercicio, true, false));
                });
            }else {
                var  d = new  Date();
                var  n = d.getFullYear();
                par.append(new Option(n,n, true, false));
             
            }
        });
    },
    nCont: function () {
        if ($('#nContinua').val()!='') {
            contValue = $('#nContinua').val();
            let fondo = $('#fondo').val();
            let ar=$('#ar').val();
            dao.getMesesCont(ar,fondo,contValue);
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
    getMesesCont: function (idA, idF,value) {
        for (const key in mesesV) {
            if (Object.hasOwnProperty.call(mesesV, key)) {
                mesesV[key] = false;
            }
        }
        $.ajax({
            type: "GET",
            url: '/actividades/meses-activos/' + idA + "/" + idF,
            dataType: "JSON"
        }).done(function (data) {
            let { mese } = data;
                for (const key in mese) {
                    if (Object.hasOwnProperty.call(mese, key)) {
                        const e = mese[key];
                        switch (key) {
                            case 'enero':
                                if (e != 0.0 || e != 0) {
                                    mesesV.enero = true;
                                    $("#1").prop('disabled', false);
                                    $("#1").prop('required', true);
                                    $("#1").val(value);
                                } else {
                                    $("#1").prop('disabled', 'disabled');
                                }
                                break;
                            case 'febrero':
                                if (e != 0.0 || e != 0) {
                                    mesesV.febrero = true;
                                    $("#2").prop('disabled', false);
                                    $("#2").prop('required', true);
                                    $("#2").val(value);
                                } else {
                                    $("#2").prop('disabled', 'disabled');
                                }
                                break;
                            case 'marzo':
                                if (e != 0.0 || e != 0) {
                                    mesesV.marzo = true;
                                    $("#3").prop('disabled', false);
                                    $("#3").prop('required', true);
                                    $("#3").val(value);
                                } else {
                                    $("#3").prop('disabled', 'disabled');

                                }
                                break;
                            case 'abril':
                                if (e != 0.0 || e != 0) {
                                    mesesV.abril = true;
                                    $("#4").prop('disabled', false);
                                    $("#4").prop('required', true);
                                    $("#4").val(value);
                                } else {
                                    $("#4").prop('disabled', 'disabled');
                                }
                                break;
                            case 'mayo':
                                if (e != 0.0 || e != 0) {
                                    mesesV.mayo = true;
                                    $("#5").prop('disabled', false);
                                    $("#5").prop('required', true);
                                    $("#5").val(value);
                                } else {
                                    $("#5").prop('disabled', 'disabled');
                                }
                                break;
                            case 'junio':
                                if (e != 0.0 || e != 0) {
                                    mesesV.junio = true;
                                    $("#6").prop('disabled', false);
                                    $("#6").prop('required', true);
                                    $("#6").val(value);
                                } else {
                                    $("#6").prop('disabled', 'disabled');
                                }
                                break;
                            case 'julio':
                                if (e != 0.0 || e != 0) {
                                    mesesV.julio = true;
                                    $("#7").prop('disabled', false);
                                    $("#7").prop('required', true);
                                    $("#7").val(value);
                                } else {
                                    $("#7").prop('disabled', 'disabled');
                                }
                                break;
                            case 'agosto':
                                if (e != 0.0 || e != 0) {
                                    mesesV.agosto = true;
                                    $("#8").prop('disabled', false);
                                    $("#8").prop('required', true);
                                    $("#8").val(value);
                                } else {
                                    $("#8").prop('disabled', 'disabled');
                                }
                                break;
                            case 'septiembre':
                                if (e != 0.0 || e != 0) {
                                    mesesV.septiembre = true;
                                    $("#9").prop('disabled', false);
                                    $("#9").prop('required', true);
                                    $("#9").val(value);
                                } else {
                                    $("#9").prop('disabled', 'disabled');
                                }
                                break;
                            case 'octubre':
                                if (e != 0.0 || e != 0) {
                                    mesesV.octubre = true;
                                    $("#10").prop('disabled', false);
                                    $("#10").prop('required', true);
                                    $("#10").val(value);
                                } else {
                                    $("#10").prop('disabled', 'disabled');
                                }
                                break;
                            case 'noviembre':
                                if (e != 0.0 || e != 0) {
                                    mesesV.noviembre = true;
                                    $("#11").prop('disabled', false);
                                    $("#11").prop('required', true);
                                    $("#11").val(value);
                                } else {
                                    $("#11").prop('disabled', 'disabled');
                                }
                                break;
                            case 'diciembre':

                                if (e != 0.0 || e != 0) {
                                    mesesV.diciembre = true;
                                    $("#12").prop('disabled', false);
                                    $("#12").prop('required', true);
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
    clearCont: function (tipo) {
        if (tipo!='aceptar') {
            $("#tipo_Ac option[value='']").attr("selected", true);
        }
        $('#nContinua').val("");
        $('#continua').modal('hide');
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
                    dataType: "json"
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
                    dataType: "json"
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
        let upp;
        if ($('#upp').val() == '') {
            upp = $('#upp_filter').val();
        } else {
            upp = $('#upp').val();
        }
        let anio = $('#anio_filter').val();
        _url = "/actividades/exportExcel/" + upp + "/" + anio;
        window.open(_url, '_blank');
        $('#cabecera').css("visibility", "visible");
        //  window.location = _url;
    },
    exportPdf: function () {
        let upp;
        if ($('#upp').val() == '') {
            upp = $('#upp_filter').val();
        } else {
            upp = $('#upp').val();
        }
        let anio = $('#anio_filter').val();
        _url = "/actividades/exportPdf/" + upp + "/" + anio;
        window.open(_url, '_blank');
        $('#cabecera').css("visibility", "visible");
        //  window.location = _url;
    },
    getData : function(upp,anio){
		$.ajax({
			type : "GET",
			url : "/actividades/data/"+upp+"/"+anio,
			dataType : "json"
        }).done(function (_data) {
			_table = $("#proyectoM");
			_columns = [
				{"aTargets" : [0] , "mData" :[0] },
				{"aTargets" : [1] , "mData" :[1] },
				{"aTargets" : [2] , "mData" :[2] },
				{"aTargets" : [3] , "mData" :[3] },
				{"aTargets" : [4] , "mData" :[4] },
                {"aTargets" : [5] , "mData": [5] },
                {"aTargets" : [6] , "mData" :[6] },
				{"aTargets" : [7] , "mData" :[7] },
				{"aTargets" : [8] , "mData" :[8] },
				{"aTargets" : [9] , "mData" :[9] },
				{"aTargets" : [10], "mData" :[10]},
                { "aTargets": [11], "mData": [11] },
                {"aTargets" : [12] , "mData" :[12] },
				{"aTargets" : [13] , "mData" :[13] },
				{"aTargets" : [14] , "mData" :[14] },
				{"aTargets" : [15] , "mData" :[15] },
				{"aTargets" : [16] , "mData" :[16] },
                {"aTargets" : [17] , "mData": [17] },
                {"aTargets" : [18] , "mData" :[18] },
				{"aTargets" : [19] , "mData" :[19] }
            ];
            _height = '1px';
            _pagination = 15;
			_gen.setTableScrollFotter(_table, _columns, _data);
		});
    },
    getSelect: function () {
        $.ajax({
            type: "GET",
            url: '/calendarizacion/selects',
            dataType: "JSON"
        }).done(function (data) {
            const { unidadM, beneficiario } = data;
            document.getElementById("medida").options[0].disabled = true;
            $.each(unidadM, function (i, val) {
                $('#medida').append("<option value='" + val.clave + "'>" + val.unidad_medida + "</option>");
            });
            document.getElementById("tipo_Be").options[0].disabled = true;
            $.each(beneficiario, function (i, val) {
                $('#tipo_Be').append("<option value='" + val.id + "'>" + val.beneficiario + "</option>");
            });
        });
    },
    getActiv: function (upp,sub) {
        $("#tipo_Ac").empty();
        $.ajax({
            type: "GET",
            url: '/calendarizacion/tcalendario/'+upp,
            dataType: "JSON"
        }).done(function (data) {       
            if (Object.keys(data).length == 2&& sub!='UUU') {
                $("#tipo_Ac").append(new Option("--Tipo Actividad--", ""));
                document.getElementById("tipo_Ac").options[0].disabled = true;

            }
            if (sub=='UUU') {
                $("#tipo_Ac").append(new Option('Acumulativa', 'Acumulativa'));
            } else {
                $.each(data, function (i, val) {
                    if (val == 1) {
                        $('#tipo_Ac').append("<option value='" + i + "'>" +i+"</option>");
                    }
                });
            }

        });
    },
    cierreMetas: function (upp) {
        $.ajax({
            type: "GET",
            url: '/actividades/cierre-metas/'+upp,
            dataType: "JSON"
        }).done(function (data) {
            if (!data.status) {
                $(".cierreMetas").hide();
            } else {
                $(".cierreMetas").show();
            }
            

        });
    },
    editarPutMeta: function () {
        var form = $('#actividad')[0];
        var data = new FormData(form);
        data.append('sumMetas', $('#sumMetas').val());
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
            dao.limpiar();
            const {mensaje } = response;
            Swal.fire({
                icon: mensaje.icon,
                title: mensaje.title,
                text: mensaje.text,
            });
            $('#cerrar').trigger('click');
            if ($('#upp').val() == '') {
                dao.getUpps();
                dao.getData($('#upp_filter').val(),$('#anio_filter').val());
            } else {
                dao.getData($('#upp').val(),$('#anio_filter').val());
            }
        });
    },
    editarMeta: function (id) {
        $("#addActividad").modal("show");
        $.ajax({
            type: "GET",
            url: "/calendarizacion/update/" + id,
            dataType : "json"
        }).done(function (data) {
            dao.getActiv(data.clv_upp,data.subprograma);
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
            $('#beneficiario').val(data.cantidad_beneficiarios);
            $("#tipo_Be option[value='" + data.beneficiario_id + "']").attr("selected", true);
            $("#medida option[value='" + data.unidad_medida_id + "']").attr("selected", true);
            $("#tipo_Ac option[value='"+ data.tipo +"']").attr("selected",true);
            $('#1').val(data.enero);
            $('#2').val(data.febrero);
            $('#3').val(data.marzo);
            $('#4').val(data.abril);
            $('#5').val(data.mayo);
            $('#6').val(data.junio);
            $('#7').val(data.julio);
            $('#8').val(data.agosto);
            $('#9').val(data.septiembre);
            $('#10').val(data.octubre);
            $('#11').val(data.noviembre);
            $('#12').val(data.diciembre);
            $('#sumMetas').val(data.total);
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
                                    $("#1").prop('disabled', false);
                                    $("#1").prop('required', true);
                                } else {
                                    if (data.enero !=0) {
                                        $('#1').val(0);
                                        $('#sumMetas').val(data.total-data.enero);
                                    }

                                    $("#1").prop('disabled', 'disabled');
                                }
                                break;
                            case 'febrero':
                                if (e != 0.0 || e != 0) {
                                    mesesV.febrero = true;
                                    $("#2").prop('disabled', false);
                                    $("#2").prop('required', true);
                                } else {
                                    if (data.febrero !=0) {
                                        $('#2').val(0);
                                        $('#sumMetas').val(data.total-data.febrero);
                                    }
                                    $("#2").prop('disabled', 'disabled');
                                }
                                break;
                            case 'marzo':
                                if (e != 0.0 || e != 0) {
                                    mesesV.marzo = true;
                                    $("#3").prop('disabled', false);
                                    $("#3").prop('required', true);
                                } else {
                                    if (data.marzo !=0) {
                                        $('#3').val(0);
                                        $('#sumMetas').val(data.total-data.marzo);
                                    }
                                    $("#3").prop('disabled', 'disabled');

                                }
                                break;
                            case 'abril':
                                if (e != 0.0 || e != 0) {
                                    mesesV.abril = true;
                                    $("#4").prop('disabled', false);
                                    $("#4").prop('required', true);
                                } else {
                                    if (data.abril !=0) {
                                        $('#4').val(0);
                                        $('#sumMetas').val(data.total-data.abril);
                                    }
                                    $("#4").prop('disabled', 'disabled');
                                }
                                break;
                            case 'mayo':
                                if (e != 0.0 || e != 0) {
                                    mesesV.mayo = true;
                                    $("#5").prop('disabled', false);
                                    $("#5").prop('required', true);
                                } else {
                                    if (data.mayo !=0) {
                                        $('#5').val(0);
                                        $('#sumMetas').val(data.total-data.mayo);
                                    }
                                    $("#5").prop('disabled', 'disabled');
                                }
                                break;
                            case 'junio':
                                if (e != 0.0 || e != 0) {
                                    mesesV.junio = true;
                                    $("#6").prop('disabled', false);
                                    $("#6").prop('required', true);
                                } else {
                                    if (data.junio !=0) {
                                        $('#6').val(0);
                                        $('#sumMetas').val(data.total-data.junio);
                                    }
                                    $("#6").prop('disabled', 'disabled');
                                }
                                break;
                            case 'julio':
                                if (e != 0.0 || e != 0) {
                                    mesesV.julio = true;
                                    $("#7").prop('disabled', false);
                                    $("#7").prop('required', true);
                                } else {
                                    if (data.julio !=0) {
                                        $('#7').val(0);
                                        $('#sumMetas').val(data.total-data.julio);
                                    }
                                    $("#7").prop('disabled', 'disabled');
                                }
                                break;
                            case 'agosto':
                                if (e != 0.0 || e != 0) {
                                    mesesV.agosto = true;
                                    $("#8").prop('disabled', false);
                                    $("#8").prop('required', true);
                                } else {
                                    if (data.agosto !=0) {
                                        $('#8').val(0);
                                        $('#sumMetas').val(data.total-data.agosto);
                                    }
                                    $("#8").prop('disabled', 'disabled');
                                }
                                break;
                            case 'septiembre':
                                if (e != 0.0 || e != 0) {
                                    mesesV.septiembre = true;
                                    $("#9").prop('disabled', false);
                                    $("#9").prop('required', true);
                                } else {
                                    if (data.septiembre !=0) {
                                        $('#9').val(0);
                                        $('#sumMetas').val(data.total-data.septiembre);
                                    }
                                    $("#9").prop('disabled', 'disabled');
                                }
                                break;
                            case 'octubre':
                                if (e != 0.0 || e != 0) {
                                    mesesV.octubre = true;
                                    $("#10").prop('disabled', false);
                                    $("#10").prop('required', true);
                                } else {
                                    if (data.octubre !=0) {
                                        $('#10').val(0);
                                        $('#sumMetas').val(data.total-data.octubre);
                                    }
                                    $("#10").prop('disabled', 'disabled');
                                }
                                break;
                            case 'noviembre':
                                if (e != 0.0 || e != 0) {
                                    mesesV.noviembre = true;
                                    $("#11").prop('disabled', false);
                                    $("#11").prop('required', true);
                                } else {
                                    if (data.noviembre !=0) {
                                        $('#11').val(0);
                                        $('#sumMetas').val(data.total-data.noviembre);
                                    }
                                    $("#11").prop('disabled', 'disabled');
                                }
                                break;
                            case 'diciembre':
                                if (e != 0.0 || e != 0) {
                                    mesesV.diciembre = true;
                                    $("#12").prop('disabled', false);
                                    $("#12").prop('required', true);
                                } else {
                                    if (data.diciembre !=0) {
                                        $('#12').val(0);
                                        $('#sumMetas').val(data.total-data.diciembre);
                                    }
                                    $("#12").prop('disabled', 'disabled');
                                }
                                break;
                    
                            default:
                                break;
                        }
                    
                    }
                }
            } else {
                
                $("#sumMetas").prop('disabled', 'disabled');
                for (let i = 1; i <= 12; i++) {
                    $("#" + i).prop('disabled', 'disabled');

                }
                
            }

            if (edit) {
                $('#editMetas').addClass(" alert alert-danger").addClass("text-center");
                $('#editMetas').text("Las claves presupuestarias fueron modificadas y es posible que un mes no tenga presupuesto lo cual fue modificado a CERO");
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
                                $('#1-error').text("Este campo es requerido").addClass('has-error').css({'display':''});
                                mesesfs++;
                            } else {
                                
                                $('#1-error').text("").removeClass('has-error');
                            }
                        }
                        break;
                    case 'febrero':
                        if (e) {
                           
                            if ($("#2").val() == 0) {
                                $('#2-error').text("Este campo es requerido").addClass('has-error').css({'display':''});
                                mesesfs++;
                            } else {
                                
                                $('#2-error').text("").removeClass('has-error');
                            }
                        }
                        break;
                    case 'marzo':
                        if (e) {
                           
                            if ($("#3").val() == 0) {
                                $('#3-error').text("Este campo es requerido").addClass('has-error').css({'display':''});
                                mesesfs++;
                            } else {
                                
                                $('#3-error').text("").removeClass('has-error');
                            }
                        }
                        break;
                    case 'abril':
                        if (e) {
                           
                            if ($("#4").val() == 0) {
                                $('#4-error').text("Este campo es requerido").addClass('has-error').css({'display':''});
                                mesesfs++;
                            } else {
                                
                                $('#4-error').text("").removeClass('has-error');
                            }
                        }
                        break;
                    case 'mayo':
                        if (e) {
                           
                            if ($("#5").val() == 0) {
                                $('#5-error').text("Este campo es requerido").addClass('has-error').css({'display':''});
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
                                $('#7-error').text("Este campo es requerido").addClass('has-error').css({'display':''});
                                mesesfs++;
                            } else {
                                
                                $('#7-error').text("").removeClass('has-error');
                            }
                        }
                        break;
                    case 'agosto':
                        if (e) {
                           
                            if ($("#8").val() == 0) {
                                $('#8-error').text("Este campo es requerido").addClass('has-error').css({'display':''});
                                mesesfs++;
                            } else {
                                
                                $('#8-error').text("").removeClass('has-error');
                            }
                        }
                        break;
                    case 'septiembre':
                        if (e) {
                           
                            if ($("#9").val() == 0) {
                                $('#9-error').text("Este campo es requerido").addClass('has-error').css({'display':''});
                                mesesfs++;
                            } else {
                                
                                $('#9-error').text("").removeClass('has-error');
                            }
                        }
                        break;
                    case 'octubre':
                        if (e) {
                           
                            if ($("#10").val() == 0) {
                                $('#10-error').text("Este campo es requerido").addClass('has-error').css({'display':''});
                                mesesfs++;
                            } else {
                                
                                $('#10-error').text("").removeClass('has-error');
                            }
                        }
                        break;
                    case 'noviembre':
                        if (e) {
                           
                            if ($("#11").val() == 0) {
                                $('#11-error').text("Este campo es requerido").addClass('has-error').css({'display':''});
                                mesesfs++;
                            } else {
                                
                                $('#11-error').text("").removeClass('has-error');
                            }
                        }
                        break;
                    case 'diciembre':

                        if (e) {
                           
                            if ($("#12").val() == 0) {
                                $('#12-error').text("Este campo es requerido").addClass('has-error').css({'display':''});
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
        console.log("meses sin llenar:",mesesfs);
        if (mesesfs >= 1) {
            $("#meses-error").text("Debes de llenar los meses desbloqueados").addClass('has-error');
            return false;
           
        } else {
            $("#meses-error").text("").removeClass('has-error');
            return true;
       }
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
            dataType: "JSON"
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
                    $('#cerrar').trigger('click');

                    if ($('#upp').val() == '') {
                        dao.getUpps();
                        dao.getData($('#upp_filter').val(), $('#anio_filter').val());
                    } else {
                        dao.getData($('#upp').val(), $('#anio_filter').val());
                    }
                
                });
            }
        });
    },
    limpiar: function () {
        $("#meses-error").text("").removeClass('has-error');
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
        $('#beneficiario').val("");
        for (let i = 1; i <=12; i++) {
            $("#" + i).prop('disabled', true); 
        }
    },
    limpiarFormFirma: function () {
        $('#firmaModal').modal('hide');
        document.getElementById("frm_eFirma").reset(); 
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
            if($('#' + i).val() != "" && $('#' + i).val() != 0){
                let suma = parseInt($('#' + i).val());
                e.push(suma);
            }
        }
        if (dao.arrEquals(e)) {
            $('#sumMetas').val(e[0]);
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
    revConfirmarMetas: function (upp,anio) {
        $.ajax({
            type: "GET",
            url: '/actividades/rev-confirmar-metas/'+upp+"/"+anio,
            dataType: "JSON"
        }).done(function (data) {
            if (!data.status) {
                $(".confirmacion").hide();
            } else {
                $(".confirmacion").show();
            }
            

        });
    },
    rCMetasUpp: function (upp,anio) {
        $.ajax({
            type: "GET",
            url: '/actividades/rev-confirmar-metas-upp/'+upp+"/"+anio,
            dataType: "JSON"
        }).done(function (data) {
            if (data.status) {
                $(".cmupp").show();
                $('#validMetas').addClass(" alert alert-danger").addClass("text-center");
                $('#validMetas').text("Las metas ya fueron confirmadas");
                if ($('#upp').val() !='') {
                    $(".cierreMetas").hide();
                } 
                $(".confirmacion").hide();
            } else {
              /*   $('#validMetas').addClass(" alert alert-danger").addClass("text-center"); */
                $('#validMetas').text("").removeClass().removeClass(" alert alert-danger");
                $(".cmupp").hide();
            }
            

        });
    },
    ConfirmarMetas: function () {
        let anio = $('#anio_filter').val();
        let upp = "";
        if ($('#upp').val() == '') {
            upp = $('#upp_filter').val();
            
        } else {
            upp = $('#upp').val();
        }
        Swal.fire({
            icon: 'question',
            title: '¿Estás seguro que quieres confirmar las metas?',
            showDenyButton: true,
            confirmButtonText: 'Confirmar',
            denyButtonText: `Cancelar`,
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                $.ajax({
                    type: "GET",
                    url: '/actividades/confirmar-metas/'+upp+"/"+anio,
                    dataType: "JSON"
                }).done(function (data) {
                    const { mensaje } = data;
                    Swal.fire({
                        icon: mensaje.icon,
                        title: mensaje.title,
                        text: mensaje.text,
                        footer: mensaje?.footer,
                    });
                    dao.getData(upp, anio);
                    dao.revConfirmarMetas(upp, anio);
                    dao.rCMetasUpp(upp,anio);
                });
            } /* else if (result.isDenied) {
              Swal.fire('Changes are not saved', '', 'info')
            } */
          })
       
    },
    DesconfirmarMetas: function () {
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
                    url: '/actividades/desconfirmar-metas/'+upp+"/"+anio,
                    dataType: "JSON"
                }).done(function (data) {
                    const { mensaje } = data;
                    Swal.fire({
                        icon: mensaje.icon,
                        title: mensaje.title,
                        text: mensaje.text,
                    });
                    dao.getData(upp, anio);
                    dao.revConfirmarMetas(upp, anio);
                    dao.rCMetasUpp(upp,anio);
                });
            } /* else if (result.isDenied) {
              Swal.fire('Changes are not saved', '', 'info')
            } */
          })
       
    },
    
};

var init = {
    validateCreate: function (form) {
        _gen.validate(form, {
            rules: {
                tipo_Ac: { required: true },
                beneficiario: { required: true },
                tipo_Be: { required: true },
                medida: { required: true },
                sumMetas: { required: true }
            },
            messages: {
                tipo_Ac: { required: "Este campo es requerido" },
                beneficiario: { required: "Este campo es requerido" },
                tipo_Be: { required: "Este campo es requerido" },
                medida: { required: "Este campo es requerido" },
                sumMetas: { required: "Este campo es requerido  y mayor a CERO" }
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
    
    const moonLanding = new Date();
    $("#anio_filter option[value='" + moonLanding.getFullYear() + "']").attr("selected", true);
   
    $("#cerrar").click(function(){
        $("#addActividad").modal('hide')
    });
    $("#cancelar").click(function(){
        $("#addActividad").modal('hide')
      });
    $('#btnSave').click(function (e) {
        e.preventDefault();
        let flag = dao.validMeses();
        if ($('#tipo_Ac').val() != 'Continua') {
            if ($('#actividad').valid() && flag) {
                dao.editarPutMeta();
            }
        } else {
            if (dao.validatCont() != 0) {
                if ($('#actividad').valid() && flag) {
                    dao.editarPutMeta();
                }
            }
        }
    });
    dao.getSelect();
    dao.getAniosM();
    $("#upp_filter").select2({
        maximumSelectionLength: 10
    });
    $("#anio_filter").select2({
        maximumSelectionLength: 10
    });
    if ($('#upp').val() == '') {
        dao.getUpps();
        dao.getData($('#upp_filter').val(), $('#anio_filter').val());
        dao.revConfirmarMetas($('#upp_filter').val(), $('#anio_filter').val());
        dao.rCMetasUpp($('#upp_filter').val(),$('#anio_filter').val());
    } else {
        dao.getData($('#upp').val(), $('#anio_filter').val());
        dao.cierreMetas($('#upp').val());
        dao.revConfirmarMetas($('#upp').val(), $('#anio_filter').val());
        dao.rCMetasUpp($('#upp').val(),$('#anio_filter').val());
    }

    for (let i = 1; i <= 12; i++) {
        $("#" + i).val(0);
    }
/*     $("#sumMetas").val(0);   
 */
    $('#tipo_Ac').change(() => {
        for (let i = 1; i <= 12; i++) {
            $("#" + i).prop('disabled', false);
        }

    });
    $('#upp_filter').change(() => {
        dao.getData($('#upp_filter').val(), $('#anio_filter').val());
        dao.revConfirmarMetas($('#upp_filter').val(), $('#anio_filter').val());
        dao.rCMetasUpp($('#upp_filter').val(),$('#anio_filter').val());

    });
    $('#anio_filter').change(() => {
        if ($('#upp').val() == '') {
            dao.getData($('#upp_filter').val(), $('#anio_filter').val());
            dao.revConfirmarMetas($('#upp_filter').val(), $('#anio_filter').val());
        } else {
            dao.getData($('#upp').val(), $('#anio_filter').val());
            dao.revConfirmarMetas($('#upp').val(), $('#anio_filter').val());
        }
    });

    $('#btnSaveFirma').click(function (e) {
        init.validateFirmaE($('#frm_eFirma'));

        if ($('#frm_eFirma').valid()) {
            dao.firmarReporte();
        }

    });
    $('#tipo_Ac').change(() => {
        for (let i = 1; i <= 12; i++) {
              $('#' + i).val(0);
        }
        dao.getMeses($('#ar').val(), $('#fondo').val());
        $('#sumMetas').val("");
        if ($('#tipo_Ac').val() == 'Continua') {
            $('#continua').modal('show')
        }
    });
    $('#continua').modal({
        backdrop: 'static',
        keyboard: false
    });
});