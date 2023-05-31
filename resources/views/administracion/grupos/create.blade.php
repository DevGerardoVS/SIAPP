@extends('layouts.app')
@section('content')
<div class="container">
    <section id="widget-grid">
	<div class="row">
		<article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable" id="widget-article">
			<div class="jarviswidget" id="widget" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false">
				<header>
					<h2>Crear Nuevo Grupo</h2>
				</header>
				<div>
					<div class="widget-body">
						<div class="widget-body">
							<form class="form-horizontal" id="frmCreate">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8 col-md-offset-2">
                                        <fieldset>
                                            <section class="col col-6">
                                                <legend></legend>

                                                <div class="form-group">
                                                    <label class="control-label col-md-4">Nombre Grupo</label>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" id="nombre" name="nombre_grupo" placeholder="Nombre de Grupo">
                                                    </div>
                                                </div>

                                            </section>
                                        </fieldset>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <a class="btn btn-labeled btn-danger btnCancel" id="btnCancel" href="/adm-grupos">
                                                <span class="btn-label"><i class="glyphicon glyphicon-arrow-left"></i></span>
                                                Regresar
                                            </a>
                                            <button class="btn btn-labeled btn-primary" type="button" id="btnSave">
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
</div>

<script>
    $(document).ready(function () {
       $('#btnSave').on('click',function (e) {
           e.preventDefault()

           $.ajax({
               url:"{{route('postStore')}}",
               type: "POST",
               data: {
                   "_token": "{{ csrf_token() }}",
                   "nombre": $('#nombre').val()
               },
               success:function(response){
                   console.log(response)
                   if (response == "done") {
                       window.location.href = '/adm-grupos';
                   }
               },
               error: function(response) {
                   console.log('Error: ' + response);
               }
           });
       })
    });
</script>
@endsection