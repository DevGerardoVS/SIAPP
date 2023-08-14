var dao = {
	getData: function (anio, mes) {
		const aniojs = new Date();
		if (anio ==null || anio=='') {
			anio = aniojs.getFullYear();
		}
		$.ajax({
			type : "GET",
			url : "/adm-bitacora/data/" +anio+"/"+mes,
			dataType : "json"
		}).done(function(_data){
			_table = $("#tbl-bitacora");
			_columns = [
				{"aTargets" : [0], "mData" : [0]},
				{"aTargets" : [1], "mData" : [1]},
				{"aTargets" : [2], "mData" : [2]},
				{"aTargets" : [3], "mData" : [3]},
				{"aTargets" : [4], "mData" : [4]},
				{"aTargets" : [5], "mData" : [5]}
			];
			_height = '1px';
            _pagination = 15;
			_gen.setTableScroll(_table, _columns, _data,_height,_pagination);
		});
	},
	getmeses: function () {
		$('#mes_filter').empty();
		var mess = new Date();
		var me2 = mess.getMonth();
		me2 = me2 + 1;
		let mes= ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre' ];
		var par = $('#mes_filter');
		par.html('');
		par.append(new Option("--Meses--", "", true, true));
		document.getElementById("mes_filter").options[0].disabled = true;

		$.each(mes, function (i, val) {
			let mes = i + 1;
			if (me2 == mes) {
				par.append(new Option(val, `${mes <= 9 ? '0' + mes : mes}`,true,true));

			} else {
				par.append(new Option(val, `${mes <= 9 ? '0' + mes : mes}`));

			}
		});
	},
	getAnios : function(){
		$('#anio_filter').empty();
		$.ajax({
			type : "GET",
			url : "/adm-bitacora/dataAnios",
			dataType : "json"
		}).done(function(anio){
			var par = $('#anio_filter');
			par.html('');
			par.append(new Option("--Años--", "", true, true));
			document.getElementById("anio_filter").options[0].disabled = true;
			$.each(anio, function (i, val) {
				if (i = 1) {
					par.append(new Option(val, val, true, true));

				} else {
					par.append(new Option(val, val));

				}
			});
		});
	},
	exportExcel: function () {
		let anio = $("#anio_filter option:selected").val();
			let mes = $('#mes_filter').val();
		_url = "/adm-bitacora/exportExcelBitacora/" + anio+"/"+mes;
        window.open(_url, '_blank');
		
	}
};

$(document).ready(function () {
	dao.getmeses();
	dao.getAnios();
	var fecha = new Date();
	var mes = fecha.getMonth();
	mes = mes + 1;
	dao.getData($('#anio_filter').val(), mes<=9?'0'+mes:mes);
	$('#anio_filter').change(() => {
		dao.getData($("#anio_filter option:selected").val(), $('#mes_filter').val());
    });
    $('#mes_filter').change(() => {
		dao.getData($('#anio_filter').val(), $('#mes_filter').val());
    });
});