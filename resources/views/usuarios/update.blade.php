<section id="widget-grid">
	<div class="row">
		<article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable" id="widget-article">
			<div class="jarviswidget" id="widget" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false">
				<header>
					<span class="widget-icon"> <i class="fa fa-table"></i> </span>
					<h2>Editar Información de Usuario</h2>
				</header>
				<div>
					<div class="widget-body">
						<div class="widget-body">
							<form class="form-horizontal" id="frm_update">
							<div class="row">
								<div class="col-md-10 col-md-offset-1">
									<fieldset>
										<section class="col col-6">
											<legend>Datos para editar usuario</legend>
											<input type="hidden"  id="id_user" name="id" value="{{$user->id}}">
											<input type="hidden" name="_method" value="POST" id="_method">

											<div class="form-group col-md-6">
												<label class="control-label col-md-4">Nombre de usuario</label>
												<div class="col-md-8">
													<input type="text" class="form-control" id="in_username" name="username" placeholder="Nombre de Usuario" value="{{$user->username}}">
												</div>
											</div>

											<div class="form-group col-md-6">
												<label class="control-label col-md-4">Nombre</label>
												<div class="col-md-8">
													<input type="text" class="form-control" id="in_nombre" name="nombre" value="{{$user->nombre}}">
												</div>
											</div>

											<div style="clear:both"></div>

											<div class="form-group col-md-6">
												<label class="control-label col-md-4">Primer Apellido</label>
												<div class="col-md-8">
													<input type="text" class="form-control" id="in_p_apellido" name="p_apellido" value="{{$user->p_apellido}}">
												</div>
											</div>

											<div class="form-group col-md-6">
												<label class="control-label col-md-4">Segundo Apellido</label>
												<div class="col-md-8">
													<input type="text" class="form-control" id="in_s_apellido" name="s_apellido" value="{{$user->s_apellido}}">
												</div>
											</div>

											<div style="clear:both"></div>

											<div class="form-group col-md-6">
												<label class="control-label col-md-4">Correo Electrónico</label>
												<div class="col-md-8">
													<input type="text" class="form-control" id="in_email" name="email" value="{{$user->email}}">
												</div>
											</div>

											<div class="form-group col-md-6">
												<label class="control-label col-md-4">Celular</label>
												<div class="col-md-8">
													<input type="text" class="form-control" id="in_celular" name="celular" value="{{$user->celular}}">
												</div>
											</div>

											<div style="clear:both"></div>



											<div class="form-group col-md-6">
												<label class="control-label col-md-4">Perfil</label>
												<div class="col-md-8">
													<select name="id_grupo" id="slc_perfil" class="form-control select2"></select>
												</div>
											</div>

											<div style="clear:both"></div>

										</section>
									</fieldset>
								</div>
							</div>

							<div class="form-actions">
								<div class="row">
									<div class="col-md-12">
										<a class="btn btn-labeled btn-danger btnCancel" id="btnCancel" href="#/adm-usuarios">
											<span class="btn-label"><i class="glyphicon glyphicon-arrow-left"></i></span>
											Regresar
										</a>
										<button class="btn btn-labeled btn-primary" type="button" id="btnUpdate">
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
<script src="/js/administracion/usuarios/init.js"></script>
<script>
	//En las vistas solo se llaman las funciones del archivo init


	var idPerfil = "{{$user->id_grupo}}";
	if (idPerfil != 'null' || idPerfil != null ) {
		dao.getPerfil(idPerfil);
	}else {
		dao.getPerfil("");
	}
	init.validateCreate($('#frm_create'));
</script>