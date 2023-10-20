@extends('layouts.app')
@include('administracion.usuarios.permisosModal')
@include('administracion.usuarios.permisosModalEdit')

@include('panels.datatable')
@section('content')
    <div class="container">
        <form action="{{ route('getdataUserPerm') }}" id="buscarForm" method="GET">
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
                            <h2>Usuarios con permisos adicionales</h2>
                        </header>
                        <br>
                        <div>
                            <div class="jarviswidget-editbox">
                            </div>
                            <div class="widget-body-toolbar">
                                <div class="row">
                                    <div class="col-xs-9 col-sm-5 col-md-5 col-lg-5">
                                    </div>
                                    @if (Auth::user()->id_grupo!=2 && Auth::user()->id_grupo!=3)
                                    <div class="col-xs-3 col-sm-7 col-md-7 col-lg-7 text-right">
                                            <button type="button" class="btn btn-dark" data-toggle="modal" id="btnNew"
                                            data-target=".bd-permisos-modal-lg" data-backdrop="static"
                                            data-keyboard="false">Agregar permisos adicionales</button>

                                            {{-- es para añadir permisos al catalogo 
                                                <button type="button" class="btn btn-dark" data-toggle="modal" id="btnNew"
                                            data-target=".createpermiso" data-backdrop="static"
                                            data-keyboard="false">Permisos adicionales</button> --}}
                                    </div>
                                    @endif

        
                                </div>
                            </div>
                            <br><br>
                            <div class="widget-body no-padding ">
                                <div class="table-responsive ">
                                    <table id="catalogo" class="table table-hover table-striped ">
                                        <thead>
                                            <tr class="colorMorado">
                                                <th data-hide="phone">Clave UPP</th>
                                                <th data-hide="phone">Nombre Usuario</th>
                                                <th data-hide="phone">Nombre Completo</th>
                                                <th data-hide="phone">Perfil</th>
                                                <th data-hide="phone">Permisos</th>
                                                @if (Auth::user()->id_grupo!=2 && Auth::user()->id_grupo!=3)
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
    <script src="/js/administracion/permisos/initA.js"></script>
    <script src="/js/utilerias.js"></script>
@endsection


