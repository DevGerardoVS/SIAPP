@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <header class="d-flex justify-content-center" style=" border-bottom: 5px solid #17a2b8;">
                <h2>Carga masiva metas</h2>
            </header>
            &nbsp;

            <div class="d-inline-flex p-2">
                <div>
                    <form id="formFile">
                        @csrf
                        <br>
                        <div class="wrap1">
                            <label><b>Lea las instrucciones para asegurar el funcionamiento correcto del
                                    proceso:</b></label>
                            <ul style="width:75%; float:left;">
                                <li><b>Asegúrese de utilizar la plantilla</b> para el correcto funcionamiento de la carga
                                    masiva.</li>
                                <li>Debe llenar <b>todos</b> las columnas, para esto puede apoyarse con los catalogos que se
                                    encuentran en las otras pestañas.</li>
                                <li>El numero de beneficiarios debe ser <b>mayor a cero</b>.</li>
                                <li><b>Agregar las filas necesarias</b>.</li>
                                <li>Solo se pueden llenar los meses que estan registrados en <b>calendarización de
                                        claves</b>.</li>
                                <li>Para el subprograma <b>UUU</b> se registran automaticamnete en el sistema el total y los
                                    meses predeterminados.</li>
                            </ul>
                            <button id="CargaMasiva" type="button" class="btn btn-outline-primary text-center"
                                style="float:left;text-decoration:none; width:20%;" onclick="dao.getPlantillaCmUpp()"><i
                                    class="fa fa-check-square-o" aria-hidden="true"></i>&nbsp; Descargar plantilla</button>
                            <input name="cmFile" type="file" id="cmFile" name="cmFile" accept=".xlsx,.xlsm"
                                class="border border-secondary rounded" placeholder="Archivo" required
                                style="margin-top:5%; width : 100%;">
                                <span id="cmFile-error"></span>
                            <br>
                        </div>
                        <br>
                    </form>
                </div>
            </div>

            <div class="d-flex justify-content-center">
                <a type="button" class="btn btn-secondary" href="/calendarizacion/proyecto/metas-delegacion">Actividades
                    capturadas</a>
                &nbsp; &nbsp;
                <button type="button" onclick="dao.save()" class="btn btn-primary"><i class="fa fa-save" aria-hidden="true"></i> &nbsp;Guardar</button>
            </div>
        </div>
    </div>
    <script src="/js/calendarizacion/metas/initDel.js"></script>
    <script src="/js/utilerias.js"></script>
@endsection
