@extends('layouts.app')
@include('calendarizacion.metas.modalCarga')
@include('calendarizacion.metas.actividadContinua')
@section('content')
    <div class="container">
        <input id='upp' type="text" style="display: none" value="{{ Auth::user()->clv_upp }}">
        <input id='area' type="text" style="display: none">
        <input id='conmir' type="text" style="display: none">
        <input id='calendar' type="text" style="display: none">
        <input id='activiMir' type="text" style="display: none">
            <div class="row">
                        <header class="d-flex justify-content-center" style=" border-bottom: 5px solid #17a2b8;">
                            <h2>Agregar Actividad</h2>
                        </header>
                        &nbsp;
                        <label id="validMetas"  ></label>                                                            
                                <div class="row">
                                    @if (Auth::user()->id_grupo!= 4)
                                        <div class="col-md-4">
                                            <label class="control-label">UPP</label>
                                            <select class="form-control filters select2" id="upp_filter" name="upp_filter"
                                                autocomplete="upp_filter" placeholder="Seleccione una UR">
                                                <option value="" disabled selected>Seleccione una UPP</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="control-label">UR</label>
                                            <select class="form-control filters select2" id="ur_filter" name="ur_filter"
                                                autocomplete="ur_filter" placeholder="Seleccione una UR" disabled>
                                                <option value=""  selected>Seleccione una UR</option>
                                            </select>
                                        </div>
                                    @else
                                        <div class="col-md-4">
                                            <label class="control-label">UR</label>
                                            <select class="form-control filters select2" id="ur_filter" name="ur_filter"
                                                autocomplete="ur_filter" placeholder="Seleccione una UR">
                                                <option value="" disabled selected>Seleccione una UR</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4"></div>
                                    @endif
                                    @if (Auth::user()->id_grupo == 1 || Auth::user()->id_grupo == 4)
                                        @if (check_assignFront(1))
                                            <div  id="CargaMasiva" class="col-md-4 d-flex CargaMasiva " style="justify-content: flex-end" >
                                                <div >
                                                <button type="button" class="btn btn-outline-primary float-right CargaMasiva" data-toggle="modal"
                                                data-target="#carga" data-backdrop="static" style="display: none">Carga-Masiva</button>
                                                    {{--<button type="button" class="btn btn-outline-primary float-right desconfirmacion" 
                                                    onclick="dao.DesConfirmarMetas()"><i class="fa fa-check-square-o" aria-hidden="true"></i>&nbsp;Desconfirmar Metas</button>
                                                     --}}
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                        <div id="metasVista" class="row">
                            <div class="container">
                                &nbsp;
                                <div class=" table table-responsive-lg d-flex justify-content-center">
                                    <table id="entidad">
                                        <thead>
                                            <tr >
                                                <th class="vertical colorMorado sorting">Finalidad</th>
                                                <th class="vertical colorMorado sorting">Función</th>
                                                <th class="vertical colorMorado sorting">Subfunción</th>
                                                <th class="vertical colorMorado sorting">Eje</th>
                                                <th class="vertical colorMorado sorting">Linea de acción</th>
                                                <th class="vertical colorMorado sorting">Programa sectorial</th>
                                                <th class="vertical colorMorado sorting">Tipologia CONAC</th>
                                                <th class="vertical colorMorado sorting">Programas</th>
                                                <th class="vertical colorMorado sorting">SubProgramas</th>
                                                <th class="vertical colorMorado sorting">Proyecto</th>
                                                <th class="vertical" style="background-color:#afafaf;">Selección </th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodyclaves"></tbody>
                                    </table>
                                </div>
                            </div>
                            <br>
                            @include('calendarizacion.metas.tableMetas')
                            <div class="d-flex justify-content-center">
                                <a type="button" class="btn btn-secondary activC" href="/calendarizacion/proyecto"
                                onclick="dao.limpiar()">Actividades capturadas</a>
                                
                                &nbsp; &nbsp;
                                @if (Auth::user()->id_grupo == 1 || Auth::user()->id_grupo == 4)
                                    <button id="btnSave" type="button" class="btn btn-primary btnSave">Guardar</button>
                                @endif
                            </div>
                        </div>
                        <div id="incomplete" class="d-flex justify-content-center" style="display: none">
                            <div class="row">
                                    <i id="icono" aria-hidden="true" ></i>
                                    <div class="col-md-12">
                                        <h1 id="texto" class="d-flex justify-content-center"></h1>
                                    </div>
                            </div>
                        </div>
            </div> 
    </div>
    </div>
    </div>
    <script src="/js/calendarizacion/metas/init.js"></script>
    <script src="/js/utilerias.js"></script>
    
    <script>
        init.validateFile($('#formFile'));
        init.validateCont($('#formContinua'));

    </script>
@endsection
