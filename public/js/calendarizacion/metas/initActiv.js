const inputs = ['sel_actividad', 'sel_fondo', 'tipo_Ac', 'beneficiario', 'tipo_Be', 'medida'];

var dao = {
    getUpps: function () {
        $.ajax({
            type: "GET",
            url: '/calendarizacion/upps/',
            dataType: "JSON"
        }).done(function (data) {
            var par = $('#upp_filter');
            par.html('');
            $.each(data, function (i, val) {
                if (data[i].clv_upp=='001') {
                    par.append(new Option(data[i].upp, data[i].clv_upp,true,false));
                } else
                {
                    par.append(new Option(data[i].upp, data[i].clv_upp));
                }
                
            });


        });
    },
    exportJasper: function () {
        let upp;
        if ($('#upp').val() == '') {
            upp = $('#upp_filter').val();
        } else {
            upp = $('#upp').val();
        }
        $.ajax({
            type:'get',
            url:"/actividades/jasper/" + upp,
            dataType : "json"
        }).done(function (params) {
            $('#firmaModal').modal('show');
        });
    },
    exportExcel: function () {
        let upp;
        if ($('#upp').val() == '') {
            upp = $('#upp_filter').val();
        } else {
            upp = $('#upp').val();
        }
        _url = "/actividades/exportExcel/" + upp;
        window.open(_url, '_blank');
        $('#cabecera').css("visibility","visible");
      //  window.location = _url;
    },
    exportPdf: function () {
        let upp;
        if ($('#upp').val() == '') {
            upp = $('#upp_filter').val();
        } else {
            upp = $('#upp').val();
        }
        _url = "/actividades/exportPdf/" + upp;
        window.open(_url, '_blank');
                $('#cabecera').css("visibility","visible");

      //  window.location = _url;
    },
    getData : function(upp){
		$.ajax({
			type : "GET",
			url : "/actividades/data/"+upp,
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
                $('#medida').append("<option value='"+val.clave+"'>"+val.unidad_medida+"</option>");
            });
            document.getElementById("tipo_Be").options[0].disabled = true;
            $.each(beneficiario, function (i, val) {
                $('#tipo_Be').append("<option value='"+val.id+"'>"+val.beneficiario+"</option>");
            });

        });
    },
    getActiv: function (upp) {
        $("#tipo_Ac").empty();
        $.ajax({
            type: "GET",
            url: '/calendarizacion/tcalendario/'+upp,
            dataType: "JSON"
        }).done(function (data) {        
            $.each(data, function (i, val) {
                if (val == 1) {
                    $('#tipo_Ac').append("<option value='" + i + "'>" +i+"</option>");
                }
            });
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
                dao.getData($('#upp_filter').val());
            } else {
                dao.getData($('#upp').val());
        
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
            dao.getActiv(data.clv_upp);
            $('#proyectoMD').empty();
            $('#proyectoMD').append("<thead><tr class='colorRosa'>"
             +"<th class= 'vertical' > UPP</th >"
             +"<th class='vertical'>UR</th>"
             +"<th class='vertical'>Programa</th>"
             +"<th class='vertical'>Subprograma</th>"
             +"<th class='vertical'>Proyecto</th>"
             +"<th class='vertical'>Fondo</th>"
             +"<th class='vertical'>Actividad</th>"
                + "</tr>thead")
                
            $('#proyectoMD').append('<tbody class="text-center"><tr>'
                + '<th scope="row">' + data.clv_upp + '</th> <th>  '
                + data.clv_ur+ '</th> <th>'+ data.clv_programa
                + '</th><th>' + data.subprograma + '</th><th>'
                + data.proyecto + '</th><th>' + data.clv_fondo
                + '</th><th>' + data.actividad + '</th>' +
                '</tr></tbody>')

            $('#id_meta').text(data.id);
            $('#Nactividad').text(data.actividad);
            $('#Nfondo').text(data.clv_fondo);
            $('#beneficiario').val(data.cantidad_beneficiarios);
            $("#tipo_Be option[value='" + data.beneficiario_id + "']").attr("selected", true);
            $("#medida option[value='"+ data.unidad_medida_id +"']").attr("selected",true);
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
        });
    },
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
                    const {mensaje } = data;
                    Swal.fire({
                        icon: mensaje.icon,
                        title: mensaje.title,
                        text: mensaje.text,
                    });
                    $('#cerrar').trigger('click');

                    if ($('#upp').val() == '') {
                        dao.getUpps();
                        dao.getData($('#upp_filter').val());
                    } else {
                        dao.getData($('#upp').val());
                
                    }
                
                });

            }
        })
    },
    limpiar: function () {
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
};

$(document).ready(function () {
    $("#cerrar").click(function(){
        $("#addActividad").modal('hide')
    });
    $("#cancelar").click(function(){
        $("#addActividad").modal('hide')
      });
    $('#btnSave').click(function (e) {
        e.preventDefault();
        if ($('#actividad').valid()) {
            dao.editarPutMeta();
        }
    });
    dao.getSelect();
    $("#upp_filter").select2({
        maximumSelectionLength: 10
    });
    if ($('#upp').val() == '') {
        dao.getUpps();
        dao.getData($('#upp_filter').val());
    } else {
        dao.getData($('#upp').val());

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
        dao.getData($('#upp_filter').val());
    });

    $('#btnSaveFirma').click(function (e) {
        init.validateFirmaE($('#frm_eFirma'));
        if ($('#frm_eFirma').valid()) {
            dao.firmarReporte();
        }

    });
});