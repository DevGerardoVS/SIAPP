@extends('layouts.app')
@section('content')
@include('panels.datatable')
    <div class="container">
        <form action="{{ route('configuraciones') }}" id="buscarForm" method="Post">
            @csrf
            <input id="filter" name="filter" value="" style="display: none">
        </form>
        
        <section id="widget-grid">
            <div class="row">
                <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                    <div class="jarviswidget" id="wid-id-1" data-widget-editbutton="false" data-widget-colorbutton="false"
                        data-widget-deletebutton="false">
                        <header class="d-flex justify-content-center" style=" border-bottom: 5px solid #17a2b8;">
                            <h2>Configuraciones</h2>
                        </header>
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Tipo de actividad por UPP</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="autorizadas-tab" data-bs-toggle="tab" data-bs-target="#auto" type="button" role="tab" aria-controls="profile" onclick="adjustTableColumns()" aria-selected="false">UPP's autorizadas para RR HH</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reportesCarga-tab" data-bs-toggle="tab" data-bs-target="#carga" type="button" role="tab" aria-controls="profile" onclick="" aria-selected="false">Archivos de carga</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="manuales-tab" data-bs-toggle="tab" data-bs-target="#manual" type="button" role="tab" aria-controls="profile" onclick="" aria-selected="false">Manuales</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <br>
                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                
                                <div class="jarviswidget-editbox">
                                </div>
                                <div class="widget-body-toolbar">
                                    <div class="row">
                                        <div class="col-xs-9 col-sm-5 col-md-5 col-lg-5">
                                        </div>
                                        <div class="col-xs-3 col-sm-7 col-md-7 col-lg-7 text-right">
                                            <select id="upps" class="form-select">
                                                <option value="">Todas las UPP's</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <table id="catalogo" class="table table-striped table-bordered text-center table-a"
                                    style="width:100%">
                                    <thead>
                                        <tr class="colorMorado">
                                            <th>Clave UPP</th>
                                            <th>UPP</th>
                                            <th>Acumulativa</th>
                                            <th>Continua</th>
                                            <th>Especial</th>
                                        </tr>
                                    </thead>
                                </table>
                                
                            </div>
                            <div class="tab-pane fade" id="auto" role="tabpanel" aria-labelledby="autorizadas-tab">
                                <div class="widget-body-toolbar">
                                    <div class="row">
                                        <div class="col-xs-9 col-sm-5 col-md-5 col-lg-5">
                                        </div>
                                        <div class="col-xs-3 col-sm-7 col-md-7 col-lg-7 text-right">
                                            <input id="filter_auto" name="filter_auto" value="" style="display: none">
                                            <select id="upps_auto" class="form-select">
                                                <option value="">Todas las UPP's</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <table id="catalogo_b" class="table table-striped table-bordered text-center table-b"
                                    style="width:100%">
                                    <thead>
                                        <tr class="colorMorado">
                                            <th width="20%">Clave UPP</th>
                                            <th width="50%">UPP</th>
                                            <th width="30%">Autorizadas para delegación</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            {{-- se agrega un nuevo tab para los archivos de carga --}}
                            <div class="tab-pane fade" id="carga" role="tabpanel" aria-labelledby="reportesCarga-tab">
                                <div class="widget-body-toolbar">
                                </div>
                                <br>
                                <table id="archivosCarga-table" class="table table-striped table-bordered text-center table-b"
                                    style="width:100%">
                                    <thead>
                                        <tr class="colorMorado">
                                            <th width="20%">#</th>
                                            <th width="50%">Archivo</th>
                                            <th width="30%">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Áreas funcionales</td>
                                            <td><a data-toggle="tooltip" title="Descargar" class="btn btn-sm btn-success" href="/archivos-carga/1"><i class="fa fa-file-excel-o" style="color: aliceblue"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Fondos</td>
                                            <td><a data-toggle="tooltip" title="Descargar" class="btn btn-sm btn-success" href="/archivos-carga/2" ><i class="fa fa-file-excel-o" style="color: aliceblue"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Centro de costos  / Centro de beneficios </td>
                                            <td><a data-toggle="tooltip" title="Descargar" class="btn btn-sm btn-success" href="/archivos-carga/3" ><i class="fa fa-file-excel-o" style="color: aliceblue"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>Centro gestor</td>
                                            <td><a data-toggle="tooltip" title="Descargar" class="btn btn-sm btn-success" href="/archivos-carga/4" ><i class="fa fa-file-excel-o" style="color: aliceblue"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <td>Pospre</td>
                                            <td><a data-toggle="tooltip" title="Descargar" class="btn btn-sm btn-success" href="/archivos-carga/5" ><i class="fa fa-file-excel-o" style="color: aliceblue"></i></a></td>
                                        </tr>
                                        <tr>
                                            <td>6</td>
                                            <td>Claves presupuestales</td>
                                            <td><a data-toggle="tooltip" title="Descargar" class="btn btn-sm btn-success" href="/archivos-carga/6" ><i class="fa fa-file-excel-o" style="color: aliceblue"></i></a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--Manuales-->
                            <div class="tab-pane fade" id="manual" role="tabpanel" aria-labelledby="manuales-tab">
                                <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                                    <div class="container w-100 mt-4 p-4">
                                        <h5 style="text-align: left; font-weight: bold;">{{ __('messages.manuales') }}</h5>
                                        <form action="{{route('get_manuales')}}" id="buscarForm_c" method="post">
                                            @csrf
                                            <div class="row">
                                                <div class="col-sm-3"></div>
                                                <div class="col-sm-4"></div>
                                                <div class="col-sm-3"></div>
                                            </div>
                                        </form>
                                        <br>
                                        <button type="button" id="btn_new_registro" data-toggle="modal" data-target="#modalNuevoM" class="btn" style="color:#0d6efd"><i class="fa fa-plus">
                                                {{ __('messages.nuevo_registro') }}</i></button>
                                        <br>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table id="catalogo_c" class="table table-striped table-bordered text-center " style="width:100%">
                                                    <thead>
                                                        <tr class="colorMorado">
                                                            <th>{{ __('messages.nombre') }}</th>
                                                            <th>{{ __('messages.archivo') }}</th>
                                                            <th>{{ __('messages.acciones') }}</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>

                          
                        
                    </div>
                </article>
            </div>
        </section>
        <!--Modal-->
        <div class="modal fade bd-example-modal-xl" id="modalNuevoM" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
              <div class="modal-content">
                <div class="modal-header colorMorado">
                  <h5 class="modal-title" id="exampleModalLabel">Carga de archivo</h5>
                  <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formRegistro" class="justify-content-md-center" action="" method="POST">
                        @csrf
                        @method('POST')
                        <input id="id_act" name="file_filter" value="" style="display: none">
                        <div class="row">
                            <div class="col-md-6"> 
                                <label for="nombre" class="form-label">{{ __('messages.nombre') }}: </label>
                                <input type="text" title="El campo función es requerido" id="nombre" name="nombre"
                                    class="form-control" autocomplete="nombre">
                                <span id="nombre_error" class="invalid-feedback" role="alert"></span>

                            </div>
                            <div class="col-md-6">

                                <label for="usuarios" class="form-label">{{ __('messages.usuarios') }}: </label>
                                
                                <span id="nombre_error" class="invalid-feedback" role="alert"></span>
                                <br>
                                <div id="roles">

                                </div>
                                  
                            </div>

                        </div>
                        <br>
                        <div class="row">
                            <div class="col-sm-12"> 
                                <input type="file"  class="dropify" id="archivo"  enctype="multipart/form-data" name="archivo" enctype="multipart/form-data" data-allowed-file-extensions="pdf doc docx xls xlsx" data-max-file-size="6M"/>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                  <button type="button" id="close-modal-new" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                  <button type="button" class="btn colorMorado" onclick="sendData()">Guardar</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--Modal delete-->
        <div class="modal fade bd-example-modal-md" tabindex="-1" role="dialog" id="modal_delete" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header colorMorado">
                        <h5 class="modal-title" id="staticBackdropLabel">{{ __('messages.confirm_datos') }}</h5>
                        <button type="button" class="close_modal btn-close" data-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        
                        <div class="row" id="modal_message">
                            {{-- Aqui va el cuerpo del mensaje --}}
                            ¿Está seguro que desea eliminar el archivo?
                        </div>
                        <br>
                        <button type="button" id="close-modal" class="btn btn-secondary close_modal" data-dismiss="modal">{{ __('messages.cancelar') }}</button>
                        <button type="button" id="delete-button" class="btn colorMorado">{{ __('messages.confirm') }}</button>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/js/administracion/configuraciones/init.js"></script>
    <script src="/js/manuales/init.js"></script>
    <script src="/js/utilerias.js"></script>
    <script>
        //En las vistas solo se llaman las funciones del archivo init
        init.validateCreate($('#frmCreate'));

        
    </script>
@endsection