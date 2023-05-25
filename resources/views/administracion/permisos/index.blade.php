@extends('layouts.app')
@section('content')
<div class="container">
    <section id="widget-grid">
        <div class="row">
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable" id="widget-article">
                <div class="jarviswidget" id="widget" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false">
                    <header>
                        <h2>Administración de Permisos</h2>
                    </header>
                    <div>
                        <div class="widget-body">
                            <div class="widget-body">
                                <form class="form-horizontal" id="formCreate">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-8 col-md-offset-2">
                                            <fieldset>
                                                <section class="col col-6">
                                                    <legend class="font-lg"> <span class="label label-info"> {{$data['grupo']->nombre_grupo}} </span> </legend>

                                                    <input type="hidden" value="{{$data['grupo']->id}}" id="id">

                                                    <div class="form-group">
                                                        <div class="tree smart-form" id="modules-tree">
                                                            <ul>
                                                                <ul>
                                                                    <?php $menus = DB::select('CALL sp_menu_sidebar(?,?, ?)', [Auth::user()->id,Session::get('sistema'), null]); ?>
                                                                    @foreach($menus as $menu)
                                                                    <li>
																<span>
																	<i class="fa fa-lg {{$menu->icono}}"></i>&nbsp;
																	{{$menu->nombre_menu}}
																	<span class="ch">
																		@if(in_array($menu->id, $data['menus']))
																		<input type="checkbox" name="menus" value="{{$menu->id}}" checked>
																		@else
																		<input type="checkbox" name="menus" value="{{$menu->id}}">
																		@endif
																	</span>
																</span>
                                                                        <ul>
                                                                            <?php $hijos = DB::select('CALL sp_menu_sidebar(?,?, ?)', [Auth::user()->id,Session::get('sistema'), $menu->id]); ?>
                                                                            @foreach($hijos as $hijo)
                                                                            <li style="display: none">
																		<span>
																			<i class="fa fa-lg fa-plus-circle"></i>&nbsp;
																			{{$hijo->nombre_menu}}
																			<span class="ch">
																				@if(in_array($hijo->id, $data['menus']))
																				<input type="checkbox" name="menus" value="{{$hijo->id}}" checked>
																				@else
																				<input type="checkbox" name="menus" value="{{$hijo->id}}">
																				@endif
																			</span>
																		</span>
                                                                                <?php $nietos = DB::select('CALL sp_menu_sidebar(?,?, ?)', [Auth::user()->id,Session::get('sistema'),$hijo->id]); ?>
                                                                                @if($nietos)
                                                                                <ul>
                                                                                    @foreach($nietos as $nieto)
                                                                                    <li style="display: none">
																					<span>
																						<i class="fa fa-lg fa-plus-circle"></i>&nbsp;
																						{{$nieto->nombre_menu}}
																						<span class="ch">
																							@if(in_array($nieto->id, $data['menus']))
																							<input type="checkbox" name="menus" value="{{$nieto->id}}" checked>
																							@else
																							<input type="checkbox" name="menus" value="{{$nieto->id}}">
																							@endif
																						</span>
																					</span>
                                                                                        <ul>
                                                                                            <?php $permisos = DB::select('SELECT id, tipo FROM adm_funciones WHERE modulo=? ', [$nieto->nombre]); ?>
                                                                                            @foreach($permisos as $permiso)
                                                                                            <li style="display:none">
																							<span>
																								<label class="checkbox inline-block">
																									@if(in_array($permiso->id, $data['asignados']))
																									<input type="checkbox" name="permisos" value="{{$permiso->id}}" checked>
																									@else
																									<input type="checkbox" name="permisos" value="{{$permiso->id}}">
																									@endif
																									<i></i> {{$permiso->tipo}}
																								</label>
																							</span>
                                                                                            </li>
                                                                                            @endforeach
                                                                                            <?php $modulos = DB::select('SELECT DISTINCT modulo FROM adm_funciones WHERE padre=? ORDER BY modulo', [$nieto->nombre]); ?>
                                                                                            @foreach($modulos as $modulo)
                                                                                            <li style="display: none"><span><i class="fa fa-lg fa-plus-circle"></i>&nbsp; {{$modulo->modulo}}</span>
                                                                                                <ul>
                                                                                                    <?php $permisos = DB::select('SELECT id, tipo FROM adm_funciones WHERE modulo=? ORDER BY tipo', [$modulo->modulo]); ?>
                                                                                                    @foreach($permisos as $permiso)
                                                                                                    <li style="display:none">
																								<span>
																									<label class="checkbox inline-block">
																										@if(in_array($permiso->id, $data['asignados']))
																										<input type="checkbox" name="permisos" value="{{$permiso->id}}" checked>
																										@else
																										<input type="checkbox" name="permisos" value="{{$permiso->id}}">
																										@endif
																										<i></i> {{$permiso->tipo}}
																									</label>
																								</span>
                                                                                                    </li>
                                                                                                    @endforeach
                                                                                                </ul></li>
                                                                                            @endforeach
                                                                                        </ul>
                                                                                    </li>
                                                                                    @endforeach
                                                                                </ul>
                                                                                @else
                                                                                <ul>
                                                                                    <?php $permisos = DB::select('SELECT id, tipo FROM adm_funciones WHERE modulo=? ORDER BY tipo', [$hijo->nombre_menu]); ?>
                                                                                    @foreach($permisos as $permiso)
                                                                                    <li style="display:none">
																					<span>
																						<label class="checkbox inline-block">
																							@if(in_array($permiso->id, $data['asignados']))
																							<input type="checkbox" name="permisos" value="{{$permiso->id}}" checked>
																							@else
																							<input type="checkbox" name="permisos" value="{{$permiso->id}}">
																							@endif
																							<i></i>&nbsp; {{$permiso->tipo}}
																						</label>
																					</span>
                                                                                    </li>
                                                                                    @endforeach
                                                                                </ul>
                                                                                @endif
                                                                            </li>
                                                                            @endforeach
                                                                        </ul>
                                                                        <ul>
                                                                            <?php $permisos = DB::select('SELECT id, tipo FROM adm_funciones WHERE modulo=? ORDER BY tipo', [$menu->nombre_menu]); ?>
                                                                            @foreach($permisos as $permiso)
                                                                            <li style="display:none">
																	<span>
																		<label class="checkbox inline-block">
																			@if(in_array($permiso->id, $data['asignados']))
																			<input type="checkbox" name="permisos" value="{{$permiso->id}}" checked>
																			@else
																			<input type="checkbox" name="permisos" value="{{$permiso->id}}">
																			@endif
																			<i></i>&nbsp; {{$permiso->tipo}}
																		</label>
																	</span>
                                                                            </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </li>
                                                                    @endforeach
                                                                </ul>

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
	$('.tree > ul').attr('role', 'tree').find('ul').attr('role', 'group');
	$('.tree').find('li:has(ul)').addClass('parent_li').attr('role', 'treeitem').find(' > span').attr('title', 'Collapse this branch').on('click', function(e) {
		var children = $(this).parent('li.parent_li').find(' > ul > li');
		if (children.is(':visible')) {
			children.hide('fast');
			$(this).attr('title', 'Expand this branch').find(' > i').removeClass().addClass('fa fa-lg fa-plus-circle');
		} else {
			children.show('fast');
			$(this).attr('title', 'Collapse this branch').find(' > i').removeClass().addClass('fa fa-lg fa-minus-circle');
		}
		e.stopPropagation();
	});	
