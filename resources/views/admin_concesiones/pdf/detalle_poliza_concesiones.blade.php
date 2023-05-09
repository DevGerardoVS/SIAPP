<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>{{$title}}</title>
        <style>
            h5{
                text-align: center;
                text-transform: uppercase;
            }
            .center-img,
            .center-txt {
              display: inline-block;
              vertical-align: middle;
            }
            .contenido{
                font-size: 12px;
            }
            #tb_encabezado, #tb_contenido, #tb_pie{
                width: 100%;
            }
            .col_encabezado{
                width: 50%;
            }
            .col_pie{
                width: 40%;
                text-align: center;
            }
            .col_inter{
                width: 20%;
            }
            .col_firma{
                border-top: 1px solid #000000;
            }
            .tr_space{
                height: 300px;
            }
            .tr_head_content {
                background-color: #6A0F49 !important;
                color: white !important;
            }
            #tb_contenido tbody tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            .column_data{
                text-align: center;
            }
            .column_numbers{
                text-align: right;
            }
            #wrapper {
                text-align: left;
            }
            .tr_sin_adeudo {
                background-color: #FFF3CD !important;
                /*color: white !important;*/
            }
        </style>
    </head>
    <body>
        <div id="wrapper">
            <img class="center-img" src="{{ public_path('img/SFA LogoHorizontalN.png') }}" style="width: 175px; height: 88px; margin-right: 50px;" alt="logo">
            <div class="center-txt">
               <h5 >{{$title}}</h5>
            </div>
        </div>
        <div class="contenido">
            <table id="tb_encabezado">
                <tbody>
                    <tr>
                        <td class="col_encabezado">
                            <h5><b>{{ __('messages.datos_propietario') }}</b></h5>
                            <table id="tb_encabezado">
                                <tbody>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.propietario')}}: </b></p></td>
                                        <td class="col_encabezado"><p>{{$objConcesion->propietario}}</p></td>
                                    </tr>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.rfc')}}: </b></p></td>
                                        <td class="col_encabezado"><p>{{$objConcesion->rfc}}</p></td>
                                    </tr>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.no_placas')}}: </b></p></td>
                                        <td class="col_encabezado"><p>{{$objConcesion->no_placas}}</p></td>
                                    </tr>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.no_serie_vehiculo')}}: </b></p></td>
                                        <td class="col_encabezado"><p>{{$objConcesion->no_serie_vehiculo}}</p></td>
                                    </tr>
                                    <tr class="tr_space"><td colspan="2"><p><br><br></p></td></tr>
                                    <tr class="tr_space"><td colspan="2"><p><br><br></p></td></tr>
                                </tbody>
                            </table>
                        </td>
                        <td class="col_encabezado">
                            <h5><b>{{ __('messages.datos_concesion') }}</b></h5>
                            <table id="tb_encabezado">
                                <tbody>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.no_concesion')}}: </b></p></td>
                                        <td class="col_encabezado"><p>{{$objPoliza->no_concesion}}</p></td>
                                    </tr>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.objeto_contrato')}}: </b></p></td>
                                        <td class="col_encabezado"><p>{{$objConcesion->objeto_contrato}}</p></td>
                                    </tr>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.cuenta_contrato')}}: </b></p></td>
                                        <td class="col_encabezado"><p>{{$objConcesion->cuenta_contrato}}</p></td>
                                    </tr>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.interlocutor')}}: </b></p></td>
                                        <td class="col_encabezado"><p>{{$objConcesion->interlocutor}}</p></td>
                                    </tr>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.tipo_servicio')}}: </b></p></td>
                                        <td class="col_encabezado"><p>{{$objConcesion->tipo_servicio}}</p></td>
                                    </tr>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.grupo')}}: </b></p></td>
                                        <td class="col_encabezado"><p>{{$objConcesion->grupo}}</p></td>
                                    </tr>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.modalidad')}}: </b></p></td>
                                        <td class="col_encabezado"><p>{{$objConcesion->modalidad}}</p></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="col_firma"></td>
                    </tr>
                    <tr>
                        <td class="col_encabezado">
                            <h5><b>{{ __('messages.datos_poliza') }}</b></h5>
                            <table id="tb_encabezado">
                                <tbody>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.no_poliza')}}: </b></p></td>
                                        <td class="col_encabezado"><p>{{$objPoliza->no_poliza}}</p></td>
                                    </tr>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.aseguradora')}}: </b></p></td>
                                        <td class="col_encabezado"><p>{{$objPoliza->aseguradora}}</p></td>
                                    </tr>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.fecha_vencimiento_poliza')}}: </b></p></td>
                                        <td class="col_encabezado"><p>{{date('d/m/Y', strtotime($objPoliza->fecha_vencimiento_poliza))}}</p></td>
                                    </tr>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.fecha_creacion_poliza')}}: </b></p></td>
                                        <td class="col_encabezado"><p>{{date('d/m/Y', strtotime($objPoliza->fecha_creacion_poliza))}}</p></td>
                                    </tr>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.estatus')}}: </b></p></td>
                                        <td class="col_encabezado"><p>{{$objPoliza->estatus == 0 ? "Pendiente" : ($objPoliza->estatus == 1 ? "Inconsistente" : "Revisada")}}</p></td>
                                    </tr>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.observaciones')}}: </b></p></td>
                                        <td class="col_encabezado"><p>{{$objPoliza->observaciones}}</p></td>
                                    </tr>
                                    @if($objDetallePago->detalle_conceptos != 'N/A')
                                    <tr class="tr_space"><td colspan="2"><p><br></p></td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </td>
                        <td class="col_encabezado">
                            <h5><b>{{ __('messages.datos_pago') }}</b></h5>
                            @if($objDetallePago->detalle_conceptos != 'N/A')
                            <table id="tb_encabezado">
                                <tbody>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.linea_captura')}}: </b></p></td>
                                        <td class="col_encabezado"><p>{{$objDetallePago->linea_captura}}</p></td>
                                    </tr>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.orden_pago')}}: </b></p></td>
                                        <td class="col_encabezado"><p>{{$objDetallePago->orden_pago}}</p></td>
                                    </tr>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.importe_concesion')}}: </b></p></td>
                                        <td class="col_encabezado"><p>${{$objDetallePago->importe_concesion}}</p></td>
                                    </tr>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.estatus_pago')}}: </b></p></td>
                                        <td class="col_encabezado"><p>{{$objDetallePago->estatus_pago == 0 ? "Pendiente de pago" : "Pagado"}}</p></td>
                                    </tr>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.periodo_pago')}}: </b></p></td>
                                        <td class="col_encabezado"><p>{{$objDetallePago->ejercicio}}</p></td>
                                    </tr>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.importe_refrendo')}}: </b></p></td>
                                        <td class="col_encabezado"><p>${{$objDetallePago->importe_refrendo}}</p></td>
                                    </tr>
                                    <tr>
                                        <td class="col_encabezado"><p><b>{{__('messages.importe_total')}}: </b></p></td>
                                        <td class="col_encabezado"><p>${{$objDetallePago->importe_total}}</p></td>
                                    </tr>
                                </tbody>
                            </table>
                            @else
                            <table id="tb_encabezado">
                                <tbody>
                                    <tr class="tr_sin_adeudo">
                                        <td><h5><b>{{ __('messages.sin_adeudos') }}<br><br>{{$objDetallePago->ejercicio}}</b></h5></td>
                                    </tr>
                                    <tr class="tr_space"><td colspan="2"><p><br><br></p></td></tr>
                                    <tr class="tr_space"><td colspan="2"><p><br><br></p></td></tr>
                                    <tr class="tr_space"><td colspan="2"><p><br><br></p></td></tr>
                                </tbody>
                            </table>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="col_firma"></td>
                    </tr>
                </tbody>
            </table>
            @if($objDetallePago->detalle_conceptos != 'N/A')
            <h5><b>{{ __('messages.conceptos_pago') }}</b></h5> 
            <table id="tb_contenido">
                <thead>
                    <tr class="tr_head_content">
                        <th>{{ __('messages.periodo') }}</th>
                        <th>{{ __('messages.cantidad') }}</th>
                        <th>{{ __('messages.clave') }}</th>
                        <th>{{ __('messages.concepto') }}</th>
                        <th>{{ __('messages.valor_unitario') }}</th>
                        <th>{{ __('messages.importe') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (json_decode($objDetallePago->detalle_conceptos) as $d)
                        <tr class="tr_body_content">
                            <td class="column_data">{{$d->PERIODO}}</td>
                            <td class="column_data">{{$d->CANTIDAD}}</td>
                            <td class="column_data">{{$d->CLAVE}}</td>
                            <td class="column_data">{{$d->CONCEPTO ?? ''}}</td>
                            <td class="column_numbers">${{number_format(str_replace('-','',$d->VALOR_UNITARIO),2)}}</td>
                            <td class="column_numbers">${{number_format(str_replace('-','',$d->IMPORTE),2)}}</td>
                        </tr>
                    @endforeach
                    <tr class="tr_body_content">
                        <td class="column_data"></td>
                        <td class="column_data"></td>
                        <td class="column_data"></td>
                        <td class="column_data"><b>{{ __('messages.total') }}</b></td>
                        <td class="column_numbers">${{$objDetallePago->importe_total}}</td>
                        <td class="column_numbers">${{$objDetallePago->importe_total}}</td>
                    </tr>
                </tbody>
            </table>
            @endif
        </div>
    </body>
</html>