@extends('layouts.app')
@include('panels.datatable')

@include('calendarizacion.techos.modalCreate')
@include('calendarizacion.techos.modalCarga')
@include('calendarizacion.techos.modalExportExcel')
@include('calendarizacion.techos.modalExportPDF')

@section('content')

<div class="container">
    <form action="{{ route('getTechos') }}" id="buscarForm" method="POST">
        @csrf
    
        <section id="widget-grid" class="container">
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
                                        <?php $upp = DB::table('v_epp')->select('clv_upp','upp')->distinct()->get();?>
                                        <select class="form-control filters" id="upp_filter" name="upp_filter" placeholder="Seleccione una UPP" data-live-search="true">
                                            <option value="0" selected>Buscar por UPP</option>
                                            @foreach($upp as $u)
                                            <option value="{{$u->clv_upp}}" >{{$u->upp}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <?php $fondo = DB::table('fondo')->select('clv_fondo_ramo','fondo_ramo')->distinct()->get();?>
                                        <select class="form-control filters" id="fondo_filter" name="fondo_filter" placeholder="Seleccione un fondo" data-live-search="true">
                                            <option value="0" selected>Buscar por fondo</option>
                                            @foreach($fondo as $f)
                                            <option value="{{$f->clv_fondo_ramo}}" >{{$f->fondo_ramo}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-success" 
                                        data-bs-toggle="modal" data-bs-target="#exportExcel">
                                            <i class="fa fa-file-excel-o"></i> Exportar Excel
                                        </button>
                                        <!-- <a type="button"  href="{{ route('exportExcel') }}"class="btn btn-success">
                                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                            Exportar Excel
                                        </a> -->
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger" 
                                        data-bs-toggle="modal" data-bs-target="#exportPDF">
                                            <i class="fa fa-file-excel-o"></i> Exportar PDF
                                        </button>
                                        <!-- <a type="button"  href="{{ route('exportExcel') }}"class="btn btn-success">
                                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                            Exportar Excel
                                        </a> -->
                                    </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-secondary" data-toggle="" id="btnCarga"
                                            data-target=".carga-masiva" data-backdrop="static"
                                            data-keyboard="false"><i class="fa fa-file-text-o" aria-hidden="true"></i> Carga masiva
                                    </button>
                                </div>
                                <div class="col-md-4"></div>
                                <div class="col-md-2 text-center">
                                    <button type="button" class="btn btn-success" data-toggle="modal" id="btnNew"
                                            data-target=".bd-example-modal-lg" data-backdrop="static"
                                            data-keyboard="false"><i class="fa fa-plus"></i> Agregar
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
                                        <th>Usuario que actualizó</th>
                                        <th>Acciones</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
            </div>
    </form>
    </div>

</article>
</div>
</section>
</div>
<script src="/js/calendarizacion/techos/init.js"></script>
<script src="/js/utilerias.js"></script>
<script>
    //En las vistas solo se llaman las funciones del archivo init
    init.validateCreate($('#frm_create_techo'));
    init.validateFile($('#importPlantilla'));
</script>
{{-- <script src="/js/calendarizacion/techos/initCM.js"></script>
<script>
    //En las vistas solo se llaman las funciones del archivo init
    init.validateFile($('#importPlantilla'));
</script> --}}
@endsection

