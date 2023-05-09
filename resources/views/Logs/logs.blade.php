<?php
    $titleDesc = 'Descarga de Logs';
    $titleDesc = ucfirst(mb_strtolower($titleDesc,'UTF-8'));
?>

@extends('layouts.app')

@section('content')
    <div class="container customContainerPosition">
        <div>
            <h3 style="margin-left: 2%;">{{$titleDesc}}</h3>
            <hr style="width: 98%; border: 1px solid gray;">

            <br>
            
            <div class="contenedorBordeGuinda contenedorBColor" style="width: 95%; margin-left: 2%;">
                <form action="{{ route('downloadLogs') }}" method="POST" style="text-align: center">
                    @csrf
                    <div class="wrap1">
                        <div style="width:50%; margin-left: 25%;">
                            <label>Archivo</label>
                            <select class="form-control form-select" name="selected" id="selected">
                                @foreach ($logs as $log)
                                    <option value="{{$log['filename']}}">{{$log['filename']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <br>
                        <div>
                            <button class="btn btn-primary hoverButtonStyle " id="addPM" style="width:15%;">
                                <i class="fas  fa-download" style="color: #6a0e4a"></i> 
                                Descargar 
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <br>

        </div>
    @endsection
