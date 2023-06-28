<?php
    $titleDesc = "Administración de Captura";
    
?>

@extends('layouts.app')
@section('content')

<div class="container w-100 p-4" >
    <header>
        <h1 class="fw-bold text-center">{{ $titleDesc }}</h1>
        <div class="rounded-pill" style="height: .5em; background-color: rgb(37, 150, 190)"></div>
        <form action="{{route('claves_presupuestarias')}}" id="buscarFormA" name="buscarFormA" method="post"></form>
        <form action="{{route('metas_actividades')}}" id="buscarFormB" name="buscarFormB" method="post"></form> 
    </header>

     {{-- Form para cambiar los select --}}
     <form id="form" method="POST">
        @csrf
        <div class="col-md-10 col-sm-12 d-md-flex mt-5">
            <div class="col-sm-3 col-md-3 col-lg-2 text-md-end">
                <label for="anio_filter" class="form-label fw-bold mt-md-1">Ejercicio: </label>
            </div>
            <div class="col-sm-12 col-md-3 col-lg-2">
                <select class="form-control filters filters_anio" id="anio_filter" name="anio_filter" autocomplete="anio_filter">
                    @foreach ($anios as $anio)
                        <option value={{$anio->ejercicio}}>{{$anio->ejercicio}}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-sm-3 col-md-3 col-lg-2 text-md-end">
                <label for="estatus_filter" class="form-label fw-bold mt-md-1">Fecha de corte:</label>
            </div>
            <div class="col-sm-12 col-md-3 col-lg-2">
                <select class="form-control filters filters_estatus" id="estatus_filter" name="estatus_filter" autocomplete="estatus_filter">
                    <option value="">Todos</option>
                    @foreach ($estatus as $est)
                    <option value={{$est->estatus}}>{{$est->estatus}}</option>
                @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-10 col-sm-12 d-md-flex mt-2 ">
            <div class="col-sm-3 col-md-3 col-lg-2 text-md-end d-none div_upp">
                <label for="upp_filter" class="form-label fw-bold mt-md-1">UPP:</label>
            </div>
            <div class="col-md-6 col-sm-12 d-none div_upp">
                <select class="form-control filters filters_upp" id="upp_filter" name="upp_filter" autocomplete="upp_filter">
                    <option value="">Todos</option>
                    @foreach ($upps as $upp)
                        <option value={{$upp->clave}} {{$upp->descripcion}}>{{$upp->clave}} {{$upp->descripcion}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

</div>
@endsection