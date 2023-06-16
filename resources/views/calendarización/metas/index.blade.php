@extends('layouts.app')
@include('panels.datatable')
@section('content')
    <div class="container">
        <form action="{{ route('getMetasP') }}" id="buscarForm" method="POST">
            @csrf
            <textarea id="ur" style="display: none"></textarea>
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
                            <h2>Calendarización de metas</h2>
                        </header>
                        <br>
                        <div>
                            <div class="widget-body-toolbar">
                                <div class="row">
                                            <div class="col-md-5">
                                                <select class="form-control filters" id="ur_filter" name="ur_filter"
                                                    autocomplete="ur_filter" placeholder="Seleccione una UR" data-live-search="true">
                                                    <option value="" disabled selected>Seleccione una UR</option>
                                                </select>
                                            </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="widget-body no-padding ">
                            <div class="table-responsive ">
                                <table id="catalogo" class="table table-hover table-striped ">
                                    <thead>
                                        <tr class="colorMorado">
                                            <th>Programas</th>
                                            <th>SubProgramas</th>
                                            <th>Proyecto</th>
                                            <th>Seleccion</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="modal-body">
                <form id="actividad">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="control-label">Nombre de la actividad</label>
                            <select class="form-control" aria-placeholder="Selecciona una actividad" id="sel_actividad" data-live-search="true"
                            name="sel_actividad">
                        </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="control-label">Fondo</label>
                            <select class="form-control" placeholder="Selecciona una actividad" id="sel_fondo" data-live-search="true"
                                name="sel_fondo" autocomplete="anio_filter" placeholder="Seleccione un año">
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label class="control-label">Tipo de calendario</label>
                            <select class="form-control" aria-placeholder="Selecciona una actividad" id="tipo_Ac" data-live-search="true"
                                name="tipo_Ac" >
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="control-label">No. Beneficiarios</label>
                            <input type="text" class="form-control" id="beneficiario" name="beneficiario" onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" >
                        </div>
                        <div class="form-group col-md-3">
                            <label class="control-label">Beneficiarios</label>
                            <select class="form-control" aria-placeholder="Selecciona una Beneficiarios" id="tipo_Be" data-live-search="true"
                                name="tipo_Be">
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="control-label">Unidad de medida</label>
                            <select class="form-control" aria-placeholder="Selecciona una Medida" data-live-search="true" id="medida"
                                name="medida">
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="meses1" class="table table-hover table-striped ">
                            <thead>
                                <tr class="colorMorado" style="text-align:center;">
                                    <th>Enero</th>
                                    <th>Febrero</th>
                                    <th>Marzo</th>
                                    <th>Abril</th>
                                    <th>Mayo</th>
                                    <th>Junio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <td><input onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" id="1" name="1" type="text" class="form-control meses" onkeyup="dao.sumar();" disabled></td>
                                <td><input onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" id="2" name="2" type="text" class="form-control meses" onkeyup="dao.sumar();" disabled></td>
                                <td><input onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" id="3" name="3" type="text" class="form-control meses" onkeyup="dao.sumar();" disabled></td>
                                <td><input onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" id="4" name="4" type="text" class="form-control meses" onkeyup="dao.sumar();" disabled></td>
                                <td><input onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" id="5" name="5" type="text" class="form-control meses" onkeyup="dao.sumar();" disabled></td>
                                <td><input onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" id="6" name="6" type="text" class="form-control meses" onkeyup="dao.sumar();" disabled></td>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive ">
                        <table id="meses2" class="table table-hover table-striped"
                            style="border-bottom-style: none;">
                            <thead>
                                <tr class="colorMorado" style="text-align:center;">
                                    <th>Julio </th>
                                    <th>Agosto </th>
                                    <th>Septiembre</th>
                                    <th>Octubre </th>
                                    <th>Noviembre </th>
                                    <th>Diciembre </th>
                                </tr>
                            </thead>
                            <tbody>
                                <td><input onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" onkeyup="dao.sumar();" id="7" name="7" type="text"   class="form-control  meses" disabled></td>
                                <td><input onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" onkeyup="dao.sumar();" id="8" name="8" type="text"   class="form-control  meses" disabled></td>
                                <td><input onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" onkeyup="dao.sumar();" id="9" name="9" type="text"   class="form-control  meses" disabled></td>
                                <td><input onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" onkeyup="dao.sumar();" id="10" name="10" type="text" class="form-control  meses" disabled></td>
                                <td><input onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" onkeyup="dao.sumar();" id="11" name="11" type="text" class="form-control  meses" disabled></td>
                                <td><input onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" onkeyup="dao.sumar();" id="12" name="12" type="text" class="form-control  meses" disabled></td>
                                <tr style="border-style: none;">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <h6><b>Metas Calendarizadas</b></h6>
                                    </td>
                                    <td><input onkeypress="return (event.charCode >= 48 && event.charCode <= 57 && event.charCode >= 99 && event.charCode <= 122 )" id="sumMetas" name="sumMetas" type="text" class="form-control" >
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
    </div>
    </article>
    </div>
    </section>
    </div>
    <script src="/js/calendarización/metas/init.js"></script>
    <script src="/js/utilerias.js"></script>
    <script>
        //En las vistas solo se llaman las funciones del archivo init
        init.validateCreate($('#frm_create'));
    </script>
@endsection
