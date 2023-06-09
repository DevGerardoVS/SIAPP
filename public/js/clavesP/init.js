var dao = {
    getData : function(){
		$.ajax({
			type : "GET",
			url : "/calendarizacion/claves-get",
			dataType : "json",
			//data : {},
		}).done(function(_data){
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
          var par = $('#sel_region');
          par.html('');
          par.append(new Option("-- Selecciona una Region --", ""));
          $.each(data, function(i, val){
            par.append(new Option(data[i].descripcion, data[i].clave + '-'+ data[i].region_id));
          });
          //par.select2().select2("val", id);
        });
    },
	getMunicipiosByRegion : function(id){
        $.ajax({
          	type : "get",
          	url: '/cat-municipios/'+ id,
        }).done(function(data){
          var par = $('#sel_municipio');
          par.html('');
          par.append(new Option("-- Selecciona un Municipio --", ""));
          $.each(data, function(i, val){
            par.append(new Option(data[i].descripcion, data[i].clave + '-' + data[i].municipio_id));
          });
          //par.select2().select2("val", id);
        });
    },
	getLocalidadByMunicipio : function(id){
        $.ajax({
          	type : "get",
          	url: '/cat-localidad/'+ id,
        }).done(function(data){
          var par = $('#sel_localidad');
          par.html('');
          par.append(new Option("-- Selecciona una Localidad --", ""));
          $.each(data, function(i, val){
            par.append(new Option(data[i].descripcion, data[i].clave));
          });
          //par.select2().select2("val", id);
        });
    },
	getUpp : function(id){
		console.log('funcion UPPs')
        $.ajax({
          	type : "get",
          	url: '/cat-upp',
        }).done(function(data){
          var par = $('#sel_upp');
          par.html('');
          par.append(new Option("-- Selecciona una Unidad Programática --", ""));
          $.each(data, function(i, val){
            par.append(new Option(data[i].descripcion, data[i].clave + '-' +data[i].upp_id));
          });
          //par.select2().select2("val", id);
        });
    },
	getUninadResponsableByUpp : function(id){
        $.ajax({
          	type : "get",
          	url: '/cat-unidad-responsable/'+ id,
        }).done(function(data){
          var par = $('#sel_unidad_res');
          par.html('');
          par.append(new Option("-- Selecciona una Unidad Responsable --", ""));
          $.each(data, function(i, val){
            par.append(new Option(data[i].descripcion, data[i].clave + '-' + data[i].ur_id));
          });
          //par.select2().select2("val", id);
        });
    },
	getProgramaPresupuestarioByur : function(id){
        $.ajax({
          	type : "get",
          	url: '/cat-programa-presupuestario/'+ id,
        }).done(function(data){
          var par = $('#sel_programa');
          par.html('');
          par.append(new Option("-- Selecciona un Programa Presupuestario --", ""));
          $.each(data, function(i, val){
            par.append(new Option(data[i].descripcion, data[i].clave + '-'+ data[i].programa_presupuestario_id));
          });
          //par.select2().select2("val", id);
        });
    },
	getSubProgramaByProgramaId : function(id){
        $.ajax({
          	type : "get",
          	url: '/cat-subprograma-presupuesto/'+ id,
        }).done(function(data){
          var par = $('#sel_sub_programa');
          par.html('');
          par.append(new Option("-- Selecciona un Sub Programa Presupuestario --", ""));
          $.each(data, function(i, val){
            par.append(new Option(data[i].descripcion, data[i].clave + '-' + data[i].subprograma_presupuestario_id));
          });
          //par.select2().select2("val", id);
        });
    },
	getProyectoBySubPrograma : function(id){
        $.ajax({
          	type : "get",
          	url: '/cat-proyecyo/'+ id,
        }).done(function(data){
          var par = $('#sel_proyecto');
          par.html('');
          par.append(new Option("-- Selecciona un Proyecto --", ""));
          $.each(data, function(i, val){
            par.append(new Option(data[i].descripcion, data[i].clave ));
          });
          //par.select2().select2("val", id);
        });
    },
	getLineaDeAccionByUpp : function(id){
        $.ajax({
          	type : "get",
          	url: '/cat-linea-accion/'+ id,
        }).done(function(data){
          var par = $('#sel_linea');
          par.html('');
          par.append(new Option("-- Selecciona una Linea de Acción --", ""));
          $.each(data, function(i, val){
            par.append(new Option(data[i].descripcion, data[i].clave ));
          });
          //par.select2().select2("val", id);
        });
    },
	getPartidaByUpp : function(id){
        $.ajax({
          	type : "get",
          	url: '/cat-partidas/'+ id,
        }).done(function(data){
          var par = $('#sel_partida');
          par.html('');
          par.append(new Option("-- Selecciona una Partida --", ""));
          $.each(data, function(i, val){
            par.append(new Option(data[i].descripcion, data[i].clave ));
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
	$('#sel_region').change(function(e){
		e.preventDefault();
		let val = this.value;
		let id = val.substring(3,5);
		dao.getMunicipiosByRegion(id);
	});
	$('#sel_municipio').change(function(e){
		e.preventDefault();
		let val = this.value;
		let id = val.substring(4);
		dao.getLocalidadByMunicipio(id);
	});
	$('#sel_upp').change(function(e){
		e.preventDefault();
		let val = this.value;
		let id = val.substring(4);
		console.log("🚀 ~ file: init.js:213 ~ $ ~ id:", id)
		dao.getUninadResponsableByUpp(id);
		dao.getPartidaByUpp(id);
	});
	$('#sel_unidad_res').change(function(e){
		e.preventDefault();
		let val = this.value;
		let id = val.substring(3);
		dao.getProgramaPresupuestarioByur(id);
		dao.getLineaDeAccionByUpp(id);
	});
	$('#sel_programa').change(function(e){
		e.preventDefault();
		let val = this.value;
		let id = val.substring(3);
		dao.getSubProgramaByProgramaId(id);
	});
	$('#sel_sub_programa').change(function(e){
		e.preventDefault();
		let val = this.value;
		let id = val.substring(3);
		dao.getProyectoBySubPrograma(id);
	});


});