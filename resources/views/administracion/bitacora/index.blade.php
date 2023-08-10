@extends('layouts.app')
@section('content')
    <div class="container">
        <header class="d-flex justify-content-center" style=" border-bottom: 5px solid #17a2b8; margin-bottom: 5px;">
            <h2>Bitácora del Sistema</h2>
        </header>
        <div class="row">
            <div class="col-md-2">
                <label class="control-label">año</label>
                <select class="form-control filters" id="anio_filter" name="anio_filter" autocomplete="anio_filter">
                    <option value="" disabled selected>Seleccione un año</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="control-label">mes</label>
                <select class="form-control filters" id="mes_filter" name="mes_filter" autocomplete="mes_filter">
                    <option value="" disabled selected>Seleccione un mes</option>
                </select>
            </div>
            <div class="col-md-7 text-right">
                <br>
                <button type="button" onclick="dao.exportExcel()" class="btn btn-outline-success "><i
                        class="fa fa-file-excel-o" aria-hidden="true"></i> Exportar Excel</button> &nbsp
            </div>
        </div>
        <div class="row widget-body no-padding ">
            <div class=" table-responsive">
                <table id="tbl-bitacora" class="table table-striped justify-content-center" style="width: 100%" >
                    <thead style="visibility: visible !important" >
                        <tr class="colorMorado">
                            <th>Nombre Usuario</th>
                            <th>Movimiento o Acción</th>
                            <th>Módulo</th>
                            <th>Ip Origen</th>
                            <th>Fecha Movimiento</th>
                            <th>Fecha/Hora Creación</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <script src="/js/administracion/bitacora/init.js"></script>
    <script src="/js/utilerias.js"></script>
@endsection
