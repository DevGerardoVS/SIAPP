const inputs = ['sel_actividad', 'sel_fondo', 'tipo_Ac', 'beneficiario', 'tipo_Be', 'medida'];

var dao = {
    nextPage: function () {
        var url = window.location.href;
        
        $.ajax({
            type: "GET",
            url: "/nombres/" + url.slice(-1),
            dataType: "JSON"
        }).done(function (data) {
            console.log("names",data)
            var act = $('#nombres'); 
            act.html('');
            act.append('<li><b>UR:</b>&nbsp;' + data[0] + '</li>');
            act.append('<li><b>Programa:</b>&nbsp;' + data[1] + '</li>');
            act.append('<li><b>Subprograma:</b>&nbsp;' + data[2] + '</li>');
            act.append('<li><b>Proyecto:</b>&nbsp;' + data[3] + '</li>');
        });
    },
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
                        location.reload();
                    }
                });
            }
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



    }, getSelect: function () {
        $.ajax({
            type: "GET",
            url: '/calendarizacion/selects',
            dataType: "JSON"
        }).done(function (data) {
            console.log("done",data);
            const { unidadM, fondos, beneficiario, actividades,activids } = data;
            var act = $('#sel_actividad'); 
            act.html('');
            act.append(new Option("--Actividad--",""));
            document.getElementById("sel_actividad").options[0].disabled = true;
            $.each(actividades, function (i, val) {
                act.append(new Option(val.actividad,val.clv_actividad));
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
    

    crearUsuario: function () {
        var form = $('#actividad')[0];
        var data = new FormData(form);
        var url = window.location.href;
        data.append('pMir_id', url.slice(-1));
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
            Swal.fire({
                icon: 'success',
                title: 'Your work has been saved',
                showConfirmButton: false,
                timer: 1500
            });
            getData();
        });
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
    for (let i = 1; i <= 12; i++) {
        $("#" + i).val(0);
    }
    $("#sumMetas").val(0);
    getData();
    dao.nextPage();
    dao.getSelect();
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

    })
});