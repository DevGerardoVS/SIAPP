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
        const clasificacionAdmin = _data[index].clasificacion_administrativa;
        const centroGestor = _data[index].entidad_federativa + _data[index].region + _data[index].municipio + _data[index].localidad + _data[index].upp + _data[index].subsecretaria + _data[index].ur;
        const areaFuncional = _data[index].finalidad + _data[index].funcion + _data[index].subfuncion + _data[index].eje + _data[index].linea_accion + _data[index].programa_sectorial + _data[index].tipologia_conac + _data[index].programa_presupuestario + _data[index].subprograma_presupuestario + _data[index].proyecto_presupuestario;
        const periodoPre = _data[index].periodo_presupuestal;
        const posicionPre = _data[index].posicion_presupuestaria;
        const fondo = _data[index].anio + _data[index].etiquetado + _data[index].fuente_financiamiento + _data[index].ramo + _data[index].fondo_ramo + _data[index].capital;
        const proyectoObra = _data[index].proyecto_obra;
        let row = _data[index].claveUr +" "+'-'+" "+ _data[index].descripcionUr +" "+'-'+" "+'Presupuesto calendarizado: ';
        let totalByClave = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(_data[index].totalByClave); 
        let id = _data[index].id;
        data.push({'id':id,'clasificacionAdmin':clasificacionAdmin, 'centroGestor':centroGestor,'areaFuncional':areaFuncional,'periodoPre': periodoPre, 'posicionPre':posicionPre,'fondo':fondo,'proyectoObra': proyectoObra,'row': row,'totalByClave': totalByClave});  
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
					+  '<a data-toggle="tooltip" title="Ver" class="btn btn-sm btn-primary">' + '<i class="fa fa-eye" style="color: aliceblue"></i></a>&nbsp;'
					+  '<a data-toggle="tooltip" title="Eliminar" class="btn btn-sm btn-danger" onclick="dao.eliminarClave(' + o.id + ')">' + '<i class="fa fa-trash" style="color: aliceblue"></i></a>&nbsp;';
				}},
				
			];
			_gen.setTableScrollGroupBy(_table, _columns, data);
		});
	},
  postCreate: function () {
    let clasificacionAdministrativa = document.getElementById('clasificacion').innerHTML;
    let entidadFederativa = document.getElementById('entidadFederativa').innerHTML;
    let region = document.getElementById('region').innerHTML;
    let municipio = document.getElementById('municipio').innerHTML;
    let localidad = document.getElementById('localidad').innerHTML;
    let upp = document.getElementById('upp').innerHTML;
    let subsecretaria = document.getElementById('subsecretaria').innerHTML;
    let ur = document.getElementById('ur').innerHTML;
    let finalidad = document.getElementById('finalidad').innerHTML;
    let funcion = document.getElementById('funcion').innerHTML;
    let subfuncion = document.getElementById('subfuncion').innerHTML;
    let eje = document.getElementById('eje').innerHTML;
    let lineaAccion = document.getElementById('lineaAccion').innerHTML;
    let programaSectorial = document.getElementById('programaSectorial').innerHTML;
    let conac = document.getElementById('conac').innerHTML;
    let programaPre = document.getElementById('programaPre').innerHTML;
    let subPrograma = document.getElementById('subPrograma').innerHTML;
    let proyectoPre = document.getElementById('proyectoPre').innerHTML;
    let mesAfectacion = document.getElementById('mesAfectacion').innerHTML;
    let capitulo = document.getElementById('capitulo').innerHTML;
    let concepto = document.getElementById('concepto').innerHTML;
    let partidaGen = document.getElementById('partidaGen').innerHTML;
    let partidaEpecifica = document.getElementById('partidaEpecifica').innerHTML;
    let tipoGasto = document.getElementById('tipoGasto').innerHTML;
    let anioFondo = document.getElementById('anioFondo').innerHTML;
    let etiquetado = document.getElementById('etiquetado').innerHTML;
    let fuenteFinanciamiento = document.getElementById('fuenteFinanciamiento').innerHTML;
    let ramo = document.getElementById('ramo').innerHTML;
    let fondoRamo = document.getElementById('fondoRamo').innerHTML;
    let capital = document.getElementById('capital').innerHTML;
    let proyectoObra = document.getElementById('proyectoObra').innerHTML;
    let enero = document.getElementById('enero').value;
    let febrero = document.getElementById('febrero').value;
    let marzo = document.getElementById('marzo').value;
    let abril = document.getElementById('abril').value;
    let mayo = document.getElementById('mayo').value;
    let junio = document.getElementById('junio').value;
    let julio = document.getElementById('julio').value;
    let agosto = document.getElementById('agosto').value;
    let septiembre = document.getElementById('septiembre').value;
    let octubre = document.getElementById('octubre').value;
    let noviembre = document.getElementById('noviembre').value;
    let diciembre = document.getElementById('diciembre').value;
    let total = document.getElementById('totalCalendarizado').value;
    let tipo = document.getElementById('tipo').value;
    let datos = [{'clasificacionAdministrativa':clasificacionAdministrativa,'entidadFederativa':entidadFederativa,'region':region, 'municipio':municipio,'localidad':localidad,'upp':upp,'subsecretaria':subsecretaria,
    'ur':ur,'finalidad':finalidad,'funcion':funcion,'subfuncion':subfuncion,'eje':eje,'lineaAccion':lineaAccion,'programaSectorial':programaSectorial,
    'conac':conac,'programaPre':programaPre,'subPrograma':subPrograma,'proyectoPre':proyectoPre,'mesAfectacion':mesAfectacion,'capitulo':capitulo,'concepto':concepto,'partidaGen':partidaGen,'partidaEpecifica':partidaEpecifica,'tipoGasto':tipoGasto,
    'anioFondo':anioFondo,'etiquetado':etiquetado,'fuenteFinanciamiento':fuenteFinanciamiento,'ramo':ramo,'fondoRamo':fondoRamo,'capital':capital,
    'proyectoObra':proyectoObra,'enero':enero,'febrero':febrero,'marzo':marzo,'abril':abril,'mayo':mayo,'junio':junio,'julio':julio,'agosto':agosto,'septiembre':septiembre,'octubre':octubre,'noviembre':noviembre,'diciembre':diciembre,'total':total,'tipo':tipo}];
      $.ajax({
        type: "POST",
        url: '/calendarizacion-guardar-clave',
        data: {'data': datos}
      }).done(function (response) {
        if (response != 'done') {
          Swal.fire(
            'Error',
            'A ocurrido un error por favor intentalo de nuevo...',
            'error'
          );
        }else{
          Swal.fire(
            'Exito',
            'Registro Exitoso',
            'success'
          );
          window.location.href = 'calendarizacion/claves';
        }
      });
  },
  eliminarClave : function (id) {
    Swal.fire({
      title: '¿Seguro que desea eliminar la clave?',
      text: "Está acción es irreversible",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'Continuar'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: "POST",
          url: '/calendarizacion-eliminar-clave',
          data: {'id':id}
        }).done(function (response) {
          if (response != 'done') {
            Swal.fire(
              'Error',
              'A ocurrido un error',
              'error'
            );
          }else{
            Swal.fire(
              'Eliminado',
              'Eliminado correctamente.',
              'success'
            );
            dao.getData("");
          }
        })
       
      }
    })
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
            par.append(new Option(data[i].region, data[i].clv_region));
          });
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
            par.append(new Option( data[i].municipio , data[i].clv_municipio));
          });
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
            par.append(new Option(data[i].localidad, data[i].clv_localidad));
          });
        });
    },
	getUpp : function(id){
        $.ajax({
          	type : "get",
          	url: '/cat-upp',
        }).done(function(data){
          var par = $('#sel_upp');
          par.html('');
          par.append(new Option("-- Selecciona una Unidad Programática --", ""));
          $.each(data, function(i, val){
            par.append(new Option(data[i].descripcion , data[i].clave));
          });
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
            par.append(new Option( data[i].ur, data[i].clv_ur));
          });
        });
    },
  getSubSecretaria :function (upp,ur) {
    $.ajax({
      type: 'get',
      url: '/cat-subSecretaria/'+ upp + '/' + ur,
    }).done(function (data) {
      document.getElementById('subsecretaria').innerHTML = data.clv_subsecretaria;
    });
  },
	getProgramaPresupuestarioByur : function(uppId,id){
        $.ajax({
          	type : "get",
          	url: '/cat-programa-presupuestario/'+ uppId + '/' + id,
        }).done(function(data){
          var par = $('#sel_programa');
          par.html('');
          par.append(new Option("-- Selecciona un Programa Presupuestario --", ""));
          $.each(data, function(i, val){
            par.append(new Option(data[i].programa, data[i].clv_programa));
          });
        });
    },
	getSubProgramaByProgramaId : function(ur,id){
        $.ajax({
          	type : "get",
          	url: '/cat-subprograma-presupuesto/'+ ur + '/'+ id,
        }).done(function(data){
          var par = $('#sel_sub_programa');
          par.html('');
          par.append(new Option("-- Selecciona un Sub Programa Presupuestario --", ""));
          $.each(data, function(i, val){
            par.append(new Option( data[i].subprograma, data[i].clv_subprograma));
          });
        });
    },
	getProyectoBySubPrograma : function(programa,id){
        $.ajax({
          	type : "get",
          	url: '/cat-proyecyo/'+ programa + '/' + id,
        }).done(function(data){
          var par = $('#sel_proyecto');
          par.html('');
          par.append(new Option("-- Selecciona un Proyecto --", ""));
          $.each(data, function(i, val){
            par.append(new Option(data[i].proyecto , data[i].clv_proyecto ));
          });
        });
    },
	getLineaDeAccionByUpp : function(uppId,id){
        $.ajax({
          	type : "get",
          	url: '/cat-linea-accion/'+ uppId + '/' + id,
        }).done(function(data){
          var par = $('#sel_linea');
          par.html('');
          par.append(new Option("-- Selecciona una Linea de Acción --", ""));
          $.each(data, function(i, val){
            par.append(new Option(data[i].linea_accion , data[i].clv_linea_accion ));
          });
        });
    },
    getAreaFuncional: function (uppId,id) {
      $.ajax({
        type:'get',
        url: '/cat-area-funcional/'+uppId +'/'+id,
      }).done(function (data) {
        document.getElementById('finalidad').innerHTML = data.clv_finalidad;
        document.getElementById('funcion').innerHTML = data.clv_funcion;
        document.getElementById('subfuncion').innerHTML = data.clv_subfuncion;
        document.getElementById('eje').innerHTML = data.clv_eje;
        document.getElementById('programaSectorial').innerHTML = data.clv_programa_sectorial;
        document.getElementById('conac').innerHTML = data.clv_tipologia_conac;
      });
    },

	getPartidaByUpp : function(id){
        $.ajax({
          	type : "get",
          	url: '/cat-partidas',
        }).done(function(data){
          var par = $('#sel_partida');
          par.html('');
          par.append(new Option("-- Selecciona una Partida --", ""));
          $.each(data, function(i, val){
            par.append(new Option(data[i].partida_especifica, data[i].clv_capitulo + data[i].clv_concepto + data[i].clv_partida_generica + data[i].clv_partida_especifica + data[i].clv_tipo_gasto ));
          });
        });
    },
    getFondosByUpp: function (id) {
      $.ajax({
        type:'get',
        url:'/cat-fondos/'+ id
      }).done(function (data) {
        var par = $('#sel_fondo');
          par.html('');
          par.append(new Option("-- Selecciona una Partida --", ""));
          $.each(data, function(i, val){
            par.append(new Option(data[i].fondo_ramo, data[i].ejercicio + data[i].clv_etiquetado + data[i].clv_fuente_financiamiento + data[i].clv_ramo + data[i].clv_fondo + data[i].clv_capital));
          });
      });
    },
    getClasificacionAdmin:function (upp,ur) {
      $.ajax({
        type:'get',
        url: '/cat-clasificacion-administrativa/'+ upp + '/' + ur,
      }).done(function (data) {
        console.log('Clasificacion Adm',data);
        let clasificacion = data.clv_sector_publico + data.clv_sector_publico_f + data.clv_sector_economia + data.clv_subsector_economia + data.clv_ente_publico;
        document.getElementById('clasificacion').innerHTML = clasificacion;
      });
    },
    getPresupuestoPorUpp: function (upp,fondo,subPrograma) {
      $.ajax({
        type:'get',
        url:'/presupuesto-upp-asignado/'+ upp +'/' + fondo + '/' + subPrograma,
      }).done(function (data) {
        console.log('presupues por upp',data);
        let presupuesto = new Intl.NumberFormat('en-US',{style:'currency', currency:'USD'}).format(data.presupuesto);
        document.getElementById('preFondo').value = presupuesto;
        let disponible = new Intl.NumberFormat('en-US',{style:'currency', currency:'USD'}).format(data.disponible);
        document.getElementById('preDisFondo').value = disponible;
      });
    },


  getPresupuesAsignado : function(){
    $.ajax({
      type: 'get',
      url: '/get-presupuesto-asignado'
    }).done(function(response){
      let totalAsignado = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(response['presupuestoAsignado'][0].totalAsignado);
      let Totcalendarizado = new Intl.NumberFormat('en-US',{style:'currency', currency:'USD'}).format(response.Totcalendarizado);
      let disponible = new Intl.NumberFormat('en-US',{style:'currency', currency:'USD'}).format(response.disponible);
      $('#asignadoUpp').val(totalAsignado);
      $('#calendarizado').val(Totcalendarizado);
      $('#disponibleUpp').val(disponible);
      $('#tipo').val(response.tipo);
      
    });

  },
  getTabla: function () {
    let clasificacionAdministrativa = document.getElementById('clasificacion').innerHTML;
    let entidadFederativa = document.getElementById('entidadFederativa').innerHTML;
    let region = document.getElementById('region').innerHTML;
    let municipio = document.getElementById('municipio').innerHTML;
    let localidad = document.getElementById('localidad').innerHTML;
    let upp = document.getElementById('upp').innerHTML;
    let subsecretaria = document.getElementById('subsecretaria').innerHTML;
    let ur = document.getElementById('ur').innerHTML;
    let finalidad = document.getElementById('finalidad').innerHTML;
    let funcion = document.getElementById('funcion').innerHTML;
    let subfuncion = document.getElementById('subfuncion').innerHTML;
    let eje = document.getElementById('eje').innerHTML;
    let lineaAccion = document.getElementById('lineaAccion').innerHTML;
    let programaSectorial = document.getElementById('programaSectorial').innerHTML;
    let conac = document.getElementById('conac').innerHTML;
    let programaPre = document.getElementById('programaPre').innerHTML;
    let subPrograma = document.getElementById('subPrograma').innerHTML;
    let proyectoPre = document.getElementById('proyectoPre').innerHTML;
    let mesAfectacion = document.getElementById('mesAfectacion').innerHTML;
    let capitulo = document.getElementById('capitulo').innerHTML;
    let concepto = document.getElementById('concepto').innerHTML;
    let partidaGen = document.getElementById('partidaGen').innerHTML;
    let partidaEpecifica = document.getElementById('partidaEpecifica').innerHTML;
    let tipoGasto = document.getElementById('tipoGasto').innerHTML;
    let anioFondo = document.getElementById('anioFondo').innerHTML;
    let etiquetado = document.getElementById('etiquetado').innerHTML;
    let fuenteFinanciamiento = document.getElementById('fuenteFinanciamiento').innerHTML;
    let ramo = document.getElementById('ramo').innerHTML;
    let fondoRamo = document.getElementById('fondoRamo').innerHTML;
    let capital = document.getElementById('capital').innerHTML;
    let proyectoObra = document.getElementById('proyectoObra').innerHTML;
    let clave = clasificacionAdministrativa + entidadFederativa + region + municipio + localidad + upp + subsecretaria + ur + finalidad + funcion +
      subfuncion + eje + lineaAccion + programaSectorial + conac + programaPre + subPrograma + proyectoPre + mesAfectacion + capitulo + concepto + partidaGen + partidaEpecifica + tipoGasto + anioFondo +
      etiquetado + fuenteFinanciamiento + ramo + fondoRamo + capital + proyectoObra;
    console.log('Clave Completa', clave);
    $.ajax({
      type: "GET",
      url: "/ver-detalle/" + clave,
      dataType: "json"
    }).done(function (data) {
      $("#detalleClave").empty();
      $("#titulo").text(`${upp} - ${data[9][2]}`);
      table = $("#detalleClave");
      let clase;
      for (let i = 0; i < data.length; i++) {
        if (i <= 11)
          clase = 'centro-gestor';
        if (i > 11 && i <= 21)
          clase = 'area-funcional';
        if (i == 22)
          clase = 'periodo-presupuestal';
        if (i > 22 && i <= 27)
          clase = 'clasificacion-economica';
        if (i > 27 && i <= 33)
          clase = 'fondo';
        if (i == 34)
          clase = 'inversion';

        $("#detalleClave").append('<tr><td class="col-md-4 text-left">' + data[i][0] + '</td><td class="col-md-1 ' + clase + '">' + data[i][1] + '</td><td class="col-md-7 text-left">' + data[i][2] + '</td></tr>');

      }
      $('detalle').show(true);
    });
  }
};
var init = {
  validateClave : function (form) {
    _gen.validate(form,{
      rules:{
        sel_region : {required:true},
        sel_municipio : {required: true},
        sel_localidad : {required: true},
        sel_upp : {required: true},
        sel_unidad_res : {required: true},
        sel_programa : {required: true},
        sel_sub_programa : {required: true},
        sel_proyecto : {required: true},
        sel_linea : {required: true},
        sel_periodo : {required: true},
        sel_partida : {required:true},
        sel_fondo : {required:true},
      },
      messages: {
        sel_region : {required:'Este campo es requerido'},
        sel_municipio : {required: 'Este campo es requerido'},
        sel_localidad : {required: 'Este campo es requerido'},
        sel_upp : {required: 'Este campo es requerido'},
        sel_unidad_res : {required: 'Este campo es requerido'},
        sel_programa : {required: 'Este campo es requerido'},
        sel_sub_programa : {required: 'Este campo es requerido'},
        sel_proyecto : {required: 'Este campo es requerido'},
        sel_linea : {required: 'Este campo es requerido'},
        sel_periodo : {required: 'Este campo es requerido'},
        sel_partida : {required:'Este campo es requerido'},
        sel_fondo : {required:'Este campo es requerido'},
      }
    })
  },
}

