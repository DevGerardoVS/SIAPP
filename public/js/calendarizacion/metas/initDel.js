const inputs = ['sel_actividad', 'sel_fondo', 'tipo_Ac', 'beneficiario', 'tipo_Be', 'medida'];

var dao = {
    getUpps: function () {
        $.ajax({
            type: "GET",
            url: '/calendarizacion/upps/',
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
            var par = $('#anio_filter');
            par.html('');
            if (data.length >= 1) {
                $.each(data, function (i, val) {
                    par.append(new Option(val.ejercicio, val.ejercicio, true, false));
                });
            }else {
                var  d = new  Date();
                var n = d.getFullYear();
                var nn = n + 1;
                par.append(new Option(nn,nn, true, false));
                par.append(new Option(n,n, true, false));
             
            }
        });
    },
    getData : function(upp,anio){
		$.ajax({
			type : "GET",
			url : "/actividades/data/metas-delegacion/"+upp+"/"+anio,
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

    getPlantillaCmUpp: function () {
        const url = "/actividades/plantilla/metas-delegacion";
        window.location.href = url;
    },
    crearMetaImp: function () {
        var form = $('#formFile')[0];
        var data = new FormData(form);
        let timerInterval;
        Swal.fire({
          title: "Espere un momento",
          text: "Registrando datos..",
          timer: 40000,
          timerProgressBar: true,
          didOpen: () => {
            Swal.showLoading();
            const timer = Swal.getPopup().querySelector("b");
            timerInterval = setInterval(() => {
              timer.textContent = `${Swal.getTimerLeft()}`;
            }, 100);
          },
          willClose: () => {
            clearInterval(timerInterval);
          }
        }).then(() => {
          $.ajax({
            type: "POST",
            url: '/actividades/import/metas-delegacion',
            data: data,
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
        }).done(function (response) {
            $("#cmFile").val("");
          
            Swal.fire({
                icon: response.icon,
                title: response.title,
                text: response.text
            });


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
    save: function () {
            if ($('#formFile').valid()) {
                dao.crearMetaImp();
            }
    },
    editarMeta: function (id) {
        $("#addActividad").modal("show");
        $.ajax({
            type: "GET",
            url: "/calendarizacion/update/" + id,
            dataType : "json"
        }).done(function (data) {
            $("#tipo_Ac").append(new Option('Acumulativa', 'Acumulativa'));
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
            $("#tipo_Be option[value='']").remove();
            $("#medida option[value='']").remove();
            $("#tipo_Be").append(new Option('Empleado',  data.beneficiario_id ));
            $("#medida").append(new Option('Nomina', data.unidad_medida_id ));
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
            $('#sumMetas').val(data.total).prop('disabled', 'disabled');;
            $('#ar').val(data.ar);
            $('#fondo').val(data.clv_fondo);
            let edit = false;
            for (let i = 0; i <=12; i++) {
                $('#'+i).prop('disabled', 'disabled');
                
            }

            if (edit) {
                $('#editMetas').addClass(" alert alert-danger").addClass("text-center");
                $('#editMetas').text("Las claves presupuestarias fueron modificadas y es posible que un mes no tenga presupuesto lo cual fue modificado a CERO");
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
        $('.form-group').removeClass('has-error');  
        for (let i = 1; i <=12; i++) {
            $('#' + i).val(0);
        }
        $('#beneficiario').val("");
        for (let i = 1; i <=12; i++) {
            $("#" + i).prop('disabled', true); 
        }
    },
    editarPutMeta: function () {
        var form = $('#actividad')[0];
        var data = new FormData(form);
     /*    for (let i = 0; i <= 11; i++) {  
            data.append(i, 2);
        }
        data.append(12, 3);
        data.append('sumMetas', 25); */
        $.ajax({
            type: "POST",
            url: '/calendarizacion/put/metas-delegacion',
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
    rCMetasUpp: function (upp,anio) {
        $.ajax({
            type: "GET",
            url: '/actividades/flag-confirmar-metas/'+upp+"/"+anio,
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        }).done(function (data) {
            console.log(data);
            if (data.status) {
                dao.checkCMetasUpp(upp,anio);
            } else {
                $(".confirmacion").hide();
              /*   $('#validMetas').addClass(" alert alert-danger").addClass("text-center"); */
            /*     $('#validMetas').text("").removeClass().removeClass(" alert alert-danger");
                $(".cmupp").hide(); */
            }
            

        });
    },
    checkCMetasUpp: function (upp,anio) {
        $.ajax({
            type: "GET",
            url: '/actividades/check-metas/delegacion/'+upp+"/"+anio,
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        }).done(function (data) {
            console.log(data);
            if (data.status) {
                 $(".cmupp").show();
                $('#validMetas').addClass(" alert alert-danger").addClass("text-center");
                $('#validMetas').text("Las metas ya fueron confirmadas");
                $(".confirmacion").hide();
            } else {
                $(".confirmacion").show();
                $('#validMetas').addClass(" alert alert-danger").addClass("text-center");
                 $('#validMetas').text("").removeClass().removeClass(" alert alert-danger");
                $(".cmupp").hide();
            }
            

        });
    },
    ConfirmarMetas: function () {
        let anio = $('#anio_filter').val();
        let upp = $('#upp_filter').val();

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
                    url: '/actividades/confirmar-metas/delegacion/'+upp+"/"+anio,
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
                    dao.getData(upp, anio);
                    dao.rCMetasUpp($('#upp_filter').val(), $('#anio_filter').val());
                });
            } /* else if (result.isDenied) {
              Swal.fire('Changes are not saved', '', 'info')
            } */
          })
       
    },
};
var init = {
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
    }
};
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    dao.getUpps();
    dao.getAniosM();
    dao.rCMetasUpp($('#upp_filter').val(), $('#anio_filter').val());
    $("#upp_filter").select2({
        maximumSelectionLength: 10
    });
    $("#anio_filter").select2({
        maximumSelectionLength: 10
    });
    dao.getData($('#upp_filter').val(), $('#anio_filter').val());
    init.validateFile($('#formFile'));

    $('#upp_filter').change(() => {
        dao.getData($('#upp_filter').val(), $('#anio_filter').val());
        dao.rCMetasUpp($('#upp_filter').val(), $('#anio_filter').val());

    });
    $('#anio_filter').change(() => {
        dao.getData($('#upp_filter').val(), $('#anio_filter').val());
        dao.rCMetasUpp($('#upp_filter').val(), $('#anio_filter').val());

    });
    $('#btnSave').click(function (e) {
        e.preventDefault();
            if ($('#actividad').valid()) {
                dao.editarPutMeta();
            }
    });
});