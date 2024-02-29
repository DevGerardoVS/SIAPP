@extends('layouts.app')

@section('content')

    <div class="">
        <form action="" id="editData" method="Post">
            @csrf
            <input id="archivo_id" name="filter" value="" style="display: none">
            
        </form>

        
        <section id="widget-grid">

            <div class="row">

                <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                    <div class="container w-100 mt-4 p-4">
                        <h5 style="text-align: left; font-weight: bold;">{{ __('messages.manuales') }}</h5>
                        <form action="{{route('get_manuales')}}" id="buscarForm" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-sm-3"></div>
                                <div class="col-sm-4"></div>
                                <div class="col-sm-3"></div>
                            </div>
                        </form>
                        <br>
                        <button type="button" id="btn_new_registro" data-toggle="modal" data-target="#modalNuevoM" class="btn" style="color:#0d6efd"><i class="fas fa-plus">
                                {{ __('messages.nuevo_registro') }}</i></button>
                        <br>
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="catalogo" class="table table-striped table-bordered text-center " style="width:100%">
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
            <br>
       
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
                        <input id="id_act" name="filter" value="" style="display: none">
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


@include('panels.datatable')

<script src="/js/manuales/init.js"></script>

@endsection