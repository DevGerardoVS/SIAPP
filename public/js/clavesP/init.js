var dao = {
  getData : function(ejercicio, upp, ur){
  $.ajax({
    type : "POST",
    url : "/calendarizacion-claves-get",
    dataType : "json",
    data:{'ejercicio':ejercicio,'upp':upp, 'ur':ur}
  }).done(function(_data){
    let data = [];
    let estatus = _data['estatus'] != null ? _data['estatus'].estatus : '';
    let rol = _data['rol'] != null ? _data['rol'] : 1;
    let filtroEjercicio = document.getElementById('filAnioAbierto').value;
    for (let index = 0; index < _data['claves'].length; index++) {
      let estatusVista = '';
      const clasificacionAdmin = _data['claves'][index].clasificacion_administrativa;
      const centroGestor = _data['claves'][index].entidad_federativa + _data['claves'][index].region + _data['claves'][index].municipio + _data['claves'][index].localidad + _data['claves'][index].upp + _data['claves'][index].subsecretaria + _data['claves'][index].ur;
      const areaFuncional = _data['claves'][index].finalidad + _data['claves'][index].funcion + _data['claves'][index].subfuncion + _data['claves'][index].eje + _data['claves'][index].linea_accion + _data['claves'][index].programa_sectorial + _data['claves'][index].tipologia_conac + _data['claves'][index].programa_presupuestario + _data['claves'][index].subprograma_presupuestario + _data['claves'][index].proyecto_presupuestario;
      const periodoPre = _data['claves'][index].periodo_presupuestal;
      const posicionPre = _data['claves'][index].posicion_presupuestaria;
      let upp = _data['claves'][index].claveUpp;
      let status = _data['claves'][index].estado;
      status == 1 ? estatusVista = 'Confirmadas' : estatusVista = 'Registradas';
      const fondo = _data['claves'][index].anio + _data['claves'][index].etiquetado + _data['claves'][index].fuente_financiamiento + _data['claves'][index].ramo + _data['claves'][index].fondo_ramo + _data['claves'][index].capital;
      const proyectoObra = _data['claves'][index].proyecto_obra;
      let row = upp+' - ' + _data['claves'][index].claveUr +" "+'-'+" "+ _data['claves'][index].descripcionUr +" "+'-'+" "+ estatusVista;
      let totalByClave = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(_data['claves'][index].totalByClave); 
      let id = _data['claves'][index].id;
      let estado = _data['claves'][index].estado;
      let ejercicioCheck = _data['claves'][index].ejercicio
      data.push({'id':id,'rol':rol, 'filtroEjercicio':filtroEjercicio , 'ejercicioCheck':ejercicioCheck,'clasificacionAdmin':clasificacionAdmin, 'centroGestor':centroGestor,'areaFuncional':areaFuncional,'periodoPre': periodoPre, 'posicionPre':posicionPre,'fondo':fondo,'proyectoObra': proyectoObra,'row': row,'totalByClave': totalByClave, 'estado':estado, 'estatus':estatus});  
    }
    const ejercicioActual = _data['claves'].length > 0 ? _data['claves'][0].ejercicio : document.getElementById('filAnio').value;
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
        if (o.rol == 1) {
          if (o.estatus != 'Cerrado' && o.estatus != '') {
            if (o.estado == 0) {
              return '<a data-toggle="tooltip" title="Modificar" class="btn btn-sm btn-success" href="/clave-update/'+o.id+'" >' + '<i class="fa fa-pencil" style="color: aliceblue"></i></a>&nbsp;'
            +  '<a data-toggle="tooltip" title="Eliminar" class="btn btn-sm btn-danger" onclick="dao.eliminarClave(' + o.id + ')">' + '<i class="fa fa-trash" style="color: aliceblue"></i></a>&nbsp;';
            }else{
              return '<p><i class="fa fa-check">&nbsp;Confirmado</i></p>';
            }
          }else{
            return '<p><i class="fa fa-ban">&nbsp;Cerrado</i></p>';
          }
        }else{
          if (o.filtroEjercicio == o.ejercicioCheck) {
            return '<a data-toggle="tooltip" title="Modificar" class="btn btn-sm btn-success" href="/clave-update/'+o.id+'" >' + '<i class="fa fa-pencil" style="color: aliceblue"></i></a>&nbsp;'
            +  '<a data-toggle="tooltip" title="Eliminar" class="btn btn-sm btn-danger" onclick="dao.eliminarClave(' + o.id + ')">' + '<i class="fa fa-trash" style="color: aliceblue"></i></a>&nbsp;';  
          }else{
            return '<p><i class="fa fa-ban">&nbsp;Cerrado</i></p>';
          }
        }
      }},
    ];
    _gen.setTableScrollGroupBy(_table, _columns, data);
   
    dao.getPresupuesAsignado(ejercicioActual,upp);
    $("#filtro_anio option[value="+ ejercicioActual +"]").attr("selected",true);
    
    
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
    let ejercicio = document.getElementById('anio').value;
    let datos = [{'clasificacionAdministrativa':clasificacionAdministrativa,'entidadFederativa':entidadFederativa,'region':region, 'municipio':municipio,'localidad':localidad,'upp':upp,'subsecretaria':subsecretaria,
    'ur':ur,'finalidad':finalidad,'funcion':funcion,'subfuncion':subfuncion,'eje':eje,'lineaAccion':lineaAccion,'programaSectorial':programaSectorial,
    'conac':conac,'programaPre':programaPre,'subPrograma':subPrograma,'proyectoPre':proyectoPre,'mesAfectacion':mesAfectacion,'capitulo':capitulo,'concepto':concepto,'partidaGen':partidaGen,'partidaEpecifica':partidaEpecifica,'tipoGasto':tipoGasto,
    'anioFondo':anioFondo,'etiquetado':etiquetado,'fuenteFinanciamiento':fuenteFinanciamiento,'ramo':ramo,'fondoRamo':fondoRamo,'capital':capital,
    'proyectoObra':proyectoObra,'enero':enero,'febrero':febrero,'marzo':marzo,'abril':abril,'mayo':mayo,'junio':junio,'julio':julio,'agosto':agosto,'septiembre':septiembre,'octubre':octubre,'noviembre':noviembre,'diciembre':diciembre,'total':total,'tipo':tipo}];
      $.ajax({
        type: "POST",
        url: '/calendarizacion-guardar-clave',
        data: {'data': datos,'ejercicio':ejercicio}
      }).done(function (response) {
        if (response != 'done') {
          switch (response) {
            case 'duplicado':
              Swal.fire(
                'Advertencia',
                'Esta clave ya ha sido registrada.',
                'warning'
              );
              break;
              case 'cantidadNoDisponible':
              Swal.fire(
                'Advertencia',
                'No es posible rebasar el limite del presupuesto.',
                'warning'
              );
              break;
          
            default:
              Swal.fire(
                'Error',
                'A ocurrido un error por favor intentalo de nuevo...',
                'error'
              );
              break;
          }
        }else{
          Swal.fire(
            'Exito',
            'Registro Exitoso',
            'success'
          );
          $("#filtro_anio option[value="+ ejercicio +"]").attr("selected",true);
          window.location.href = '/calendarizacion/claves';
        }
      });
  },
  postUpdate : function () {
    let idClave = document.getElementById('idClave').value;
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
    let datos = [{'idClave':idClave,'enero':enero,'febrero':febrero,'marzo':marzo,'abril':abril,'mayo':mayo,'junio':junio,'julio':julio,'agosto':agosto,'septiembre':septiembre,'octubre':octubre,'noviembre':noviembre,'diciembre':diciembre,'total':total}];
    $.ajax({
      type: "POST",
      url: '/calendarizacion-editar-clave',
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
        window.location.href = '/calendarizacion/claves';
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
            let ejercicio = document.getElementById('filtro_anio').value;
            let upp = document.getElementById('filtro_upp').value;
            let ur = document.getElementById('filtro_ur').value;
            dao.getData(ejercicio,upp,ur);
            //dao.getPresupuesAsignado('');
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
            if (val.clv_region == id) {
              par.append(new Option(data[i].clv_region+ ' - '+data[i].region, data[i].clv_region,true,true));
              document.getElementById("region").innerHTML = data[i].clv_region;
            }else{
              par.append(new Option(data[i].clv_region+ ' - '+data[i].region, data[i].clv_region,false,false));
            }
            
          });
        });
  },
	getMunicipiosByRegion : function(id,idSelected){
        $.ajax({
          	type : "get",
          	url: '/cat-municipios/'+ id,
        }).done(function(data){
          var par = $('#sel_municipio');
          par.html('');
          par.append(new Option("-- Selecciona un Municipio --", ""));
          $.each(data, function(i, val){
            if (idSelected != '' && val.clv_municipio == idSelected) {
              par.append(new Option(data[i].clv_municipio+ ' - '+ data[i].municipio , data[i].clv_municipio,true,true));
              document.getElementById("municipio").innerHTML = data[i].clv_municipio;
            }else{
              par.append(new Option(data[i].clv_municipio+ ' - '+ data[i].municipio , data[i].clv_municipio,false,false));
            }
          });
        });
  },
	getLocalidadByMunicipio : function(id, idSelected){
        $.ajax({
          	type : "get",
          	url: '/cat-localidad/'+ id,
        }).done(function(data){
          var par = $('#sel_localidad');
          par.html('');
          par.append(new Option("-- Selecciona una Localidad --", ""));
          $.each(data, function(i, val){
            if (idSelected != '' && val.clv_localidad == idSelected) {
              par.append(new Option(data[i].clv_localidad+ ' - '+ data[i].localidad, data[i].clv_localidad,true,true));
              document.getElementById('localidad').innerHTML = data[i].clv_localidad;
            }else{
              par.append(new Option(data[i].clv_localidad+ ' - '+ data[i].localidad, data[i].clv_localidad,false,false));
            }
          });
        });
  },
	getUpp : function(ejercicio,id){
        $.ajax({
          	type : "get",
          	url: '/cat-upp/'+ ejercicio,
        }).done(function(data){
          var par = $('#sel_upp');
          par.html('');
          par.append(new Option("-- Selecciona una Unidad Programática --", ""));
          $.each(data, function(i, val){
            if (id != '' && val.clave == id) {
             par.append(new Option(data[i].clv_upp+ ' - '+ data[i].upp , data[i].clv_upp,true,true));
             document.getElementById('upp').innerHTML = data[i].clave;
            }else{
             par.append(new Option(data[i].clv_upp+ ' - '+ data[i].upp , data[i].clv_upp,false,false));
            }
          });
        });
  },
	getUninadResponsableByUpp : function(id,ejercicio,idSelected){
        $.ajax({
          	type : "get",
          	url: '/cat-unidad-responsable/'+ id+'/'+ejercicio,
        }).done(function(data){
          var par = $('#sel_unidad_res');
          par.html('');
          par.append(new Option("-- Selecciona una Unidad Responsable --", ""));
          $.each(data, function(i, val){
            if (idSelected != '' && val.clv_ur == idSelected) {
              par.append(new Option(data[i].clv_ur+ ' - '+ data[i].ur, data[i].clv_ur,true,true));
              document.getElementById('ur').innerHTML = data[i].clv_ur;
              document.getElementById('lbl_ur').innerText ='Ur: ' + data[i].ur;
             }else{
              par.append(new Option(data[i].clv_ur+ ' - '+ data[i].ur, data[i].clv_ur,false,false));
             }
            
          });
        });
  },
  getSubSecretaria :function (upp,ur,ejercicio) {
    $.ajax({
      type: 'get',
      url: '/cat-subSecretaria/'+ upp + '/' + ur + '/'+ejercicio,
    }).done(function (data) {
      document.getElementById('subsecretaria').innerHTML = data.clv_subsecretaria;
    });
  },
	getProgramaPresupuestarioByur : function(uppId,id,ejercicio,idSelected){
        $.ajax({
          	type : "get",
          	url: '/cat-programa-presupuestario/'+ uppId + '/' + id + '/'+ ejercicio,
        }).done(function(data){
          var par = $('#sel_programa');
          par.html('');
          par.append(new Option("-- Selecciona un Programa Presupuestario --", ""));
          $.each(data, function(i, val){
            if (idSelected != '' && val.clv_programa == idSelected) {
              par.append(new Option(data[i].clv_programa+ ' - '+ data[i].programa, data[i].clv_programa,true,true));
              document.getElementById('programaPre').innerHTML = data[i].clv_programa;
             }else{
              par.append(new Option(data[i].clv_programa+ ' - '+ data[i].programa, data[i].clv_programa,false,false));
             }
            
          });
        });
  },
	getSubProgramaByProgramaId : function(ur,id, upp,ejercicio,idSelected){
        $.ajax({
          	type : "get",
          	url: '/cat-subprograma-presupuesto/'+ ur + '/'+ id + '/'+ upp + '/' + ejercicio,
        }).done(function(data){
          var par = $('#sel_sub_programa');
          par.html('');
          par.append(new Option("-- Selecciona un Sub Programa Presupuestario --", ""));
          $.each(data, function(i, val){
            if (idSelected != '' && val.clv_subprograma == idSelected) {
              par.append(new Option(data[i].clv_subprograma+ ' - '+ data[i].subprograma, data[i].clv_subprograma,true,true));
              document.getElementById('subPrograma').innerHTML = data[i].clv_subprograma;
             }else{
              par.append(new Option(data[i].clv_subprograma+ ' - '+ data[i].subprograma, data[i].clv_subprograma,false,false));
             }
            
          });
        });
  },
	getProyectoBySubPrograma : function(programa,id, upp, ur,ejercicio,idSelected){
        $.ajax({
          	type : "get",
          	url: '/cat-proyecyo/'+ programa + '/' + id+'/'+ upp + '/' + ur+'/' +ejercicio,
        }).done(function(data){
          var par = $('#sel_proyecto');
          par.html('');
          par.append(new Option("-- Selecciona un Proyecto --", ""));
          $.each(data, function(i, val){
            if (idSelected != '' && val.clv_proyecto == idSelected) {
              par.append(new Option(data[i].clv_proyecto+ ' - '+ data[i].proyecto , data[i].clv_proyecto,true,true));
              document.getElementById('proyectoPre').innerHTML = data[i].clv_proyecto;
             }else{
              par.append(new Option(data[i].clv_proyecto+ ' - '+  data[i].proyecto , data[i].clv_proyecto,false,false));
             }
            
          });
        });
  },
	getLineaDeAccionByUpp : function(uppId,id,ejercicio,idSelected){
        $.ajax({
          	type : "get",
          	url: '/cat-linea-accion/'+ uppId + '/' + id+'/'+ejercicio,
        }).done(function(data){
          var par = $('#sel_linea');
          par.html('');
          par.append(new Option("-- Selecciona una Linea de Acción --", ""));
          $.each(data, function(i, val){
            if (idSelected != '' && val.clv_linea_accion == idSelected) {
              par.append(new Option(data[i].clv_linea_accion+ ' - '+  data[i].linea_accion , data[i].clv_linea_accion,true,true));
              document.getElementById('lineaAccion').innerHTML = data[i].clv_linea_accion ;
              let periodo = '01-ENE';
              document.getElementById('mesAfectacion').innerHTML = periodo;
             }else{
              par.append(new Option(data[i].clv_linea_accion+ ' - '+ data[i].linea_accion , data[i].clv_linea_accion,false,false));
             }
          });
        });
  },
  getAreaFuncional: function (uppId,id,ejercicio) {
    $.ajax({
      type:'get',
      url: '/cat-area-funcional/'+uppId +'/'+id+'/'+ejercicio,
    }).done(function (data) {
      document.getElementById('finalidad').innerHTML = data.clv_finalidad;
      document.getElementById('funcion').innerHTML = data.clv_funcion;
      document.getElementById('subfuncion').innerHTML = data.clv_subfuncion;
      document.getElementById('eje').innerHTML = data.clv_eje;
      document.getElementById('programaSectorial').innerHTML = data.clv_programa_sectorial;
      document.getElementById('conac').innerHTML = data.clv_tipologia_conac;
    });
  },
	getPartidaByUpp : function(clasificacion,id){
        $.ajax({
          	type : "get",
          	url: '/cat-partidas/'+clasificacion,
        }).done(function(data){
          var par = $('#sel_partida');
          par.html('');
          par.append(new Option("-- Selecciona una Partida --", ""));
          $.each(data, function(i, val){
            let partida = val.clv_capitulo + val.clv_concepto + val.clv_partida_generica + val.clv_partida_especifica + val.clv_tipo_gasto;
            if (id != '' && partida == id) {
              par.append(new Option(partida + '-'+ data[i].partida_especifica, data[i].clv_capitulo + data[i].clv_concepto + data[i].clv_partida_generica + data[i].clv_partida_especifica + data[i].clv_tipo_gasto,true,true));
              document.getElementById('capitulo').innerHTML = data[i].clv_capitulo;
              document.getElementById('concepto').innerHTML = data[i].clv_concepto;
              document.getElementById('partidaGen').innerHTML = data[i].clv_partida_generica;
              document.getElementById('partidaEpecifica').innerHTML = data[i].clv_partida_especifica;
              document.getElementById('tipoGasto').innerHTML = data[i].clv_tipo_gasto;
             }else{
              par.append(new Option(partida+ '-'+ data[i].partida_especifica, data[i].clv_capitulo + data[i].clv_concepto + data[i].clv_partida_generica + data[i].clv_partida_especifica + data[i].clv_tipo_gasto,false,false));
             }
          });
        });
  },
  getFondosByUpp: function (id,subP, ejercicio,idSelected) {
    $.ajax({
      type:'get',
      url:'/cat-fondos/'+ id + '/'+ subP +'/'+ejercicio,
    }).done(function (data) {
      var par = $('#sel_fondo');
        par.html('');
        par.append(new Option("-- Selecciona un Fondo --", ""));
        $.each(data, function(i, val){
          let fondo = val.ejercicio + val.clv_etiquetado + val.clv_fuente_financiamiento + val.clv_ramo + val.clv_fondo + val.clv_capital;
          if (idSelected != '' && fondo == idSelected) {
            par.append(new Option(data[i].clv_fondo+ '-'+ data[i].fondo_ramo, data[i].ejercicio + data[i].clv_etiquetado + data[i].clv_fuente_financiamiento + data[i].clv_ramo + data[i].clv_fondo + data[i].clv_capital,true,true));
            let ejercicio = data[i].ejercicio;
            let anioText = ejercicio.toString();
            let anio = anioText.substring(2,4);
            document.getElementById('anioFondo').innerHTML = anio;
            document.getElementById('etiquetado').innerHTML = data[i].clv_etiquetado;
            document.getElementById('fuenteFinanciamiento').innerHTML = data[i].clv_fuente_financiamiento;
            document.getElementById('ramo').innerHTML = data[i].clv_ramo;
            document.getElementById('fondoRamo').innerHTML = data[i].clv_fondo;
            document.getElementById('capital').innerHTML = data[i].clv_capital;
            document.getElementById('lbl_fondo').innerText ='Fondo: ' + data[i].fondo_ramo;
            }else{
            par.append(new Option(data[i].clv_fondo+ '-'+  data[i].fondo_ramo, data[i].ejercicio + data[i].clv_etiquetado + data[i].clv_fuente_financiamiento + data[i].clv_ramo + data[i].clv_fondo + data[i].clv_capital,false,false));
            }
        });
    });
  },
  getClasificacionAdmin:function (upp,ur) {
    $.ajax({
      type:'get',
      url: '/cat-clasificacion-administrativa/'+ upp + '/' + ur,
    }).done(function (data) {
      let clasificacion = data.clv_sector_publico + data.clv_sector_publico_f + data.clv_sector_economia + data.clv_subsector_economia + data.clv_ente_publico;
      document.getElementById('clasificacion').innerHTML = clasificacion;
      dao.getPartidaByUpp(clasificacion,'');
    });
  },
  getPresupuestoPorUpp: function (upp,fondo,subPrograma,ejercicio) {
    $.ajax({
      type:'get',
      url:'/presupuesto-upp-asignado/'+ upp +'/' + fondo + '/' + subPrograma + '/' + ejercicio,
    }).done(function (data) {
      let presupuesto = new Intl.NumberFormat('en-US',{style:'currency', currency:'USD'}).format(data.presupuesto);
      document.getElementById('preFondo').value = presupuesto;
      let disponible = new Intl.NumberFormat('en-US',{style:'currency', currency:'USD'}).format(data.disponible);
      document.getElementById('preDisFondo').value = disponible;
    });
  },
  getPresupuestoPorUppEdit: function (upp,fondo,subPrograma,ejercicio) {
    $.ajax({
      type:'get',
      url:'/presupuesto-upp-asignado-edit/'+ upp +'/' + fondo + '/' + subPrograma + '/' + ejercicio+ '/' + id,
    }).done(function (data) {
      let presupuesto = new Intl.NumberFormat('en-US',{style:'currency', currency:'USD'}).format(data.presupuesto);
      document.getElementById('preFondo').value = presupuesto;
      let disponible = new Intl.NumberFormat('en-US',{style:'currency', currency:'USD'}).format(data.disponible);
      document.getElementById('preDisFondo').value = disponible;
      document.getElementById('calendarizado').value = data.calendarizado;

    });
  },
  getPresupuesAsignado : function(ejercicio, upp){
    $.ajax({
      type: 'get',
      url: '/get-presupuesto-asignado/'+ ejercicio+'/'+upp,
    }).done(function(response){
      let ejercicioActual = document.getElementById('filAnioAbierto').value;
      let totalAsignado = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(response['presupuestoAsignado'][0].totalAsignado);
      let Totcalendarizado = new Intl.NumberFormat('en-US',{style:'currency', currency:'USD'}).format(response.Totcalendarizado);
      let disponible = new Intl.NumberFormat('en-US',{style:'currency', currency:'USD'}).format(response.disponible);
      $('#asignadoUpp').val(totalAsignado);
      $('#calendarizado').val(Totcalendarizado);
      $('#disponibleUpp').val(disponible);

      if (response.rol == 1) {
        if (response.estatus != null && response.estatus.estatus && response.estatus.estatus == 'Abierto') {
          if (response.Totcalendarizado == response['presupuestoAsignado'][0].totalAsignado && response.disponible == 0  && response['presupuestoAsignado'][0].totalAsignado > 0) {
            $('#btnNuevaClave').hide(true);
            $('#btn_confirmar').show(true);
          }else{
            if (response['presupuestoAsignado'][0].totalAsignado && response['presupuestoAsignado'][0].totalAsignado > 0) {
              $('#btnNuevaClave').show(true);
            }else{
              $('#btnNuevaClave').hide(true);
            }
            
          }
        }else{
            $('#btnNuevaClave').hide(true);
            $('#btn_confirmar').hide(true);
        }
      }else{
        if (response.estatus != null && response.estatus.ejercicio && response.estatus.ejercicio == ejercicioActual) {
          if (response.Totcalendarizado == response['presupuestoAsignado'][0].totalAsignado && response.disponible == 0  && response['presupuestoAsignado'][0].totalAsignado > 0) {
            $('#btnNuevaClave').hide(true);
            $('#btn_confirmar').show(true);
          }else{
            if (response['presupuestoAsignado'][0].totalAsignado && response['presupuestoAsignado'][0].totalAsignado > 0) {
              $('#btnNuevaClave').show(true);
              $('#btn_confirmar').hide(true);
            }else{
              $('#btnNuevaClave').hide(true);
            }
          }
        }else{
          $('#btnNuevaClave').hide(true);
          $('#btn_confirmar').hide(true);
        }
        
      }
      
      
    });

  },
  getSector: function (clave) {
    $.ajax({
      type:'get',
      url:'/calendarizacion-get-sector/'+ clave,
    }).done(function(response){
      document.getElementById('lbl_sector').innerText = 'Sector: ' + response.sector;
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
    $.ajax({
      type: "GET",
      url: "/ver-detalle/" + clave + "/" + anioFondo,
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
  },
  getDetallePresupuestoByFondo : function (ejercicio,clvUpp) {
    $.ajax({
      type : 'get',
      url: '/calendarizacion-claves-presupuesto-fondo/'+ejercicio+'/'+clvUpp,
      dataType : "JSON"
    }).done(function (response) {
      let data = [];
      for (let index = 0; index < response.fondos.length; index++) {
        const clv_fondo = response.fondos[index].clv_fondo;
        const fondo_ramo = response.fondos[index].fondo_ramo;
        const Operativo = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(response.fondos[index].Operativo);
        const RH = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(response.fondos[index].RH);
        const techos_presupuestal =  new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(response.fondos[index].techos_presupuestal);
        const calendarizado =  new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(response.fondos[index].calendarizado);
        const disponible =  new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(response.fondos[index].disponible);
        let ejercicio = response.fondos[index].ejercicio
        data.push({'clv_fondo':clv_fondo,'fondo_ramo':fondo_ramo,'Operativo':Operativo,'RH':RH ,'techos_presupuestal':techos_presupuestal,'calendarizado':calendarizado,'disponible':disponible, 'ejercicio':ejercicio});  
      }
      document.getElementById('titleModalpresupuesto').innerText = response.upp['clave'] + ' - ' +response.upp['descripcion']; 
      _table = $("#tblPresupuestos");
			_columns = [
				{"aTargets" : [0], "mData" : 'clv_fondo'},
				{"aTargets" : [1], "mData" : "fondo_ramo"},
				{"aTargets" : [2], "mData" : "Operativo"},
				{"aTargets" : [3], "mData" : "RH"},
				{"aTargets" : [4], "mData" : "techos_presupuestal"},
				{"aTargets" : [5], "mData" : "calendarizado"},
        {"aTargets" : [6], "mData" : "disponible"},
        {"aTargets" : [7], "mData" : "ejercicio"},
			];
			_gen.setTableScrollFotter(_table, _columns, data);
      $('modalPresupuesto').show(true);
    });
  },
  confirmarClaves: function (upp,ejercicio) {
    Swal.fire({
      title: '¿Esstás seguro?',
      text: "Se recomienda que revises tu información antes de confirmar, una vez confirmadas tus claves no podrás editar.",
      icon: 'warning',
      showCancelButton: true,
      cancelButtonColor: '#800e3a',
      confirmButtonColor: '#000dff',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'Confirmar'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          type: "POST",
          url: '/calendarizacion-confirmar-claves',
          data: {'upp':upp, 'ejercicio':ejercicio}
        }).done(function (response) {
          if (response != 'done') {
            Swal.fire(
              'Error',
              'A ocurrido un error intentelo nuevamente o contacte al administrador',
              'error'
            );
          }else{
            Swal.fire(
              'Confirmado',
              'Confirmado de claves realizado correctamente.',
              'success'
            );
            window.location.href = '/calendarizacion/metas';
          }
        })
      }
    })
  },
  filtroUpp : function(ejercicio,id){
    $.ajax({
        type : "get",
        url: '/cat-upp/'+ejercicio,
    }).done(function(data){
      var par = $('#filtro_upp');
      par.html('');
      par.append(new Option("-- Selecciona una Unidad Programática --", ""));
      $.each(data, function(i, val){
        if (id != '' && val.clave == id) {
         par.append(new Option(data[i].clv_upp+ ' - '+ data[i].upp , data[i].clv_upp,true,true));
         document.getElementById('upp').innerHTML = data[i].clave;
        }else{
         par.append(new Option(data[i].clv_upp+ ' - '+ data[i].upp , data[i].clv_upp,false,false));
        }
      });
    });
  },
  filtroUr : function(id, ejercicio){
    $.ajax({
      type : "get",
      url: '/cat-unidad-responsable/'+ id+'/'+ejercicio,
  }).done(function(data){
    var par = $('#filtro_ur');
    par.html('');
    par.append(new Option("-- Selecciona una Unidad Responsable --", ""));
    $.each(data, function(i, val){
      if (id != '' && val.clv_ur == id) {
        par.append(new Option(data[i].clv_ur+ '-'+ data[i].ur, data[i].clv_ur,true,true));
      }else{
        par.append(new Option(data[i].clv_ur+ '-'+ data[i].ur, data[i].clv_ur,false,false));
      }
    });
  });
  },
  getObras: function(val, idSelected = ''){
    $.ajax({
      type: "get",
      url: '/cat-obras/'+ val,
    }).done(function (data) {
      if (data.permisoObra == 200) {
        $('#obras').show('');
        var par = $('#sel_obra');
        par.html('');
        par.append(new Option("-- Selecciona un Proyecto - Obra --", ""));
        $.each(data.obras, function(i, val){
          if (idSelected != '' && val.clv_proyecto_obra == idSelected) {
            par.append(new Option(data.obras[i].clv_proyecto_obra+ '-'+ data.obras[i].proyecto_obra , data.obras[i].clv_proyecto_obra,true,true));
            document.getElementById('proyectoObra').innerHTML = data.obras[i].proyecto_obra ;
           }else{
            par.append(new Option(data.obras[i].clv_proyecto_obra+ '-'+ data.obras[i].proyecto_obra , data.obras[i].clv_proyecto_obra,false,false));
           }
        });
        
      }else{
        $('#obras').hide(true);
        document.getElementById('proyectoObra').innerHTML = '000000' ;
      }
    });
  },
  getEjercicios: function (id) {
    $.ajax({
      type:'get',
      url:'/get-ejercicios'
    }).done(function (data) {
      var par = $('#filtro_anio');
      par.html('');
      par.append(new Option("-- Selecciona un Ejercicio --", ""));
      $.each(data, function(i, val){
        if (id != '' && data[i].ejercicio  == id) {
         par.append(new Option(data[i].ejercicio , data[i].ejercicio,true,true));
        }else{
         par.append(new Option(data[i].ejercicio , data[i].ejercicio,false,false));
        }
      });
    });
  },

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
  validaFiltroUpp : function (form) {
    _gen.validate(form,{
      rules:{
        filtro_upp : {required:true},
      },
      messages: {
        filtro_upp : {required:'Este campo es requerido'},
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
  $('.select2').select2({
    theme: "classic"
  });
	$('#sel_region').change(function(e){
		e.preventDefault();
		let id = this.value;
    document.getElementById("region").innerHTML = id;
		dao.getMunicipiosByRegion(id,'');
	});
	$('#sel_municipio').change(function(e){
		e.preventDefault(); 
		let id = this.value;
    document.getElementById("municipio").innerHTML = id;
		dao.getLocalidadByMunicipio(id,'');
	});
  $('#sel_localidad').change(function (e) {
    let val = this.value;
    document.getElementById('localidad').innerHTML = val;
  });
	$('#sel_upp').change(function(e){
		e.preventDefault();
		let val = this.value;
    document.getElementById('upp').innerHTML = val;
    let ejercicio = document.getElementById('anio').value;
		dao.getUninadResponsableByUpp(val,ejercicio,'');
    dao.getObras(val);
	});
	$('#sel_unidad_res').change(function(e){
		e.preventDefault();
		let id = this.value;
    document.getElementById('ur').innerHTML = id;
    var uppId = document.getElementById("sel_upp").value;
    let ejercicio = document.getElementById('anio').value;
    dao.getSubSecretaria(uppId,id,ejercicio);
		dao.getProgramaPresupuestarioByur(uppId,id,ejercicio,'');
		dao.getLineaDeAccionByUpp(uppId,id,ejercicio,'');
    dao.getAreaFuncional(uppId,id,ejercicio);
    dao.getClasificacionAdmin(uppId,id);
    var urText = $('#sel_unidad_res').find(":selected").text();
    document.getElementById('lbl_ur').innerText = 'Ur: ' + urText;
	});
	$('#sel_programa').change(function(e){
		e.preventDefault();
		let id = this.value;
    document.getElementById('programaPre').innerHTML = id;
    var upp = document.getElementById("sel_upp").value;
    var ur = document.getElementById("sel_unidad_res").value;
    let ejercicio = document.getElementById('anio').value;
		dao.getSubProgramaByProgramaId(ur,id, upp,ejercicio,'');
	});
	$('#sel_sub_programa').change(function(e){
		e.preventDefault();
		let id = this.value;
    document.getElementById('subPrograma').innerHTML = id;
    var programa = document.getElementById("sel_programa").value;
    var upp = document.getElementById("sel_upp").value;
    var ejercicio = document.getElementById('anio').value;
    var ur = document.getElementById("sel_unidad_res").value;
		dao.getProyectoBySubPrograma(programa,id,upp,ur ,ejercicio,'');
    dao.getFondosByUpp(upp, id, ejercicio,'');
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
    dao.getSector(clave);
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
    let ejercicio = $('#anio').val();
    dao.getPresupuestoPorUpp(upp,fondoRemo,subPrograma, ejercicio);
    var fondoText = $('#sel_fondo').find(":selected").text();
    document.getElementById('lbl_fondo').innerText = 'Fondo: ' + fondoText;
  });
  $('#sel_obra').change(function (e) {
    e.preventDefault();
    let clave = this.value;
    document.getElementById('proyectoObra').innerHTML = clave;
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
    window.location.href = '/calendarizacion/claves';
  });
  $('#btnCancelarUpdate').click(function (params) {
    params.preventDefault();
    window.location.href = '/calendarizacion/claves';
  });

  $('#btnUpdateClv').click(function (params) {
    params.preventDefault();
    let calendarizado = document.getElementById('calendarizado').value;
   calendarizado = parseInt(calendarizado);
    let total = document.getElementById('totalCalendarizado').value;
    let disponible = document.getElementById('preDisFondo').value;
    let disp = parseInt(disponible.replaceAll(',', '').replaceAll('$', ''));
    disp = disp + calendarizado;
    if (total > 0 && total <= parseInt(disp)) {
      dao.postUpdate();
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
  $('#presupuestoFondo').click(function () {
    let ejercicio = document.getElementById('filtro_anio').value;
    let clvUpp = document.getElementById('filtro_upp').value;
    dao.getDetallePresupuestoByFondo(ejercicio,clvUpp);
  });
  $('#btn_confirmar').click(function () {
    let upp = document.getElementById('filUpp').value;
    let ejercicio = document.getElementById('filAnio').value;
      init.validaFiltroUpp($('#filtrosClaves'));
      if ($('#filtrosClaves').valid()) {
        dao.confirmarClaves(upp,ejercicio);
      }
  });
  $('#filtro_anio').change(function(e){
		e.preventDefault();
		let id = this.value;
    dao.filtroUpp(id,'');
    document.getElementById('filAnio').value = id;
    let upp = document.getElementById('filUpp').value;
    let ur = document.getElementById('filtro_ur').value;
    if (upp && upp != '') {
      dao.filtroUr(upp,id);
    }
    dao.getData(id,upp,ur);
	});
  $('#filtro_upp').change(function (e) {
    e.preventDefault();
    let upp = this.value;
    let ejercicio = document.getElementById('filtro_anio').value;
    document.getElementById('filUpp').value = upp;
    let ur = document.getElementById('filtro_ur').value;
    dao.getData(ejercicio,upp,ur);
    dao.filtroUr(upp,ejercicio);
  });
  $('#filtro_ur').change(function (e) {
    e.preventDefault();
    let ur = this.value;
    let ejercicio = document.getElementById('filtro_anio').value;
    //let upp = document.getElementById('filtro_upp').value;
    let upp = document.getElementById('filUpp').value;
    //dao.filtroUr(upp,ejercicio);
    dao.getData(ejercicio,upp,ur);
  });
  $('#btnNuevaClave').click(function (e) {
    let ejercicio = document.getElementById('filtro_anio').value;
    window.location.href = '/calendarizacion-claves-create/'+ejercicio;
  });
  

});