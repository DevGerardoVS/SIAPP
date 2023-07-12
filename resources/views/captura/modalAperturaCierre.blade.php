<div class="modal fade bd-example-modal-lg " id="aperturaCierreModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-center  w-100" id="exampleModalLabel" style="color:#6A0F49;">Apertura y cierre de captura</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <a aria-hidden="true">&times;</a>
                </button>
            </div>
            <div class="rounded-pill" style="height: .2em; background-color: #ffc9fc"></div>
            <div class="modal-body">
                <form id="aperturaCierreForm" action="{{route('admon_capturas_update')}}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="text" hidden name="estado" id="estado">
                    <div class="col-sm-12">
                        <label for="upp_filter" class="form-label fw-bold mt-md-1">UPP:</label>
                    </div>
                    <div class="col-sm-12">
                        <select class="form-control filters filters_upp" id="upp_filter" name="upp_filter"
                            autocomplete="upp_filter">
                            <option value="">Todas las UPP</option>
                            @foreach ($upps as $upp)
                                <option value={{ $upp->clave }} {{ $upp->descripcion }}>{{ $upp->clave }}
                                    {{ $upp->descripcion }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-12">
                        <label for="modulo_filter" class="form-label fw-bold mt-md-1">Modulo:</label>
                    </div>
                    <div class="col-sm-12">
                        <select class="form-control filters filters_modulo" id="modulo_filter" name="modulo_filter"
                            autocomplete="modulo_filter">
                            <option value="" disabled selected>Seleccione una opción</option>
                            <option value="cierre_ejercicio_claves cec">Claves presupuestarias</option>
                            <option value="cierre_ejercicio_metas cem">Metas de actividades</option>
                            <option value="cierre_ejercicio_claves cec, cierre_ejercicio_metas cem">Ambas</option>
                        </select>
                    </div>
                    
                    <br>

                    <div class="form-group text-center">
                        <div class="col-sm-12 col-sm-offset-3">
                            <label class="radio-inline border p-2 align-middle me-md-5 me-sm-0" style="font-size: 1.2em">
                                <i class="fa fa-unlock"></i> Habilitar captura <input type="radio" name="capturaRadio" value="Abierto" id="capturaRadioH" class="align-middle" style="width: 20px; height:20px;"/> 
                            </label>
                            
                            <label class="radio-inline border p-2 align-middle" style="font-size: 1.2em">
                                <i class="fa fa-lock"></i> Deshabilitar captura <input type="radio" name="capturaRadio" value="Cerrado" id="capturaRadioD" class="align-middle" style="width: 20px; height:20px;" />
                            </label>
                        </div>
                    </div>

                    <br>

                    <div class="d-flex justify-content-evenly">
                        <button type="submit" class="btn btn-primary" id="btnSave">
                            <span class="btn-label"><i class="fa fa-save text-light fs-5 align-middle p-1"></i></span>
                            <span class="d-lg-inline align-middle">Guardar</span></button>
                        <button id="cerrar" type="button" class="btn btn-secondary colorMorado" data-dismiss="modal"
                            aria-label="Close">
                            <span class="btn-label"><i class="fa fa-close text-light fs-5 align-middle p-1"></i></span>
                            <span class="d-lg-inline align-middle">Cancelar</span></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>