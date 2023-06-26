let actividades = [];
var dao = {
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
                        getData();
                    }
                });
            }
        });
    },
    getAnio: function () {
        let anio = [2022, 2023, 2024, 2025];
        var par = $('#anio_filter');
        par.html('');
        par.append(new Option("-- Año--", ""));
        document.getElementById("anio_filter").options[0].disabled = true;
        $.each(anio, function (i, val) {
            par.append(new Option(anio[i], anio[i]));
        });
        /*      $.ajax({
                 type: "GET",
                 url: 'grupos',
                 dataType: "JSON"
             }).done(function (data) {
                 var par = $('#id_grupo');
                 par.html('');
                 par.append(new Option("-- Selecciona Perfil --", ""));
                 document.getElementById("id_grupo").options[0].disabled = true;
                 $.each(data, function (i, val) {
                     par.append(new Option(data[i].nombre_grupo, data[i].id));
                 });
             }); */
    },
    limpiarFormularioCrear: function () {
        $('#fondos').empty()
        $('#fondos').append('<thead>\n' +
            '     <tr class="colorMorado">\n' +
            '         <th>Tipo</th>\n' +
            '         <th>ID Fondo</th>\n' +
            '         <th>Monto</th>\n' +
            '         <th>Ejercicio</th>\n' +
            '         <th>Acciones</th>\n' +
            '     </tr>\n' +
            ' </thead>')

        $('#uppSelected').removeClass('is-invalid')
        $('#uppSelected').val(0)
    },
    eliminaFondo: function (i) {
        document.getElementById(i).outerHTML=""
    },
    filtroPresupuesto: function (i){
        var tecla = event.key;
        if (['.','e','-'].includes(tecla)){
            event.preventDefault()
        }

        if($('#presupuesto_'+i).val() == 0){
            $("#frm_create_techo").find('#presupuesto_'+i).addClass('is-invalid');
        }else{
            $('#presupuesto_'+i).removeClass('is-invalid')
        }
    }
};
var init = {
    validateCreate: function (form) {
        _gen.validate(form, {
            rules: {
                tipo: { required: true },
                fondo: { required: true },
                presupuesto: { required: true },
            },
            messages: {
                tipo: { required: "Este campo es requerido" },
                fondo: { required: "Este campo es requerido" },
                presupuesto: { required: "Este campo es requerido" },
            }
        });
    },
};

$(document).ready(function () {
    getData();
    dao.getAnio();

    $('#fondo_filter').selectpicker({ search: true });
    $('#upp_filter').selectpicker({ search: true });

    $('#btnNew').on('click',function (e) {
        e.preventDefault();
        anio = new Date().getFullYear() + 1;
        $('#anioOpt').val(anio)
    })
    $('#agregar_fondo').on('click', function (e){
        e.preventDefault()

        if($('#uppSelected').val() != 0){
            selectFondo = ''

            table = document.getElementById('fondos')
            table_lenght = (table.rows.length)

            $.ajax({
                type: "GET",
                url: '/calendarizacion/techos/get-fondos',
                dataType: "JSON"
            }).done(function (data) {
                selectFondo = '<select class="form-control filters" id="fondo_'+table_lenght+'" name="fondo_'+table_lenght+'" placeholder="Seleccione un fondo" required>';
                selectFondo += '<option value="">Seleccione fondo</option>';
                data.forEach(function(item){
                    selectFondo += '<option value="'+item.clv_fondo_ramo+'" >'+item.clv_fondo_ramo+" - "+item.fondo_ramo+'</option>'
                });
                selectFondo += '</select>';
            });

            row = table.insertRow(table_lenght).outerHTML='<tr id="'+table_lenght+'">\n' +
                '<td>' +
                '       <select class="form-control filters" id="tipo_'+table_lenght+'" name="tipo_'+table_lenght+'" placeholder="Seleccione una tipo" required>\n' +
                '           <option value="">Seleccione un tipo</option>\n'+
                '           <option value="Operativo">Operativo</option>\n'+
                '           <option value="RH">RH</option>\n' +
                '       </select>' +
                '</td>\n' +
                '<td>'
                    + selectFondo +
                '</td>\n' +
                '<td>' +
                '<input type="number" class="form-control" id="presupuesto_'+table_lenght+'" name="presupuesto_'+table_lenght+'" placeholder="$0" onkeydown="dao.filtroPresupuesto('+table_lenght+')" required>' +
                '</td>\n' +
                '  <td><input type="number" value="2024" class="form-control" id="ejercicio_'+table_lenght+'" name="ejercicio_'+table_lenght+'" disabled placeholder="2024"></td>\n' +
                '<td>' +
                '   <input type="button" value="Eliminar" onclick="dao.eliminaFondo('+table_lenght+')" title="Eliminar fondo" class="btn btn-danger delete" >' +
                '</td>\n' +
                '</tr>'
        }else{
            $('#uppSelected').addClass('is-invalid')
        }
    });
});

$('#btnSave').click(function (e) {
    e.preventDefault();

    var form = $('#frm_create_techo')[0];
    var data = new FormData(form);

        $.ajax({
            type: "POST",
            url: '/calendarizacion/techos/add-techo',
            data: data,
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000
        }).done(function (response) {
            if(response.status == 200){
                $('#cerrar').trigger('click');
                Swal.fire({
                    icon: 'success',
                    title: 'Techo financiero creado con éxito',
                    showConfirmButton: false,
                    timer: 1500
                });
                dao.limpiarFormularioCrear();
                getData();
            }else if(response.status == 400){
                Swal.fire({
                    icon: 'warning',
                    title: 'Hubo un error, datos faltantes',
                    showConfirmButton: true
                });
            }else if(response.status == 'Repetidos'){
                Swal.fire({
                    icon: 'warning',
                    title: 'Hay fondos repetidos',
                    showConfirmButton: true
                });
            }
            else{
                Swal.fire({
                    icon: 'error',
                    title: 'Hubo un error',
                    showConfirmButton: true
                });
            }
        }).fail(function (error) {
            let arr = Object.keys(error.responseJSON.errors)
            arr.forEach(function (item) {
                $("#frm_create_techo").find("#"+item).addClass('is-invalid');
            })
            Swal.fire({
                icon: 'warning',
                title: 'Hubo un error, campos vacíos',
                showConfirmButton: true
            });

        });
});