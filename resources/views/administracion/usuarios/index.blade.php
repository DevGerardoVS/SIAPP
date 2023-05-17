<section id="widget-grid" class="">
	<div class="row">
		<article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
			<div color="darken" class="jarviswidget" id="wid-id-1" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false">
				<header>
					<span class="widget-icon"> <i class="fa fa-table"></i> </span>
					<h2>Usuarios - Abogados</h2>
				</header>
				<div>
					<div class="jarviswidget-editbox">
					</div>
					<div class="widget-body-toolbar">							
						<div class="row">					
							<div class="col-xs-9 col-sm-5 col-md-5 col-lg-5">								
							</div>
							<div class="col-xs-3 col-sm-7 col-md-7 col-lg-7 text-right">
								<a class="btn btn-success" id="btnNew" href="adm-usuarios/create">
									<i class="fa fa-plus"></i> <span class="hidden-mobile"> Agregar Usuario</span>
								</a>
							</div>					
						</div>							
					</div>
					<div class="widget-body no-padding">
						<div class="table-responsive">
							<table id="tbl-usuarios" class="table table-hover table-striped">
								<thead>
									<tr>
										<th data-hide="phone">Nombre Usuario</th>
										<th data-hide="phone">Correo</th>
										<th data-hide="phone">Nombre Completo</th>
										<th data-hide="phone">Celular</th>
										<th data-hide="phone">Perfil</th>
										<th data-hide="phone">Estatus</th>
										<th class="th-administration">Administración</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</article>
	</div>
</section>
<script src="/js/administracion/usuarios/init.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script>
	dao.getData();
</script>