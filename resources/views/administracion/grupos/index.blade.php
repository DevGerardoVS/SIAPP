@extends('layouts.app')
@section('content')
<div class="container">

<section id="widget-grid" class="">
        <div class="row">
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div class="jarviswidget" id="wid-id-1" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false">
                    <header>
                        <h2>Gestor de Grupos</h2>
                    </header>
                    <div>
                        <div class="jarviswidget-editbox">
                        </div>
                        <div class="widget-body-toolbar">
                            <div class="row">
                                <div class="col-xs-9 col-sm-5 col-md-5 col-lg-5">
                                </div>
                                <div class="col-xs-3 col-sm-7 col-md-7 col-lg-7 text-right">
                                    <a class="btn btn-success" id="btnNew" href="/adm-grupos/create">
                                        <i class="fa fa-plus"></i> <span class="hidden-mobile">Agregar Nuevo Grupo</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('getGroups') }}" id="buscarForm" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-sm-2">
                            </div>
                        </form>
                        <br>
                        <!--<div class="widget-body no-padding">
                            <div class="table-responsive">
                                <table id="tbl-grupos" class="table table-hover table-striped">
                                    <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th class="th-administration">Administración</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>-->
                        <table id="catalogo" class="table table-striped table-bordered text-center " style="width:100%">
                            <thead>
                            <tr class="colorMorado">
                                <th>Nombre</th>
                                <th>Administración</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </article>
        </div>
    </section>
</div>
@isset($dataSet)
@include('panels.datatable')
@endisset

<script src="/js/administracion/grupos/init.js"></script>
<script>
	dao.getData();
    $(document).ready(function () {
       getData();
    });

</script>
@endsection
