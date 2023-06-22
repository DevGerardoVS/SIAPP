@extends('layouts.app')
@include('calendarización.metas.addActividad')
@include('panels.datatable')
@section('content')
    <div class="container">
        <section id="widget-grid" class="conteiner">
            <div class="row">
                <div>
                    <header class="d-flex justify-content-center" style=" border-bottom: 5px solid #17a2b8; margin-bottom: 5px;">
                        <h2>Proyectos con actividades</h2>
                    </header>
                </div>
                    
                    <div class="d-flex " style="justify-content: flex-end">
                        <div>
                            <a type="button" class="btn btn-primary" href="/calendarizacion/metas">Agregar Actividad</a>
                            <div class="d-flex justify-content-center" style=" margin: 2px auto;">
                                <a type="button" style="justify-content: float-right" href="{{ route('ExportExcel') }}"
                                    class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true"></i></a>
                                &nbsp
                                <a type="button" style="justify-content: flex-end" href="/actividades/exportPdf"
                                    class="btn btn-danger"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="widget-body no-padding ">
                        <div class="table-responsive ">
                            <table id="catalogo" class="table table-hover table-striped"
                                style="border-collapse: collapse;">
                                <thead>
                                    <tr class="colorMorado">
                                        <th>UR</th>
                                        <th>Programa</th>
                                        <th>Subprograma</th>
                                        <th>Proyecto</th>
                                        <th>Fondo</th>
                                        <th>Actividad</th>
                                        <th>Tipo Actividad</th>
                                        <th>Meta anual</th>
                                        <th># Beneficiarios</th>
                                        <th>Beneficiarios</th>
                                        <th>U de medida</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        </section>
    </div>
    <script src="/js/calendarización/metas/initActiv.js"></script>
    <script src="/js/utilerias.js"></script>
    <script>
        //En las vistas solo se llaman las funciones del archivo init

        init.validateCreate($('#actividad'));
    </script>
@endsection
