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
    optionTabs: function (id, anio, upp, ur) {
        ur = ur != null ? ur : '0';
        upp = upp != null ? upp : '0';
        switch (id) {
            case 'metas-tab':
                dao.limpiar('A');
                if (upp != 0 && upp != null) {
                    dao.getData(upp, ur, anio);
                }
                break;
            case 'capturadas-tab':
                if (upp != 0 && upp != null) {
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
            _gen.setTableScrollFotter(_table, _columns, _data);
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
                        if (!response.estado) {
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
    getUrs: function (anio,upp) {
        $('#ur_filter').empty();
        $.ajax({
            type: "GET",
            url: '/calendarizacion/urs/' + anio + '/' + upp,
            dataType: "JSON",
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        }).done(function (data) {
            const { urs, tAct } = data;
            var par = $('#ur_filter');
            par.html('');
            par.append(new Option("URS", "ur"));
            document.getElementById("ur_filter").options[0].disabled = true;
            $.each(urs, function (i, val) {
                par.append(new Option(val.ur, val.clv_ur));
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
            par.append(new Option("Seleccione una UPP", "upp"));
            document.getElementById("upp_filter").options[0].disabled = true;
            $.each(upp, function (i, val) {
                par.append(new Option(val.upp, val.clv_upp));
            });


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
            dao.getUrs($('#anio_filter').val(),$('#upp_filter').val());
        } else {
            upp = $('#upp').val();
            dao.getUrs($('#anio_filter').val(),$('#upp_filter').val());
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
                med.append(new Option(" U. Medida", ""));
                document.getElementById("medida").options[0].disabled = true;
                $.each(unidadM, function (i, val) {
                    med.append(new Option(val.unidad_medida, val.clave));
                });
                 
                tipo_be.append(new Option(" Beneficiarios", ""));
                document.getElementById("tipo_Be").options[0].disabled = true;
                $.each(beneficiario, function (i, val) {
                    tipo_be.append(new Option(beneficiario[i].beneficiario, beneficiario[i].id));
                });
            }
        });
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
    dao.getUpps($("#anio_filter").val());
    $(".nav-link").on("click", function () {
        let id = $(".active")[0].id;
        let anio = $("#anio_filter").val();
        let upp = $("#upp_filter").val();
        let ur = $("#ur_filter").val();
        dao.optionTabs(id, anio, upp, ur);
    });
    $("#beneficiario").on('paste', function (e) {
        e.preventDefault();
    });
    $("#sumMetas").on('paste', function (e) {
        e.preventDefault();
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
        let id = $(".active")[0].id;
        let anio = $("#anio_filter").val();
        let upp = $("#upp_filter").val();
        let ur = $("#ur_filter").val();
       dao.optionTabs(id, anio, upp, ur);
        $('#sel_actividad').empty();
        $('#sel_actividad').append("<option value=''class='text-center' ><b>-- Actividad--</b></option>");
        $('#sel_fondo').empty();
        $('#sel_fondo').append("<option value=''class='text-center' ><b>-- Fondos--</b></option>");
    });
    $("#ur_filter").select2({
        maximumSelectionLength: 10
    });
    $("#upp_filter").select2({
        maximumSelectionLength: 10
    });
});