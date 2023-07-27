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
    editarMeta: function (id) {
        Swal.fire({
            icon: 'success',
            title: 'Your work has been saved',
            showConfirmButton: false,
            timer: 1500
        })
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
        $('#sumMetas').val(0);
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
    $("#sumMetas").val(0);
    
  
    $('#btnSave').click(function (e) {
        e.preventDefault();
        if ($('#actividad').valid()) {
            dao.crearUsuario();
        }
    });
  
    

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