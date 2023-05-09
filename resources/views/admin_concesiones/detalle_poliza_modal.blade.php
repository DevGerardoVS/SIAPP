<div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" id="modal_detalle_poliza" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" id="form_modal_detalle_poliza">
                @csrf
                <input type="hidden" id="id_poliza" name="id_poliza" class="form-control">
                <input type="hidden" id="action" name="action" class="form-control">
                <div class="modal-header colorMorado">
                    <h5 class="modal-title" id="title_modal">{{ __('messages.detalle_poliza') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-2 offset-md-10">
                            <button id="btn_print_pdf" type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('messages.print') }} <i class="fas fa-file-pdf-o"></i></button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="modal-title" id="title_modal"><b>{{ __('messages.datos_propietario') }}</b></h5>
                            <div class="row">
                                <div class="col-md-5">
                                    <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.propietario') }}:</label>
                                </div>
                                <div class="col-md-7">
                                    <label id="lbl_propietario" class="col-form-label text-md-start"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.rfc') }}:</label>
                                </div>
                                <div class="col-md-7">
                                    <label id="lbl_rfc" class="col-form-label text-md-start"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.no_placas') }}:</label>
                                </div>
                                <div class="col-md-7">
                                    <label id="lbl_no_placas" class="col-form-label text-md-start"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.no_serie_vehiculo') }}:</label>
                                </div>
                                <div class="col-md-7">
                                    <label id="lbl_no_serie_vehiculo" class="col-form-label text-md-start"></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="modal-title" id="title_modal"><b>{{ __('messages.datos_concesion') }}</b></h5>
                            <div class="row">
                                <div class="col-md-5">
                                    <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.no_concesion') }}:</label>
                                </div>
                                <div class="col-md-7">
                                    <label id="lbl_no_concesion" class="col-form-label text-md-start"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.objeto_contrato') }}:</label>
                                </div>
                                <div class="col-md-7">
                                    <label id="lbl_objeto_contrato" class="col-form-label text-md-start"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.cuenta_contrato') }}:</label>
                                </div>
                                <div class="col-md-7">
                                    <label id="lbl_cuenta_contrato" class="col-form-label text-md-start"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.interlocutor') }}:</label>
                                </div>
                                <div class="col-md-7">
                                    <label id="lbl_interlocutor" class="col-form-label text-md-start"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.tipo_servicio') }}:</label>
                                </div>
                                <div class="col-md-7">
                                    <label id="lbl_tipo_servicio" class="col-form-label text-md-start"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.grupo') }}:</label>
                                </div>
                                <div class="col-md-7">
                                    <label id="lbl_grupo" class="col-form-label text-md-start"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.modalidad') }}:</label>
                                </div>
                                <div class="col-md-7">
                                    <label id="lbl_modalidad" class="col-form-label text-md-start"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="solid">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="modal-title" id="title_modal"><b>{{ __('messages.datos_poliza') }}</b></h5>
                            <div class="row">
                                <div class="col-md-5">
                                    <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.no_poliza') }}:</label>
                                </div>
                                <div class="col-md-7">
                                    <label id="lbl_no_poliza" class="col-form-label text-md-start"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.aseguradora') }}:</label>
                                </div>
                                <div class="col-md-7">
                                    <label id="lbl_aseguradora" class="col-form-label text-md-start"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.fecha_vencimiento_poliza') }}:</label>
                                </div>
                                <div class="col-md-7">
                                    <label id="lbl_fecha_vencimiento_poliza" class="col-form-label text-md-start"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.fecha_creacion_poliza') }}:</label>
                                </div>
                                <div class="col-md-7">
                                    <label id="lbl_fecha_creacion_poliza" class="col-form-label text-md-start"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.estatus') }}:</label>
                                </div>
                                <div class="col-md-7">
                                    <label id="lbl_estatus" class="col-form-label text-md-start"></label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-5">
                                    <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.observaciones') }}:</label>
                                </div>
                                <div class="col-md-7">
                                    <label id="lbl_observaciones" class="col-form-label text-md-start"></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6" id="section_detalle_pago">
                            <h5 class="modal-title" id="title_modal"><b>{{ __('messages.datos_pago') }}</b></h5> 
                            <div id="div_detalle_pago">
                                <div class="row">
                                    <div class="col-md-5">
                                        <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.linea_captura') }}:</label>
                                    </div>
                                    <div class="col-md-7">
                                        <label id="lbl_linea_captura" class="col-form-label text-md-start"></label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5">
                                        <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.orden_pago') }}:</label>
                                    </div>
                                    <div class="col-md-7">
                                        <label id="lbl_orden_pago" class="col-form-label text-md-start"></label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5">
                                        <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.importe_concesion') }}:</label>
                                    </div>
                                    <div class="col-md-7">
                                        <label id="lbl_importe_concesion" class="col-form-label text-md-start"></label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5">
                                        <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.estatus_pago') }}:</label>
                                    </div>
                                    <div class="col-md-7">
                                        <label id="lbl_estatus_pago" class="col-form-label text-md-start"></label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5">
                                        <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.periodo_pago') }}:</label>
                                    </div>
                                    <div class="col-md-7">
                                        <label id="lbl_ejercicio" class="col-form-label text-md-start"></label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5">
                                        <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.importe_refrendo') }}:</label>
                                    </div>
                                    <div class="col-md-7">
                                        <label id="lbl_importe_refrendo" class="col-form-label text-md-start"></label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-5">
                                        <label style="font-weight: bold;" class="col-form-label text-md-start">{{ __('messages.importe_total') }}:</label>
                                    </div>
                                    <div class="col-md-7">
                                        <label id="lbl_importe_total" class="col-form-label text-md-start"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="solid">
                    <div class="row" id="div_tabla_conceptos">
                        <h5 class="modal-title" id="title_modal"><b>{{ __('messages.conceptos_pago') }}</b></h5> 
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12" id="div_conceptos">
                                    <br>
                                    <table id="table_conceptos" class="table table-striped table-bordered text-center " style="width:100%">
                                        <thead>
                                            <tr class="colorMorado">
                                                <th>{{ __('messages.periodo') }}</th>
                                                <th>{{ __('messages.cantidad') }}</th>
                                                <th>{{ __('messages.clave') }}</th>
                                                <th>{{ __('messages.concepto') }}</th>
                                                <th>{{ __('messages.valor_unitario') }}</th>
                                                <th>{{ __('messages.importe') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="conceptos"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button tabindex="22" type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('messages.cancelar')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>