function calucalarCalendario() {
  var total = 0;
  $(".monto").each(function() {

    if (isNaN(parseInt($(this).val()))) {

      total += 0;

    } else {

      total +=   parseInt($(this).val());

    }

  });
  document.getElementById('totalCalendarizado').value = total;
}

$(document).ready(function(){
  $("#segundaParte").hide();
	$('#modalNewClave').modal({
        backdrop: 'static',
        keyboard: false
    });
  $('.select2').select2();
	$('#sel_region').change(function(e){
		e.preventDefault();
		let id = this.value;
    document.getElementById("region").innerHTML = id;
		dao.getMunicipiosByRegion(id);
	});
	$('#sel_municipio').change(function(e){
		e.preventDefault(); 
		let id = this.value;
    document.getElementById("municipio").innerHTML = id;
		dao.getLocalidadByMunicipio(id);
	});
  $('#sel_localidad').change(function (e) {
    let val = this.value;
    document.getElementById('localidad').innerHTML = val;
  });
	$('#sel_upp').change(function(e){
		e.preventDefault();
		let val = this.value;
    document.getElementById('upp').innerHTML = val;
		dao.getUninadResponsableByUpp(val);
    dao.getPartidaByUpp();
    dao.getFondosByUpp(val);
	});
	$('#sel_unidad_res').change(function(e){
		e.preventDefault();
		let id = this.value;
    document.getElementById('ur').innerHTML = id;
    var uppId = document.getElementById("sel_upp").value;
    dao.getSubSecretaria(uppId,id);
		dao.getProgramaPresupuestarioByur(uppId,id);
		dao.getLineaDeAccionByUpp(uppId,id);
    dao.getAreaFuncional(uppId,id);
    dao.getClasificacionAdmin(uppId,id);
	});
	$('#sel_programa').change(function(e){
		e.preventDefault();
		let id = this.value;
    document.getElementById('programaPre').innerHTML = id;
    var ur = document.getElementById("sel_unidad_res").value;
		dao.getSubProgramaByProgramaId(ur,id);
	});
	$('#sel_sub_programa').change(function(e){
		e.preventDefault();
		let id = this.value;
    document.getElementById('subPrograma').innerHTML = id;
    var programa = document.getElementById("sel_programa").value;
		dao.getProyectoBySubPrograma(programa,id);
	});
  $('#sel_proyecto').change(function (e) {
    e.preventDefault();
    let val = this.value;
    document.getElementById('proyectoPre').innerHTML = val;
  });
  $('#sel_linea').change(function (e) {
    e.preventDefault();
    let clave = this.value;
    document.getElementById('lineaAccion').innerHTML = clave;
  });
  $('#sel_periodo').change(function (e) {
    e.preventDefault();
    let clave = this.value;
    document.getElementById('mesAfectacion').innerHTML = clave;
  });
  $('#sel_partida').change(function (e) {
    e.preventDefault();
    let clave = this.value;
    let capituloClave = clave.substring(0,1);
    let conceptoClave = clave.substring(1,2);
    let partidaGen = clave.substring(2,3);
    let partidaEsp = clave.substring(3,5);
    let tipoGastoClave = clave.substring(5,6);
    document.getElementById('capitulo').innerHTML = capituloClave;
    document.getElementById('concepto').innerHTML = conceptoClave;
    document.getElementById('partidaGen').innerHTML = partidaGen;
    document.getElementById('partidaEpecifica').innerHTML = partidaEsp;
    document.getElementById('tipoGasto').innerHTML = tipoGastoClave;
  });
  $('#sel_fondo').change(function (e) {
    e.preventDefault();
    let clave = this.value;
    let anio = clave.substring(2,4);
    let etiquetado = clave.substring(4,5);
    let fuenteFinan = clave.substring(5,6);
    let ramo = clave.substring(6,8);
    let fondoRemo = clave.substring(8,10);
    let capital = clave.substring(10,11);
    document.getElementById('anioFondo').innerHTML = anio;
    document.getElementById('etiquetado').innerHTML = etiquetado;
    document.getElementById('fuenteFinanciamiento').innerHTML = fuenteFinan;
    document.getElementById('ramo').innerHTML = ramo;
    document.getElementById('fondoRamo').innerHTML = fondoRemo;
    document.getElementById('capital').innerHTML = capital;
    let upp = document.getElementById('sel_upp').value;
    let subPrograma = document.getElementById('sel_sub_programa').value;
    dao.getPresupuestoPorUpp(upp,fondoRemo,subPrograma);
  });
  $('#btnSaveClave').click(function (params) {
    params.preventDefault();
    init.validateClave($('#frm_create_clave'));
    if ($('#frm_create_clave').valid()) {
      $("#primeraParte").hide("slow");
      $("#segundaParte").show();
    }
    
  });
  $('#btnRegresar').click(function (params) {
    params.preventDefault();
    $("#segundaParte").hide("slow");
    $("#primeraParte").show();
    
  });
  $('#btnSaveAll').click(function (params) {
    params.preventDefault();
    console.log('entro en la funcion');
    let total = document.getElementById('totalCalendarizado').value;
    let disponible = document.getElementById('preDisFondo').value;
    let disp = parseInt(disponible.replaceAll(',', '').replaceAll('$', ''));
    if (total > 0 && total <= parseInt(disp)) {
      dao.postCreate();
    }else{
      if (total > 0) {
        Swal.fire(
          'Advertencia!',
          'No es posible rebasar el limite del presupuesto.',
          'warning'
        );
      }else{
        Swal.fire(
          'Advertencia!',
          'Es necesario calendarizar al menos un monto.',
          'warning'
        );
      }
     
    }
  });
  $('#verDetalle').click(function (params) {
    params.preventDefault();
    dao.getTabla();
  });
  $('#btnCancelar').click(function (params) {
    params.preventDefault();
    window.location.href = 'calendarizacion/claves';
  })
  
  

});