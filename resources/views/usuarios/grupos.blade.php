<section id="widget-grid">
	<div class="row">
		<article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable" id="widget-article">
			<div class="jarviswidget" id="widget" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false">
				<header>
					<span class="widget-icon"> <i class="fa fa-table"></i> </span>
					<h2>Administrar Grupos</h2>
					<input type="hidden" id="in_id_usuario" value="{{$usuario->id}}">
				</header>
				<div>
					<div class="widget-body">
						<div class="widget-body">
							<form class="form-horizontal" id="formGrupo" method="POST">
							<div class="row">
								<div class="col-md-8 col-md-offset-2">
									<fieldset>
										<section class="col col-6">
											<legend>{{$usuario->username}}</legend>

											<div class="bootstrap-duallistbox-container row">
												<select id="grupos" name="duallistbox" size="5" multiple>
													@foreach($grupos['disponibles'] as $disponible)														
														<option value="{{$disponible->id}}">{{$disponible->nombre_grupo}}</option>														
													@endforeach
													@foreach($grupos['asignados'] as $asignado)
														<option value="{{$asignado->id}}" selected>{{$asignado->nombre_grupo}}</option>														
													@endforeach
												</select>
											</div>
										</section>
									</fieldset>
								</div>
							</div>

							<div class="form-actions">
								<div class="row">
									<div class="col-md-12">
										<a class="btn btn-labeled btn-danger btnCancel" id="btnCancel" href="/#/adm-usuarios">
											<span class="btn-label"><i class="glyphicon glyphicon-arrow-left"></i></span>
											Regresar
										</a>
										<button type="button" class="btn btn-labeled btn-primary" id="btnSaveGrupos">
											<span class="btn-label"><i class="glyphicon glyphicon-ok"></i></span>
											Guardar
										</button>
									</div>
								</div>
							</div>							
							</form>
						</div>
					</div>
				</div>
			</div>
		</article>
	</div>
</section>
<script src="/assets/js/plugin/bootstrap-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
<script src="/js/administracion/usuarios/init.js"></script>
<script>
	//En casos como este, que son raros se mete un poco de código desde aqui por cuestiones de que es mas problema llamarlo desde el archivo init.js
	var initializeDuallistbox = $('#grupos').bootstrapDualListbox({
      nonSelectedListLabel: 'Grupos Disponibles',
      selectedListLabel: 'Grupos Asignados',      
      moveOnSelect: true,
      filterTextClear : 'Mostrar Todos',
      filterPlaceHolder : 'Filtrar',
      infoText : 'Total {0}',
      infoTextFiltered : "Coincidencias {0}",
      infoTextEmpty : "No hay grupos asignados"
    });

    $('#btnSaveGrupos').click(function(e){
    	e.preventDefault();
    	_grupos = [];
    	$("#bootstrap-duallistbox-selected-list_duallistbox option").each(function() {
    		_grupos.push($(this).val());
    	});
    	

    	var usuarioId = $('#in_id_usuario').val();
    	dao.guardarGrupo(_grupos, usuarioId);
    });
</script>
<script>
	$(".breadcrumb").html("<li>Inicio</li><li>Administración</li><li>Gestor Usuarios</li>");
</script>