@extends('layouts.app')
@include('calendarizacion.clavePresupuestaria.modalPresupuestoFondo')
@include('panels.datatable')
@section('content')
<link href="https://cdn.datatables.net/v/dt/dt-1.13.4/rg-1.3.1/datatables.min.css" rel="stylesheet"/>
<script src="https://cdn.datatables.net/v/dt/dt-1.13.4/rg-1.3.1/datatables.min.js"></script>
    <div class="container">
        <section id="widget-grid" class="conteiner">
            <div class="row">
                <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                    <div color="darken" class="jarviswidget" id="wid-id-1" data-widget-editbutton="false"
                        data-widget-colorbutton="false" data-widget-deletebutton="false">
                        <header>
                            <h2>Programación Presupuestal</h2>
                        </header>

                        <div>
                            <div class="jarviswidget-editbox">
                            </div>
                            <div class="widget-body-toolbar">
                                <a href="/calendarizacion/download-errors-excel/{!! $errors !!}" type="button" class="btn colorMorado" id="downloadbtn" name="downloadbtn" style="display:none"></a>
                                   
                                  
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="lbl_operativo" id="lbl_operativo">Operativo Asignado:</label>
                                        <input type="text" id="asignadoOperativo" name="asignadoOperativo" class="form-control montosR" disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="asignado_opertivo" id="asignado_opertivo">Operativo Calendarizado:</label>
                                        <input type="text" id="calendarizadoOperativo" name="calendarizadoOperativo" class="form-control montosR" disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="disponible_operativo" id="disponible_operativo">Operativo Disponible:</label>
                                        <input type="text" id="disponibleOperativo" name="disponibleOperativo" class="form-control montosR" disabled>
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <label for="buttonPresupuesto">&nbsp;</label>
                                        <button type="button" class="btn btn-light" data-toggle="modal"
                                        data-target="#modalPresupuesto"data-backdrop="static" data-keyboard="false" id='presupuestoFondo'>
                                            <i class="fa fa-eye">Presupuesto por Fondo</i>
                                        </button>
                                        <input type="hidden" id="filAnio" name="filAnio">
                                        <input type="hidden" id="filAnioAbierto" name="filAnioAbierto">
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <label for="buttonBtnNew">&nbsp;</label>
                                        @if (Auth::user()->clv_upp==NULL && Auth::user()->id_grupo==1)
                                        <div class="row">
                                            <button type="button" class="btn colorMorado"
                                            name="button_modal_carga_adm" id="button_modal_carga_adm">
                                            <i class="fas fa-plus">{{__("messages.carga_masiva")}} </i>
                                        </div>
                                        @else
                                        @if (check_assignFront(1))
                                        <div class="row">
                                            <button type="button" class="btn colorMorado"
                                            name="button_modal_carga" id="button_modal_carga">
                                            <i class="fas fa-plus">{{__("messages.carga_masiva")}} </i>
                                            </button>
                                        </div>
                                        @endif
                                        @endif
                                    </div>
                                    <div class="col-md-2 text-right">
                                       
                                        <div class="row">
                                            <label for="buttonBtnNew">&nbsp;</label>
                                            <button type="button" id='btnNuevaClave' class="btn btn-success form-control" ><i class="fa fa-plus"> &nbsp;Nueva Clave</i></button>
                                            <button style="display: none" type="button" class="btn btn-primary form-control"
                                                name="btn_confirmar" id="btn_confirmar">
                                                <i class="fa fa-check"> &nbsp;Confirmar Claves</i> 
                                            </button>
                                        </div>

                                    </div> 
                                </div>
                            </div>
                            <br>
                            {{-- seccion para desglose de presupuesto  --}}
                            <div class="row" id="presupuestoDeRh" >
                                {{-- presupuesto RH --}}
                                <div class="col-md-2">
                                    <label for="asignadoRH">RH Asignado:</label>
                                    <input type="text" id="asignadoRH" name="asignadoRH" class="form-control montosR" disabled>
                                </div>
                                <div class="col-md-2">
                                    <label for="asignadoUpp">RH Calendarizado:</label>
                                    <input type="text" id="calendarizadoRH" name="calendarizadoRH" class="form-control montosR" disabled>
                                </div>
                                <div class="col-md-2">
                                    <label for="asignadoUpp">RH Disponible:</label>
                                    <input type="text" id="disponibleRH" name="disponibleRH" class="form-control montosR" disabled>
                                </div>
                            </div>
                            <br>
                            <div class="alert alert-info alert-dismissible fade show" role="alert" id="alertaUppAutorizado" style="display: none">
                                <i class="fa fa-info-circle" aria-hidden="true"></i> La delegación carga la nómina de esta UPP.
                                <button type="button" class="close" aria-label="Close" onclick="hideAletr();">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            {{-- fin de seccion de desglose --}}
                            {{-- valores ocultos --}}
                            <div class="row" style="display: none">
                                <div class="col-md-2">
                                    <label for="asignadoUpp">Asignado:</label>
                                    <input type="text" id="asignadoUpp" name="asignadoUpp" class="form-control montosR" disabled>
                                </div>
                                <div class="col-md-2">
                                    <label for="asignadoUpp">Calendarizado:</label>
                                    <input type="text" id="calendarizado" name="calendarizado" class="form-control montosR" disabled>
                                </div>
                                <div class="col-md-2">
                                    <label for="asignadoUpp">Disponible:</label>
                                    <input type="text" id="disponibleUpp" name="disponibleUpp" class="form-control montosR" disabled>
                                </div>
                                
                            </div>
                            {{-- fin valores ocultos --}}
                            {{-- <br> --}}
                            <div class="row">
                                <form id="filtrosClaves" class="row align-items-center">
                                <div class="col-md-4">
                                    <select class="form-control select2" name="filtro_anio" id="filtro_anio">
                                    </select>
                                </div>
                                <div class="col-md-4" id="divFiltroUpp" style="display: none">
                                    <select class="form-control select2" name="filtro_upp" id="filtro_upp">
                                        <option value="">-- Selecciona una Unidad Programática --</option>
                                    </select>
                                    <input type="hidden" id="filUpp" name="filUpp">
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control select2" name="filtro_ur" id="filtro_ur">
                                    </select>
                                </div>
                                </form>
                            </div>
                            {{-- <br> --}}
                            
                                <div class="table-responsive">
                                    <table id="claves" class="table table-hover table-striped" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th class="centro-gestor">Clasificación</th>
                                                <th class="centro-gestor">Centro Gestor</th>
                                                <th class="area-funcional">Área Funcional</th>
                                                <th class="periodo-presupuestal">Periodo</th>
                                                <th class="clasificacion-economica">Posición</th>
                                                <th class="fondo">Fondo</th>
                                                <th class="inversion">Proyecto</th>
                                                <th class="colorMorado">Total</th>
                                                <th class="colorMorado">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="centro-gestor"></td>
                                                <td class="centro-gestor"></td>
                                                <td class="area-funcional"></td>
                                                <td class="periodo-presupuestal"></td>
                                                <td class="clasificacion-economica"></td>
                                                <td class="fondo"></td>
                                                <td class="inversion"></td>
                                                <td class="colorMorado"></td>
                                                <td class="colorMorado"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            
                        </div>
                    </div>
                </article>
            </div>
        </section>
    </div>
     @include('calendarizacion.clavePresupuestaria.CargamasivaModal')
     @include('calendarizacion.clavePresupuestaria.CargamasivaModaladm')

    <script src="/js/clavesP/init.js"></script>
    <script src="/js/utilerias.js"></script>
    <script src="/js/clavesP/cargamasiva.js"></script>

    <script>
        // env="{{env('APP_ENV')}}";
        // console.log('env',env);
        function hideAletr(params) {
            $('#alertaUppAutorizado').hide(true);
        }
        let upp = "{{$uppUsuario}}";
        let ejercicio = "{{$ejercicio}}";
        dao.getEjercicios(ejercicio);
        if (upp && upp != '' && ejercicio && ejercicio != '') {
            document.getElementById('filtro_upp').value = upp;
            document.getElementById('filUpp').value = upp;
            document.getElementById('filAnio').value = ejercicio;
            dao.filtroUr(upp,ejercicio);
        }else{
            $('#divFiltroUpp').show();
        }if (ejercicio && ejercicio != '') {
            document.getElementById('filUpp').value = upp;
            document.getElementById('filAnio').value = ejercicio;
            document.getElementById('filAnioAbierto').value = ejercicio;
            dao.filtroUpp(ejercicio,'');
            dao.getData(ejercicio,'','');
        }
        
        @if($errors->any())
       
        var failures= {!! $errors !!};
        const fails = [];
         $.each(failures, function (key, value) {
        fails.push(value);
        }); 
        Swal.fire({
                icon: 'error',
                text: fails,
                confirmButtonText: "Aceptar",
                timerProgressBar: false,
                didOpen: () => {
                    Swal.hideLoading()

            },
            }).then(function(){
                location.reload();
            });
        @endif

    </script>
@endsection

