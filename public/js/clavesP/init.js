var dao = {
    getData : function(){
		$.ajax({
			type : "GET",
			url : "/calendarizacion/claves-get",
			dataType : "json",
			//data : {},
		}).done(function(_data){
			console.log('_data:',_data);
			let data = [];
			for (let index = 0; index < _data.length; index++) {
				if (_data[index].clave_presupuestal && _data[index].clave_presupuestal != '') {
					const clasificacionAdmin = _data[index].clave_presupuestal.substring(0,5);
					const centroGestor = _data[index].clave_presupuestal.substring(5,21);
					const areaFuncional = _data[index].clave_presupuestal.substring(21,37);
					const periodoPre = _data[index].clave_presupuestal.substring(37,43);
					const posicionPre = _data[index].clave_presupuestal.substring(43,49);
					const fondo = _data[index].clave_presupuestal.substring(49,58);
					const proyectoObra = _data[index].clave_presupuestal.substring(58,64);
					let row = _data[index].clave +" "+'-'+" "+ _data[index].descripcion +" "+'-'+" "+'Presupuesto calendarizado: ';
					let totalByClave = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(_data[index].totalByClave); 
					let id = _data[index].id;
					data.push({'clasificacionAdmin':clasificacionAdmin, 'centroGestor':centroGestor,'areaFuncional':areaFuncional,'periodoPre': periodoPre, 'posicionPre':posicionPre,'fondo':fondo,'proyectoObra': proyectoObra,'row': row,'totalByClave': totalByClave});
				}
				
			}
			console.log('Data',data);
			_table = $("#claves");
			_columns = [
				{"aTargets" : [0], "mData" : 'clasificacionAdmin'},
				{"aTargets" : [1], "mData" : "centroGestor"},
				{"aTargets" : [2], "mData" : "areaFuncional"},
				{"aTargets" : [3], "mData" : "periodoPre"},
				{"aTargets" : [4], "mData" : "posicionPre"},
				{"aTargets" : [5], "mData" : "fondo"},
				{"aTargets" : [6], "mData" : "proyectoObra"},
				{"aTargets" : [7], "mData" : "totalByClave"},
				{"aTargets" : [8], "mData" : function(o){
					return '<a data-toggle="tooltip" title="Modificar" class="btn btn-sm btn-success" href="#/'+o.id+'">' + '<i class="fa fa-pencil"></i></a>&nbsp;'
					+  '<a data-toggle="tooltip" title="Ver" class="btn btn-sm btn-primary">' + '<i class="fa fa-eye"></i></a>&nbsp;'
					+  '<a data-toggle="tooltip" title="Eliminar" class="btn btn-sm btn-danger">' + '<i class="fa fa-trash"></i></a>&nbsp;';
				}},
				
			];
			_gen.setTableScrollGroupBy(_table, _columns, data);
		});
	},
	getRegiones : function(id){
        $.ajax({
          type : "GET",
          url: '/cat-regiones',
          dataType : "JSON"
        }).done(function(data){
          console.log("🚀 ~ file: init.js:54 ~ data regiones:", data)
          var par = $('#sel_region');
          par.html('');
          par.append(new Option("-- Selecciona una Region --", ""));
          $.each(data, function(i, val){
            par.append(new Option(data[i].descripcion, data[i].clave));
          });
          //par.select2().select2("val", id);
        });
    },
};


$(document).ready(function(){
	$('#modalNewClave').modal({
        backdrop: 'static',
        keyboard: false
    });
	//$('.select2').select2();
	
});