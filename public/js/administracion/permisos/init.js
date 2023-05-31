var url = "/adm-permisos";
//Ejecuta Document Ready
$(document).ready(function() {
	//Input Permisos Función Change
	$("input[name='permisos']").change(function() {
		_id_mod = $(this).val();
		_id_role = $("#id").val();
		if($(this).prop("checked") == true) {			
			$.ajax({
				type : "POST",
				url : url + "/asigna",
				data : {"_token": "{{ csrf_token() }}",modulo : _id_mod, role : _id_role}
			}).done(function(_response) {
			});
		} else {
			$.ajax({
				type : "POST",
				url : url + "/remueve",
				data : {"_token": "{{ csrf_token() }}",modulo : _id_mod, role : _id_role}
			}).done(function(_response) {
			});
		}
	});

	//Input Menus Función Change
	$("input[name='menus']").change(function() {
		_id_menu = $(this).val();
		_id_role = $("#id").val();
		if($(this).prop("checked") == true) {			
			$.ajax({
				type : "POST",
				url : url + "/masigna",
				data : {menu : _id_menu, role : _id_role}
			}).done(function(_response) {
			});
		} else {
			$.ajax({
				type : "POST",
				url : url + "/mremueve",
				data : {menu : _id_menu, role : _id_role}
			}).done(function(_response) {
			});
		}
	});
	//Input Todos los Permisos Función Change
	$("input[name='all-permission']").change(function() {
		_id_role = $("#id").val();
		_type = "";
		$("#widget-grid").html('<h1 class="error-text-2 bounceInDown animated"><i class="fa fa-gear fa-spin fa-lg"></i> Cargando <span class="particle particle--a"></span><span class="particle particle--b"></span></h1>');
		if($(this).prop("checked") == true) { _type = "add"; } else { _type = "remove"; }
		$.ajax({
			type : "POST",
			url : url + "/all-permission",
			data : {role : _id_role, type : _type }
		}).done(function(_response) {
			location.reload();
		});
	});
});