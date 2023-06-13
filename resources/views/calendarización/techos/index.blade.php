@extends('layouts.app')
@include('panels.datatable')
@include('calendarización.techos.modalCreate')
@section('content')
<div class="container">
    <form action="{{ route('getTechos') }}" id="buscarForm" method="GET">
        @csrf

    </form>
    <section id="widget-grid" class="conteiner">
        <div class="row">
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div color="darken" class="jarviswidget" id="wid-id-1" data-widget-editbutton="false"
                     data-widget-colorbutton="false" data-widget-deletebutton="false">
                    <header class="d-flex justify-content-center" style=" border-bottom: 5px solid #17a2b8;">
                        <h2>Techos Financieros</h2>
                    </header>
                    <br>
                    <div>
                        <div class="widget-body-toolbar">
                            <div class="row">
                                <div class="col-md-2">
                                    <select class="form-control filters" id="anio_filter" name="anio_filter"
                                            autocomplete="anio_filter" placeholder="Seleccione un año">
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control filters" id="ur_filter" name="ur_filter"
                                            autocomplete="ur_filter" placeholder="Seleccione una UPP">
                                        <option value="" disabled selected>Buscar por UPP</option>
                                        <option value="2022">002</option>
                                        <option value="2023">003</option>
                                        <option value="2024">004</option>
                                        <option value="2025">005</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control filters" id="ur_filter" name="ur_filter"
                                            autocomplete="ur_filter" placeholder="Seleccione un fondo">
                                        <option value="" disabled selected>Buscar por fondo</option>
                                    </select>
                                </div>
                                <div class="col-md-4"></div>
                                <div class="col-md-2">
                                    <!--<button class="btn btn-primary">Nuevo registro</button>-->
                                    <button type="button" class="btn btn-success" data-toggle="modal" id="btnNew"
                                            data-target=".bd-example-modal-lg" data-backdrop="static"
                                            data-keyboard="false">Agregar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="widget-body no-padding ">
                        <div class="table-responsive ">
                            <table id="catalogo" class="table table-hover table-striped ">
                                <thead>
                                <tr class="colorMorado">
                                    <th>ID UPP</th>
                                    <th>Unidad Programatica Presupuestaria</th>
                                    <th>Tipo</th>
                                    <th>ID Fondo</th>
                                    <th>Fondo</th>
                                    <th>Presupuesto</th>
                                    <th>Ejercicio</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
        </div>
</div>
</article>
</div>
</section>
</div>
<script src="/js/calendarización/techos/init.js"></script>
<script src="/js/utilerias.js"></script>
<script>
    //En las vistas solo se llaman las funciones del archivo init
    init.validateCreate($('#frm_create'));
</script>
@endsection

