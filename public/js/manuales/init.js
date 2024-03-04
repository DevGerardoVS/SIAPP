$(document).ready(function() {

    dropifyInit();
    getData();

    $(".container").on('click', '#btn_new_registro', function() {
        limpiarCampos();
        $.ajax({
            url: "/configuraciones/get-usuarios",
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
            url: "/configuraciones/delete-manual",
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
                    confirmButtonText: "Aceptar",
                });
                if(response.status == "success"){
                    $("#close-modal").click();
                    getData();
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
        url: "/configuraciones/add-manual",
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
                confirmButtonText: "Aceptar",
            });
            if(response.status == "success"){
                
                $("#close-modal-new").click();
                getData();
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
        url: "/configuraciones/get-manual",
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
        url: "/configuraciones/download-manual",
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