</script>
<script>
    var url = "/adm-permisos";
	$(".breadcrumb").html("<li>Inicio</li><li>Administración</li><li>Permisos</li>");
    $(document).ready(function() {
        //Input Permisos Función Change
        $("input[name='permisos']").change(function() {
            _id_mod = $(this).val();
            _id_role = $("#id").val();
            if($(this).prop("checked") == true) {
               /* $.ajax({
                    type : "POST",
                    url : url + "/asigna",
                    data : {
                        "_token": "{{ csrf_token() }}",
                        modulo : _id_mod,
                        role : _id_role}
                }).done(function(_response) {
                });*/

                $.ajax({
                    url:url + "/asigna",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        modulo : _id_mod,
                        role : _id_role
                    },
                    success:function(response){
                        if (response == "done") {
                            window.location.href = '/adm-grupos';
                        }
                    },
                    error: function(response) {
                        console.log('Error: ' + response);
                    }
                });
            } else {
                $.ajax({
                    type : "POST",
                    url : url + "/remueve",
                    data : {"_token": "{{ csrf_token() }}",modulo : _id_mod, role : _id_role}
                }).done(function(_response) {
                });
            }
        });

        //Input Menus Función Change
        $("input[name='menus']").change(function() {
            _id_menu = $(this).val();
            _id_role = $("#id").val();
            if($(this).prop("checked") == true) {
                $.ajax({
                    type : "POST",
                    url : url + "/masigna",
                    data : {"_token": "{{ csrf_token() }}",menu : _id_menu, role : _id_role}
                }).done(function(_response) {
                });
            } else {
                $.ajax({
                    type : "POST",
                    url : url + "/mremueve",
                    data : {"_token": "{{ csrf_token() }}",menu : _id_menu, role : _id_role}
                }).done(function(_response) {
                });
            }
        });
        //Input Todos los Permisos Función Change
        $("input[name='all-permission']").change(function() {
            _id_role = $("#id").val();
            _type = "";
            $("#widget-grid").html('<h1 class="error-text-2 bounceInDown animated"><i class="fa fa-gear fa-spin fa-lg"></i> Cargando <span class="particle particle--a"></span><span class="particle particle--b"></span></h1>');
            if($(this).prop("checked") == true) { _type = "add"; } else { _type = "remove"; }
            $.ajax({
                type : "POST",
                url : url + "/all-permission",
                data : {"_token": "{{ csrf_token() }}",role : _id_role, type : _type }
            }).done(function(_response) {
                location.reload();
            });
        });
    });
</script>
@endsection