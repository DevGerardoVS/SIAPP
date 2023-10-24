

var init = {
	validateCreate: function (form) {
		_gen.validate(form, {
			rules: {
				nombre: { required: true }
			},

			messages: {
				nombre: { required: "Este campo es requerido" }
			}
		});
	},
};


function getUpps(){
    $.ajax({
        url:"/amd-configuracion/upps",
        type: "POST",
        dataType: 'json',
        processData: false,
        contentType: false,
        success:function(response){
            response = response.dataSet;
            var $dropdown = $("#upps");
            $.each(response, function(key, value) {
                $dropdown.append('<option value="' + value.clave + '">'  + value.clave + ' - ' + value.descripcion + '</option>');
            });

        },
        error: function(response) {
            var mensaje="";
            $.each(response.responseJSON.errors, function( key, value ) {
                mensaje += value+"\n";
            });
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: mensaje,
                confirmButtonText: "Aceptar",
            });
            //$('#errorModal').modal('show');
            console.log('Error: ' +  JSON.stringify(response.responseJSON));
        }
    });
}

function getUPPAuto(){
	$.ajax({
        url:"/amd-configuracion/upps-auto",
        type: "POST",
        dataType: 'json',
        processData: false,
        contentType: false,
        success:function(response){
            response = response.dataSet;
            var $dropdown = $("#upps_auto");
            $.each(response, function(key, value) {
                $dropdown.append('<option value="' + value.clave + '">' + value.clave + ' - ' + value.descripcion + '</option>');
            });

        },
        error: function(response) {
            var mensaje="";
            $.each(response.responseJSON.errors, function( key, value ) {
                mensaje += value+"\n";
            });
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: mensaje,
                confirmButtonText: "Aceptar",
            });
            //$('#errorModal').modal('show');
            console.log('Error: ' +  JSON.stringify(response.responseJSON));
        }
    });
}

function adjustTableColumns(){
	var dt = $("#catalogo_b");
	dt.DataTable().columns.adjust().draw();
}

function getAutorizedUpp(){

	var formData = new FormData();
	var csrf_tpken = $("input[name='_token']").val();
	var filter = $("#upps_auto option").filter(':selected').val();

	formData.append("_token",csrf_tpken);
	formData.append("filter",filter);

	$.ajax({
		url:"/amd-configuracion/data-auto",
		type: "POST",
		data: formData,
		dataType: 'json',
		processData: false,
		contentType: false,
		success:function(response){
			response = response.dataSet;
			var dt = $("#catalogo_b");
			if(response.length == 0){
				dt.attr('data-empty','true');
			}
			else{
				dt.attr('data-empty','false');
			}
			dt.DataTable().clear();
			dt.DataTable().destroy();
			dt.DataTable({
			   data: response,
			   pageLength:10,
			   scrollX: true,
			   autoWidth: false,
			   processing: true,
			   order: [],
			   ServerSide: true,
			   api:true,
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
				   buttons: {
					   copyTitle: 'Copiado al portapapeles',
					   copySuccess: {
						   _: '%d registros copiados',
						   1: 'Se copio un registro'
					   }
				   },
			   }

		   });
		   console.log("auto");
		   //dt.DataTable().columns.adjust().draw(); 
		   
		},
		error: function(response) {
			var mensaje="";
			$.each(response.responseJSON.errors, function( key, value ) {
				mensaje += value+"\n";
			});
			Swal.fire({
				icon: 'error',
				title: 'Error',
				text: mensaje,
				confirmButtonText: "Aceptar",
			});
			//$('#errorModal').modal('show');
			console.log('Error: ' +  JSON.stringify(response.responseJSON));
		}
	});
}

function updateAutoUpps(id){
	var formData = new FormData();
	var csrf_tpken = $("input[name='_token']").val();
	
	var value = $("#"+id)[0].checked;
	//console.log(value);
	formData.append("_token",csrf_tpken);
	formData.append("id",id);
	formData.append("value",value);
	$.ajax({
        url:"/amd-configuracion/update-auto",
		data: formData,
        type: "POST",
        dataType: 'json',
        processData: false,
        contentType: false,
        success:function(response){
            response = response.dataSet;
			//colocar codigo de respuesta
			if(response.length>0){
				Swal.fire({
					icon: 'warning',
					title: 'Advertencia',
					text: response,
					confirmButtonText: "Aceptar",
				});
			}
        },
        error: function(response) {
            var mensaje="";
            $.each(response.responseJSON.errors, function( key, value ) {
                mensaje += value+"\n";
            });
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: mensaje,
                confirmButtonText: "Aceptar",
            });
            //$('#errorModal').modal('show');
            console.log('Error: ' +  JSON.stringify(response.responseJSON));
        }
    });
}

function updateData(id,field){
	var formData = new FormData();
	var csrf_tpken = $("input[name='_token']").val();
	var tipo;
	switch(field){
		case "continua":
			tipo = 'c';
			break;
		case "acumulativa":
			tipo = 'a';
			break;
		case "especial":
			tipo = 'e';
			break;
		default:
			break;
	}
	var value = $("#"+id+"_"+tipo)[0].checked;
	//console.log(value);
	formData.append("_token",csrf_tpken);
	formData.append("id",id);
	formData.append("field",field);
	formData.append("value",value);
	$.ajax({
        url:"/amd-configuracion/update",
		data: formData,
        type: "POST",
        dataType: 'json',
        processData: false,
        contentType: false,
        success:function(response){
            response = response.dataSet;
            if(response == "error"){
				Swal.fire({
					icon: 'error',
					title: 'Error',
					text: "No puedes deshabilitar todas las casillas.",
					confirmButtonText: "Aceptar",
				});
				
			}
        },
        error: function(response) {
            var mensaje="";
            $.each(response.responseJSON.errors, function( key, value ) {
                mensaje += value+"\n";
            });
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: mensaje,
                confirmButtonText: "Aceptar",
            });
            //$('#errorModal').modal('show');
            console.log('Error: ' +  JSON.stringify(response.responseJSON));
        }
    });
}



$(document).ready(function () {

	getData();

    getUpps();

	getAutorizedUpp();
	
	$('#createGroup').modal({
		backdrop: 'static',
		keyboard: false
	});
	$('#btnSave').click(function (e) {
		e.preventDefault();
		if ($('#frmCreate').valid()) {
			dao.crearGrupo();
		}
	});

	$("#upps").on('change',function(){
		$("#filter").val($("#upps").val());
		getData();
	});

	$("#upps_auto").on('change',function(){
		$("#filter_auto").val($("#upps_auto").val());
		getAutorizedUpp();
	});


	getUPPAuto();
	
});