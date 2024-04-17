$(document).ready(function() {

    dropifyInit();

    //manualDatatable();
    $("#manuales-tab").on('click', function() {
        manualDatatable();
    });

    $(".container").on('click', '#btn_new_registro', function() {
        limpiarCampos();
        $.ajax({
            url: "/amd-configuracion/get-usuarios",
            type: "GET",
            data: {},
            dataType: 'json',
            success: function(response) {
                $("#roles").empty();
                //console.log(response.roles.length);
                response.roles.forEach(element => {
                    $("#roles").append('<label>'+element.nombre_grupo+'</label><input class="form-check-input roles" type="checkbox" value="'+element.id+'" name="'+element.nombre_grupo+'" id="'+element.id+'" style="margin-left:20px"><br>');
                });

            },
            error: function(response) {
                //console.log(response);
            }
        }); 

        
    });

    $(".modal").on('click', '#delete-button', function() {
        var formData = new FormData();
        var csrf_token = $("input[name='_token']").val();
        var id = $("#delete-button").attr('data-id');

        formData.append("_token",csrf_token);
        formData.append("id",id);

        $.ajax({
            url: "/amd-configuracion/delete-manual",
            data: formData,
            type:'POST',
            dataType: 'json',
            contentType: false,
            processData: false,
            success: function(response) {
                $("#confirmAcept").removeAttr('disabled');
                Swal.close();
                Swal.fire({
                    icon: response.status,
                    title: response.title,
                    text: response.message,
                    timer: 3000,
                    confirmButtonText: "Aceptar",
                });
                
                if(response.status == "success"){
                    $("#close-modal").click();
                    manualDatatable();
                }
            },
            error: function(response) {
                //console.log(response);
            }
        }); 
    });
    
});

function sendData(){
    var formData = new FormData();
    var csrf_token = $("input[name='_token']").val();
    var archivo = $("#archivo")[0].files[0];
    var act = $("#id_act").val();
    var id_archivos = $("#archivo_id").val();
    var name = $("#nombre").val();
    
    formData.append("_token",csrf_token);
    formData.append("id_act",act);
    formData.append("id_archivo",id_archivos);
    var seleccion = $(".roles");

    var users = [];

    $.each(seleccion, function(index, item) {
        const user = {};
        user.id= item.id;
        user.value = $("#"+item.id).is(':checked');
        users.push(user);
    });

    users = JSON.stringify(users);
    //console.log(users);
    formData.append("archivo",archivo);
    formData.append("name",name);
    formData.append("users",users);
    
    $.ajax({
        url: "/amd-configuracion/add-manual",
        data: formData,
        type:'POST',
        dataType: 'json',
        contentType: false,
        processData: false,
        beforeSend: function() {
            $("#confirmAcept").attr('disabled','disabled');
            let timerInterval
            Swal.fire({
                title: 'Cargando datos, espere por favor...',
                html: ' <b></b>',
                allowOutsideClick: false,
                
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading()
                }
            });
        },
        success: function(response) {
            $("#confirmAcept").removeAttr('disabled');
            Swal.close();
            Swal.fire({
                icon: response.status,
                title: response.title,
                text: response.message,
                timer: 3000,
                confirmButtonText: "Aceptar",
            });
            if(response.status == "success"){
                
                $("#close-modal-new").click();
                manualDatatable();
            }
            limpiarCampos();
        },
        error: function(response) {
            
            //console.log('Error: ' + response.title+ " "+response.message);
            
            limpiarCampos();
        },
        statusCode: {
            404: function(response) {
                console.log('ajax.statusCode: 404');
            },
            500: function(response) {
                var response = response.responseJSON;
                Swal.close();
                Swal.fire({
                    icon: response.status,
                    title: response.title,
                    text: response.message,
                    confirmButtonText: "Aceptar",
                });
                //console.log('ajax.statusCode: 500');
            }
        }
    });
}

function getManual(id){
    var formData = new FormData();
    var csrf_token = $("input[name='_token']").val();

    formData.append("_token",csrf_token);
    formData.append("id",id);

    $.ajax({
        url: "/amd-configuracion/get-manual",
        data: formData,
        type:'POST',
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function(response) {
            $("#roles").empty();
            //console.log(response);
            $("#nombre").val(response.manual.nombre);
            $("#id_act").val(response.manual.id);
            
            for (let index = 0; index < response.roles.length; index++) {
                const element = response.roles[index];
                const usuarios = JSON.parse(response.manual.usuarios);
                var checked = '';
                for (let j = 0; j < usuarios.length; j++) {
                    const id = usuarios[j].id;
                    const value = usuarios[j].value;
                    //console.log(value+" "+typeof value);
                    if(id == element.id && value == true){
                        checked = 'checked';
                        break;
                    } 
                }
                $("#roles").append('<label>'+element.nombre_grupo+'</label><input class="form-check-input roles" type="checkbox" value="'+element.id+'" name="'+element.nombre_grupo+'" id="'+element.id+'" '+checked+' style="margin-left:20px"><br>');
            }
            

        },
        error: function(response) {
            //console.log(response);
        }
    }); 
}

