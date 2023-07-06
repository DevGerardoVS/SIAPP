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
                                   
                                   @if(session('success'))
                                <div class="alert alert-success" role="alert">
                                        {{ session('success') }}
                                 </div>                               
                                     @endif
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="asignadoUpp">Asignado:</label>
                                        <input type="text" id="asignadoUpp" name="asignadoUpp" class="form-control" disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="asignadoUpp">Calendarizado:</label>
                                        <input type="text" id="calendarizado" name="calendarizado" class="form-control" disabled>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="asignadoUpp">Disponible:</label>
                                        <input type="text" id="disponibleUpp" name="disponibleUpp" class="form-control" disabled>
                                    </div>
                                    <div class="col-md-2 text-right">
                                        <label for="buttonPresupuesto">&nbsp;</label>
                                        <button type="button" class="btn btn-light" data-toggle="modal"
                                        data-target="#modalPresupuesto"data-backdrop="static" data-keyboard="false" id='presupuestoFondo'>
                                            <i class="fa fa-eye">Presupuesto por Fondo</i>
                                        </button>
                                        <input type="hidden" id="filAnio" name="filAnio">
                                    </div>
                                    <div class="col-md-2 text-right">
                                        
                                    </div>
                                    <div class="col-md-2 text-right">
                                        @if (Auth::user()->clv_upp==NULL)
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
                            <div class="row">
                                <div class="col-md-4">
                                    <select class="form-control select2" name="filtro_anio" id="filtro_anio">
                                        <option value="">-- Selecciona un Ejercicio --</option>
                                        <option value=2022>2022</option>
                                        <option value=2023>2023</option>
                                        <option value=2024>2024</option>
                                    </select>
                                </div>
                                <div class="col-md-4" id="divFiltroUpp" style="display: none">
                                    <select class="form-control select2" name="filtro_upp" id="filtro_upp">
                                        <option value="">-- Selecciona una Unidad Programática --</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control select2" name="filtro_ur" id="filtro_ur">
                                    </select>
                                </div>
                            </div>
                            <br>
                            
                                <div class="table-responsive">
                                    <table id="claves" class="table table-hover table-striped" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th class="centro-gestor">Clasificacion</th>
                                                <th class="centro-gestor">Centro Gestor</th>
                                                <th class="area-funcional">Area Funcional</th>
                                                <th class="periodo-presupuestal">Periodo</th>
                                                <th class="clasificacion-economica">Posicion</th>
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
        let upp = "{{$uppUsuario}}";
        if (upp && upp != '') {
            document.getElementById('filtro_upp').value = upp;
            dao.filtroUr(upp);
        }else{
            $('#divFiltroUpp').show();
        }
        dao.filtroUpp('');
        dao.getData('','','');
        @if($errors->any())
        console.log({!! $errors !!});
        var failures= {!! $errors !!};
        const fails = [];
        $.each(failures, function (key, value) {
        var helper =  value[0].replace('There was an error on row', 'Hay un error en la fila: ');
        fails.push(helper);
        });

        Swal.fire({
                icon: 'error',
                title: 'Error al importar la carga masiva',
                text: fails,
                confirmButtonText: "Aceptar",
            });
        @endif
    </script>
@endsection

