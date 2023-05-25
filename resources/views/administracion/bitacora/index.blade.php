@extends('layouts.app')
@section('content')

<div class="container">
    <form action="{{ route('getBitacora') }}" id="buscarForm" method="POST">
        @csrf
        <input style="display: none" type="text" id="fecha" name="fecha">
    </form>
    <section id="widget-grid" class="">
        <div class="row">
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div class="jarviswidget" id="wid-id-1" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false">
                    <header>
                        <h2>Bitácora del Sistema</h2>
                    </header>
                    <div>
                        <div class="jarviswidget-editbox">
                        </div>
{{--                         <div class="widget-body-toolbar">
                            <div class="row">
                                <div class="col-xs-9 col-sm-5 col-md-5 col-lg-5">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control fecha" id="in_filtro_fecha" placeholder="1999-12-31">
                                    </div>
                                </div>
                                <div class="col-xs-3 col-sm-7 col-md-7 col-lg-7 text-right">
                                </div>
                            </div>
                        </div> --}}
                        <div class="widget-body no-padding">
                            <div class="table-responsive">
                                <table id="catalogo" class="table table-hover table-striped">
                                    <thead>
                                        <tr class="colorMorado">
                                        <th data-class="expand">Nombre Usuario</th>
                                        <th data-hide="phone">Movimiento o Acción</th>
                                        <th data-hide="phone">Módulo</th>
                                        <th data-hide="phone">Ip Origen</th>
                                        <th data-hide="phone">Fecha Movimiento</th>
                                        <th data-hide="phone">Fecha/Hora Creación</th>
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
</div>
<script src="https://momentjs.com/downloads/moment.js"></script>
@include('panels.datatable')
<script>
var date2 = new Date();
    console.log("fecha",moment(date2).format("YYYY-MM-DD"));
    $('#fecha').val(moment(date2).format("YYYY-MM-DD"));
	getData();
</script>
@endsection