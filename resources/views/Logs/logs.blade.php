<?php
    $titleDesc = 'Descarga de Logs';
    $titleDesc = ucfirst(mb_strtolower($titleDesc,'UTF-8'));
?>

@extends('layouts.app')

@section('content')
    <div class="container customContainerPosition">
        <div>
            <header class="d-flex justify-content-center" style=" border-bottom: 5px solid #17a2b8; margin-bottom: 5px;">
                <h2 style="margin-left: 2%;">{{$titleDesc}}</h2>
            </header>

            <br>
            
            <div class="contenedorBordeGuinda contenedorBColor" style="width: 95%; margin-left: 2%;">
                <form id='formLogs'{{-- action="{{ route('downloadLogs') }}" method="POST"  --}}style="text-align: center">
                    @csrf
                    <div class="wrap1">
                        <div style="width:50%; margin-left: 25%;">
                            <label>Archivo</label>
                            <select class="form-control form-select" name="selected" id="selected">
                            </select>
                        </div>
                        <br>
                        <div>
                            <button class="btn btn-success hoverButtonStyle " id="download" style="width:15%;" onclick="dao.exportLog()">
                                <i  class="fa fa-download" aria-hidden="true"></i> 
                                Descargar 
                            </button>
                            <button class="btn btn-warning hoverButtonStyle " id="clean" style="width:15%;" onclick="dao.cleantLog()">
                                <i class="fa fa-eraser" aria-hidden="true"></i> 
                                Limpiar 
                            </button>
                            <button type="button" class="btn btn-danger hoverButtonStyle " id="clean" style="width:15%;" onclick="dao.deleteLog()">
                                <i class="fa fa-trash-o" aria-hidden="true"></i> 
                                Eliminar</button>
                        </div>
                    </div>
                </form>
            </div>

            <br>

        </div>
        <script src="/js/administracion/logs/init.js"></script>
        <script src="/js/utilerias.js"></script>
        <script>
            //En las vistas solo se llaman las funciones del archivo init
          //  init.validateCreate($('#frmCreate'));
        </script>
    @endsection
