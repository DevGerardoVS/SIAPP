@extends('layouts.main')
@section('title')
Sistema de Expedientes Digitales Jurídicos
@stop

@section('logo')
<img src="/assets/img/logo_mainbar.png" alt="SEDJ" style="margin-top:-10px">
@stop

@section('main-content')
<aside id="left-panel">
    <div class="login-info">
        <span>
            <a href="javascript:void(0);" id="show-shortcut" data-action="toggleShortcut">
            	<span class="font-lg"><i class="fa fa-user"> </i></span>&nbsp;
                <span> {{ Auth::user()->username }} </span>
            </a>
        </span>
    </div>
    <nav>
        <ul>
            <li class="active">
                <a href="/inicio">
                    <i class="fa fa-lg fa-fw fa-home"></i>
                    <span class="menu-item-parent">Inicio</span>
                </a>
            </li>
            <?php $menus = DB::select('CALL sp_menu_sidebar(?, ?)', [Auth::user()->id, null]); ?>
            @foreach($menus as $menu)
            <li>
                <a href="{{$menu->ruta}}">
                    <i class="fa fa-lg fa-fw {{$menu->icono}}"></i>
                    <span class="menu-item-parent">{{$menu->nombre_menu}}</span>
                </a>
                <?php $hijos = DB::select('CALL sp_menu_sidebar(?, ?)', [Auth::user()->id, $menu->id]); ?>
                @if($hijos)
                    <ul>
                    @foreach($hijos as $hijo)
                    <li>
                        <a href="{{$hijo->ruta}}">
                            <i class="fa fa-lg fa-fw {{$hijo->icono}}"></i>
                            <span class="menu-item-parent">{{$hijo->nombre_menu}}</span>
                        </a>
                        <?php $nietos = DB::select('CALL sp_menu_sidebar(?, ?)', [Auth::user()->id, $hijo->id]); ?>
                        @if($nietos)
                            <ul>
                            @foreach($nietos as $nieto)
                            <li>
                                <a href="{{$nieto->ruta}}">
                                    <span class="menu-item-parent">{{$nieto->nombre_menu}}</span>
                                </a>
                            </li>
                            @endforeach
                            </ul>
                        @endif
                    </li>
                    @endforeach
                    </ul>
                @endif
            </li>
            @endforeach
        </ul>
    </nav>
    <span class="minifyme" data-action="minifyMenu">
        <i class="fa fa-arrow-circle-left hit"></i>
    </span>

</aside>

{{-- /SIDEBAR --}}

<div id="main" role="main">
    <div id="ribbon">
        <span class="ribbon-button-alignment">
            <span id="refresh" class="btn btn-ribbon" data-action="resetWidgets" data-title="refresh"  rel="tooltip" data-placement="bottom" data-original-title="<i class='text-warning fa fa-warning'></i> Reiniciar aplicacion" data-html="true">
                <i class="fa fa-refresh"></i>
            </span>
        </span>
        <ol class="breadcrumb">
            <li>Inicio</li>
        </ol>
    </div>
    <div id="content"></div>
    <div id="BoxLoader" style="display:none;">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="text-center error-box">
                                <br>
                                <br>
                                <br>
                                <br>
                                <h1 class="error-text-2 bounceInDown animated"><i class="fa fa-gear fa-spin fa-lg"></i> Cargando <span class="particle particle--c" id="percProg">0</span>%<span class="particle particle--a"></span><span class="particle particle--b"></span></h1>
                                <br>
                                <p class="lead">
                                    El contenido solicitado se est&aacute; cargando
                                </p>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
         </div>
    </div>
</div>
@stop
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
