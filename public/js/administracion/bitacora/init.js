var dao = {
	getData : function(fecha){
		if (fecha == null || fecha == ''){
			fecha = 0;
		}
		$.ajax({
			type : "GET",
			url : "/adm-bitacora/data/" + fecha,
			dataType : "json"
		}).done(function(_data){
			_table = $("#tbl-bitacora");
			_columns = [
				{"aTargets" : [0], "mData" : "username"},
				{"aTargets" : [1], "mData" : "accion"},
				{"aTargets" : [2], "mData" : "modulo"},
				{"aTargets" : [3], "mData" : "ip_origen"},
				{"aTargets" : [4], "mData" : "fecha_movimiento"},
				{"aTargets" : [5], "mData" : "created_at"}
			];
			_gen.setTableScroll(_table, _columns, _data);
		});
	},
};

$(document).ready(function () {
	$(".fecha").datepicker({language:'es', format: 'yyyy-mm-dd', autoclose : true});

	$('#in_filtro_fecha').change(function () {
		dao.getData($(this).val());
	});
});