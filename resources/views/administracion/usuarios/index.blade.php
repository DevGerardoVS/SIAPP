@extends('layouts.app')
@include('administracion.usuarios.modalCreate')
@include('administracion.usuarios.permisosModal')
@include('administracion.usuarios.modalCreatePermisos')
@include('panels.datatable')
@section('content')
    <div class="container">
        <form action="{{ route('getdata') }}" id="buscarForm" method="GET">
            @csrf
            <div class="row">
                <div class="col-sm-2">
                </div>
        </form>
        <section id="widget-grid" class="conteiner">
            <div class="row">
                <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                    <div color="darken" class="jarviswidget" id="wid-id-1" data-widget-editbutton="false"
                        data-widget-colorbutton="false" data-widget-deletebutton="false">
                        <header class="d-flex justify-content-center" style=" border-bottom: 5px solid #17a2b8;">
                            <h2>Usuarios</h2>
                        </header>
                        <br>
                        <div>
                            <div class="jarviswidget-editbox">
                            </div>
                            <div class="widget-body-toolbar">
                                <div class="row">
                                    <div class="col-md-5">
                                    </div>
                                    @if (Auth::user()->id_grupo != 2 && Auth::user()->id_grupo != 3)
                                        <div class="col-md-7 text-right">
                                            <button type="button" class="btn btn-success" data-toggle="modal"
                                                id="btnNew" data-target=".bd-example-modal-lg" data-backdrop="static"
                                                data-keyboard="false">Agregar Usuario</button>
                                            <a type="button" class="btn btn-dark" href="{{ route('index_up') }}">
                                                Permisos adicionales</a>
                                        </div>
                                        <div class="col-md-4"></div>
                                        
                                        <div class="col-md-8 text-right">
                                            <br>
                                                <button type="button" style="justify-content:float-right; " onclick="dao.exportPdf()"class="btn btn-outline-danger col-md-2"><i class="fa fa-file-pdf-o text-center" aria-hidden="true"></i> Exportar PDF</button>&nbsp;
                                                <button type="button" style="justify-content: flex-end;" onclick="dao.exportExcel()" class="btn btn-outline-success col-md-2"><i class="fa fa-file-excel-o text-center" aria-hidden="true"></i> Exportar Excel</button> &nbsp;
                                        </div>
                                    @endif
                                </div><br>

                            </div>
                            <div class="widget-body no-padding ">
                                <div class="table-responsive ">
                                    <table id="catalogo" class="table table-hover table-striped ">
                                        <thead>
                                            <tr class="colorMorado">
                                                <th data-hide="phone">Clave Upp</th>
                                                <th data-hide="phone">Nombre Usuario</th>
                                                <th data-hide="phone">Correo</th>
                                                <th data-hide="phone">Nombre Completo</th>
                                                <th data-hide="phone">Celular</th>
                                                <th data-hide="phone">Perfil</th>
                                                <th data-hide="phone">Estatus</th>
                                                @if (Auth::user()->id_grupo != 2 && Auth::user()->id_grupo != 3)
                                                    <th class="th-administration">Acciones</th>
                                                @endif

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
    <script src="/js/administracion/usuarios/init.js"></script>
    <script src="/js/utilerias.js"></script>
@endsection
