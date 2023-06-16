@extends('layouts.app')
@include('administracion.usuarios.modalCreate')
@include('panels.datatable')
@section('content')
<div class="container">
    <section id="widget-grid" class="conteiner">
        <div class="row">
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div color="darken" class="jarviswidget" id="wid-id-1" data-widget-editbutton="false"
                    data-widget-colorbutton="false" data-widget-deletebutton="false">
                    <header>
                        <h2>Presupuesto por Fondo</h2>
                    </header>
                    <div>
                        <div class="jarviswidget-editbox">
                        </div>        
                        <div class="table-responsive">
                            <table id="newClave" class="table able-bordered" style="width: 100%">
                                <thead>
                                    <tr class="">
                                        <th class="colorMorado">ID Fondo</th>
                                        <th class="colorMorado">Fondo</th>
                                        <th class="colorMorado">Ejercicio</th>
                                        <th class="colorMorado">Monto Asignado</th>
                                        <th class="colorMorado">Calendarizado</th>
                                        <th class="colorMorado">Disponible</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>   
                    </div>
                </div>
            </article>
        </div>
    </section>
</div>
 
    <script src="/js/clavesP/init.js"></script>
    <script src="/js/utilerias.js"></script>
    <script src="/js/clavesP/cargamasiva.js"></script>

    <script>
    </script>
    
@endsection