var dao = {
	getData : function(){
		$.ajax({
			type : "GET",
			url : "/adm-grupos/data",
			dataType : "json"
		}).done(function(_data){
			_table = $("#tbl-grupos");
			_columns = [
				{"aTargets" : [0], "mData" : "nombre_grupo"},
				{"aTargets" : [1], "mData" : function(o){
					return '<a data-toggle="tooltip" title="Administrar Permisos" class="btn btn-sm btn-info" href="/#/adm-permisos/grupo/'+o.id+'">' + '<i class="glyphicon glyphicon-screenshot"></i></a>&nbsp;'
						+'<a data-toggle="tooltip" title="Modificar Grupo" class="btn btn-sm btn-success" href="/#/adm-grupos/update/'+o.id+'">' + '<i class="glyphicon glyphicon-pencil"></i></a>&nbsp;'
						+'<a data-toggle="tooltip" title="Eliminar Grupo" class="btn btn-sm btn-danger" onclick="dao.eliminarGrupo(' + o.id + ')">' + '<i class="glyphicon glyphicon-trash"></i></a>&nbsp;';
				}},
				
			];
			_gen.setTableScroll(_table, _columns, _data);
		});
	},

	eliminarGrupo : function(id) {
		$.SmartMessageBox({
			title : 'Confirmar Eliminación',
			buttons : '[No][Continuar]',
		}, function(btn, input) {
			if (btn === "Continuar" && input != "") {
				$.ajax({
					type : "POST",
					url : "/adm-grupos/eliminar",
					data : { id : id}
				}).done(function(data) {
					if(data != "done"){
						_gen.notificacion_min('Error', 'Hubo un problema al querer realizar la acción, contacte a soporte', 4);
					}else {
						_gen.notificacion_min('Éxito', 'La acción se ha realizado correctamente', 1);
						dao.getData();
					}
				});
			}
		});
	},

	crearGrupo : function(){
    	var form = $('#frmCreate')[0];
		var data = new FormData(form);
    	$.ajax({
    		type : "POST",
            url: '/adm-grupos/store',
            data : data,
			enctype : 'multipart/form-data',
			processData: false,
            contentType: false,
            cache: false,
            timeout: 600000
    	}).done(function(response){
    		if (response == "done") {
    			_gen.notificacion_min('Éxito', 'La acción se ha realizado correctamente', 1);
    			window.location.href = '/#/adm-grupos';
    		}
    	});
    },

    editarGrupo : function(){
    	var form = $('#frmUpdate')[0];
		var data = new FormData(form);
    	$.ajax({
    		type : "POST",
            url: '/adm-grupos/put-grupo',
            data : data,
			enctype : 'multipart/form-data',
			processData: false,
            contentType: false,
            cache: false,
            timeout: 600000
    	}).done(function(response){
    		if (response == "done") {
    			_gen.notificacion_min('Éxito', 'La acción se ha realizado correctamente', 1);
    			window.location.href = '/#/adm-grupos';
    		}
    	});
    },
};

var init = {
	validateCreate : function(form){
		_gen.validate(form, {
			rules : {
				nombre_grupo : {required: true}
			},

			messages : {
				nombre_grupo : {required: "Este campo es requerido"}
			}
		});
	},
};

$(document).ready(function () {
	$('#btnSave').click(function(e) {
		e.preventDefault();
		if ($('#frmCreate').valid()) {
			dao.crearGrupo();
		}
	});

	$('#btnUpdate').click(function(e) {
		e.preventDefault();
		if ($('#frmUpdate').valid()) {
			dao.editarGrupo();
		}
	});
});