function deleteManual(id){
    $("#delete-button").removeAttr("data-id");
    $("#delete-button").attr("data-id",id);
}

function descargar(id){
    var formData = new FormData();
    var csrf_token = $("input[name='_token']").val();

    formData.append("_token",csrf_token);
    formData.append("id",id);

    $.ajax({
        url: "/amd-configuracion/download-manual",
        data: formData,
        type:'POST',
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function(response) {
            console.log(response);
        },
        error: function(response) {
            console.log("response "+response);

        }
    }); 
}

function limpiarCampos() {
    $("#nombre").val("");
    $('.dropify-clear').click();
    $("#id_act").val("");
    $("#delete-button").removeAttr("data-id");
}

function dropifyInit(){
    $('.dropify').dropify({
        allowedFiles: ['pdf', 'xls', 'xlsx'],
        messages: {
            'default': 'Arrastre y suelte el archivo aquí o haga click',
            'replace': 'Arrastre y suelte aquí o haga click para reemplazar',
            'remove':  'Remover',
            'error':   'Ooops, algo ha salido mal.'
        },
        error: {
            'fileSize': 'El archivo es muy grande (máximo 6 Mb).',
            'minWidth': 'El ancho de la imágen es muy pequeño (mínimo px).',
            'maxWidth': 'El ancho de la imágen es muy grande (máximo px).',
            'minHeight': 'El alto de la imágen es muy pequeño (mínimo px).',
            'maxHeight': 'El alto de la imágen es muy grande (máxima px).',
            'imageFormat': 'El formato de esta imágen no esta permitido (solo JPG).',
            'fileFormat': 'El formato de archivo no esta permitido (solamente pdf, doc, docx, xls, xlsx).',
        }
    });
}



function manualDatatable() {
    var dt = $('#catalogo_c');
    dt.DataTable().clear().destroy();
    var orderDt = "";
    var column = "";
    var formatCantidades = [];
    var ordenamiento = [];
    var columns_hidden = [];
    const url = $("#buscarForm_c").attr("action");
    /* console.log("url", url); */

    if (dt.attr('data-id') != undefined) {
        var data_order = dt.attr('data-id').split(",");
        for (var i in data_order) {
            var dato = data_order[i].split("_");
            orderDt = dato[0];
            column = dato[1];
            ordenamiento[i] = [parseInt(column), "" + orderDt];
        }
    }

    if (dt.attr('data-hidden') != undefined) {
        var data_hidden = dt.attr('data-hidden').split(",");
        for (var i in data_hidden) {
            columns_hidden[i] = parseInt(data_hidden[i]);
        }
    }

    if (dt.attr('data-format') != undefined) {
        formatCantidades = dt.attr('data-format').split(",");
        for (var i in formatCantidades) {
            if (formatCantidades[i] != "") {
                formatCantidades[i] = parseInt(formatCantidades[i]);
            }
        }
    }
    $.ajax({
        url: $("#buscarForm_c").attr("action"),
        data: $("#buscarForm_c").serializeArray(),
        type: $("#buscarForm_c").attr("method"),
        dataType: 'json',
        success: function(response) {
            /* console.log("res-DataTable", response) */
            if (response?.dataSet?.length == 0) {
                dt.attr('data-empty', 'true');
            } else {
                dt.attr('data-empty', 'false');
            }


            dt.DataTable({
                data: response?.dataSet,
                pageLength: 10,
                scrollX: true,
                autoWidth: false,
                processing: true,
                order: ordenamiento,
                ServerSide: true,
                language: {
                    processing: "Procesando...",
                    lengthMenu: "Mostrar _MENU_ registros",
                    zeroRecords: "No se encontraron resultados",
                    emptyTable: "Ningún dato disponible en esta tabla",
                    info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                    infoFiltered: "(filtrado de un total de _MAX_ registros)",
                    search: "Búsqueda:",
                    infoThousands: ",",
                    loadingRecords: "Cargando...",
                    buttonText: "Imprimir",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior",
                    },
                    dom: 'Bfrtip',
                    buttons: [
                        'excel', 'pdf',
                    ]
                },
                columnDefs: [{
                        targets: formatCantidades,
                        className: 'text-center'
                    },
                    {
                        targets: columns_hidden,
                        visible: false,
                        searcheable: false
                    }
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    api.columns('.sum', {
                        page: 'current'
                    }).every(function() {
                        var sum = this.data().reduce(function(a, b) {
                            var x = parseFloat(a) || 0;
                            if (b == null) {
                                b = "0.00";
                            }
                            var y = parseFloat(b.replaceAll(",", "")) || 0;
                            return x + y;
                        }, 0);
                        sum = sum.toFixed(2);
                        $(this.footer()).html($(this.footer()).attr('data-title') +
                            ": " + sum.toString().replace(
                                /\B(?=(\d{3})+(?!\d))/g, ","));
                    });
                }
            });
            redrawTable('#catalogo_c');
        },
        error: function(response) {
            console.log('Error: ', response.responseJSON.message);
        }
    });
}