var dao = {
	getData : function(){
		$.ajax({
			type : "GET",
			url : "/adm-usuarios/data",
			dataType : "json"
		}).done(function(_data){
			_table = $("#tbl-usuarios");
			_columns = [
				{"aTargets" : [0], "mData" : "username"},
				{"aTargets" : [1], "mData" : "email"},
				{"aTargets" : [2], "mData" : "nombre_completo"},
				{"aTargets" : [3], "mData" : "celular"},
				{"aTargets" : [4], "mData" : "perfil"},
				{"aTargets" : [5], "mData" : function(o){
					var adm = '';
					if (o.estatus == 1) {
						adm = '<span class="label label-success">Activo</span>';
					}else {
						adm = '<span class="label label-danger">Inactivo</span>';
					}
					return adm;
				}},
				{"aTargets" : [6], "mData" : function(o){
					return '<a data-toggle="tooltip" title="Modificar Usuario" class="btn btn-sm btn-success" href="#/adm-usuarios/update/'+o.id+'">' + '<i class="glyphicon glyphicon-pencil"></i></a>&nbsp;'
					  +'<a data-toggle="tooltip" title="Inhabilitar/Habilitar Usuario" class="btn btn-sm btn-warning" onclick="dao.setStatus(' + o.id + ', ' + o.estatus + ')">' + '<i class="glyphicon glyphicon-lock"></i></a>&nbsp;'
					  +'<a data-toggle="tooltip" title="Eliminar Usuario" class="btn btn-sm btn-danger" onclick="dao.eliminarUsuario(' + o.id + ')">' + '<i class="glyphicon glyphicon-trash"></i></a>&nbsp;';
				}},
				
			];
			_gen.setTableScroll(_table, _columns, _data);
		});
	},

	setStatus : function(id, estatus) {
		$.SmartMessageBox({
			title : 'Confirmar Activación/Desactivación',
			buttons : '[No][Continuar]',
		}, function(btn, input) {
			if (btn === "Continuar" && input != "") {
				$.ajax({
					type : "POST",
					url : "/adm-usuarios/status",
					data : { id : id, estatus : estatus}
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

	eliminarUsuario : function(id) {
		$.SmartMessageBox({
			title : 'Confirmar Eliminación',
			buttons : '[No][Continuar]',
		}, function(btn, input) {
			if (btn === "Continuar" && input != "") {
				$.ajax({
					type : "POST",
					url : "/adm-usuarios/eliminar",
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



    getPerfil : function(id){
        $.ajax({
          type : "GET",
          url: '/adm-grupos/data',
          dataType : "JSON"
        }).done(function(data){
          var par = $('#slc_perfil');
          par.html('');
          par.append(new Option("-- Selecciona Perfil --", ""));
          $.each(data, function(i, val){
            par.append(new Option(data[i].nombre_grupo, data[i].id));
          });
          par.select2().select2("val", id);
        });
    },

    crearUsuario : function(){
    	var form = $('#frm_create')[0];
		var data = new FormData(form);
    	$.ajax({
    		type : "POST",
            url: '/adm-usuarios/store',
            data : data,
			enctype : 'multipart/form-data',
			processData: false,
            contentType: false,
            cache: false,
            timeout: 600000
    	}).done(function(response){
    		if (response == "done") {
    			_gen.notificacion_min('Éxito', 'La acción se ha realizado correctamente', 1);
    			dao.limpiarFormularioCrear();
    		} else if (response == "userDuplicate") {
    			_gen.notificacion_min('Advertencia Usuario Duplicado', 'El nombre de usuario ya existe, favor de ingresar uno nuevo', 3);
    		} else if (response == "emailDuplicate") {
    			_gen.notificacion_min('Advertencia Correo Duplicado', 'El correo de usuario ya existe, favor de ingresar uno nuevo', 3);
    		}
    	});
    },

    editarUsuario : function(){
    	var form = $('#frm_update')[0];
		var data = new FormData(form);
    	$.ajax({
    		type : "POST",
            url: '/adm-usuarios/put-usuario',
            data : data,
			enctype : 'multipart/form-data',
			processData: false,
            contentType: false,
            cache: false,
            timeout: 600000
    	}).done(function(response){
    		if (response == "done") {
    			_gen.notificacion_min('Éxito', 'La acción se ha realizado correctamente', 1);
    			window.location.href = '/#/adm-usuarios';
    		}
    	});
    },

    limpiarFormularioCrear: function() {
    	$('#in_username').val('');
    	$('#in_pass').val('');
    	$('#in_pass_conf').val('');
    	$('#in_email').val('');
    	$('#in_nombre').val('');
    	$('#in_p_apellido').val('');
    	$('#in_s_apellido').val('');
    	$('#in_email').val('');
    	$('#in_celular').val('');
    	dao.getPerfil("");
    },

    guardarGrupo: function(grupos, id) {
    	$.ajax({
    		type : "POST",
            url: '/adm-usuarios/grupos',
            data : {id: id, grupos: grupos}
    	}).done(function(response) {
    		if (response == "done") {
    			_gen.notificacion_min('Éxito', 'La acción se ha realizado correctamente', 1);
    		} else if (response == "nada") {
    			_gen.notificacion_min('Advertencia', 'Nada que agregar', 3);
    		}
    	});
    }

};

var init = {
	validateCreate : function(form){
		_gen.validate(form, {
			rules : {
				username : {required: true},
				password : {required: true},
				password_confirmation : {required: true, equalTo : "#in_pass"},
				email : {required : true},
				nombre : {required: true},
				p_apellido : {required: true},
				s_apellido : {required: true},
				celular : {required: true}
			},

			messages : {
				username : {required: "Este campo es requerido"},
				password : {required: "Esta campo es requerido"},
				password_confirmation : {required: "Este campo es requerido"},
				email : {required: "Este campo es requerido"},
				nombre : {required: "Este campo es requerido"},
				p_apellido : {required: "Este campo es requerido"},
				s_apellido : {required: "Este campo es requerido"}
			}
		});
	},
};

$(document).ready(function () {
	dao.getData();
	$('#btnSave').click(function(e){
		e.preventDefault();
		if($('#frm_create').valid()) {
			dao.crearUsuario();
		}
	});

	$('#btnUpdate').click(function(e){
		e.preventDefault();
		if($('#frm_update').valid()) {
			dao.editarUsuario();
		}
	});

	$('#in_celular').mask('00-00-00-00-00');
});