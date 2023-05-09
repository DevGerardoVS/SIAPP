@extends('layouts.app')
<?php
$stName = __('messages.nombre_sistema');
$acr = 'COCOTRA';

?>
@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.css" />

@include('layouts.preview_file_modal')
@include('admin_concesiones.change_estatus_modal')
@include('admin_concesiones.detalle_poliza_modal')
@include('admin_concesiones.polizas_seguro_modal')
@include('admin_concesiones.reemplazar_archivo_poliza_modal')

<div class="container w-100 p-4">
    <h3 style="text-align: center; font-weight: bold;">{{ __('messages.panel_admin_concesion') }}</h3>
    <br>
    <form action="{{route('preview_file_poliza')}}" id="previewFileForm" method="post">
        @csrf
        <input type="hidden" id="id_hidden" name="id_hidden" class="form-control">
    </form>
    <form action="{{route('export_admin_concesiones')}}" id="exportForm" method="post">
        @csrf
        <input type="hidden" id="tipo_servicio_export" name="tipo_servicio_filter" class="form-control">
        <input type="hidden" id="modalidad_export" name="modalidad_filter" class="form-control">
        <input type="hidden" id="aseguradora_export" name="aseguradora_filter" class="form-control">
        <input type="hidden" id="fecha_ini_poliza_export" name="fecha_ini_poliza_filter" class="form-control">
        <input type="hidden" id="fecha_fin_poliza_export" name="fecha_fin_poliza_filter" class="form-control">
        <input type="hidden" id="user_creacion_export" name="user_creacion_filter" class="form-control">
        <input type="hidden" id="estatus_pago_export" name="estatus_pago_filter" class="form-control">
    </form>
    <form action="{{ route('get_admin_concesiones') }}" id="buscarForm" method="post">
        @csrf
        <div class="row">
            <div class="col-sm-3">
                <label for="tipo_servicio_filter" class="form-label">{{__('messages.tipo_servicio')}}: </label>
                <select class="form-control filters" id="tipo_servicio_filter" name="tipo_servicio_filter" autocomplete="tipo_servicio_filter">
                    <option value="">{{__('messages.todos')}}</option>
                    @foreach ($tipo_servicio as $key=>$item)
                        <option value="{{$item->tipo_servicio}}">{{$item->tipo_servicio}}</option>
                    @endforeach
                </select>    
            </div>
            <div class="col-sm-2">
                <label for="modalidad_filter" class="form-label">{{__('messages.modalidad')}}: </label>
                <select class="form-control filters" id="modalidad_filter" name="modalidad_filter" autocomplete="modalidad_filter">
                    <option value="">{{__('messages.todas')}}</option>
                    @foreach ($modalidad as $item)
                        <option value="{{$item->modalidad}}">{{$item->modalidad}}</option>
                    @endforeach
                </select>    
            </div>
            <div class="col-sm-2">
                <label for="aseguradora_filter" class="form-label">{{__('messages.aseguradora')}}: </label>
                <select class="form-control filters" id="aseguradora_filter" name="aseguradora_filter" autocomplete="aseguradora_filter">
                    <option value="">{{__('messages.todas')}}</option>
                    @foreach ($aseguradora as $item)
                        <option value="{{$item->id}}">{{$item->nombre}}</option>
                    @endforeach
                </select>    
            </div>
            <div class="col-sm-3">
                <label for="user_creacion_filter" class="form-label">{{__('messages.user_creacion')}}: </label>
                <select class="form-control filters" id="user_creacion_filter" name="user_creacion_filter" autocomplete="estatus_poliza_filter">
                    <option value="">{{__('messages.todos')}}</option>
                    @foreach ($users_creacion as $item)
                        <option value="{{$item->created_by}}">{{$item->created_by}}</option>
                    @endforeach
                </select>    
            </div>
            <div class="col-sm-2">
                <label for="estatus_pago_filter" class="form-label">{{__('messages.estatus_pago')}}: </label>
                <select class="form-control filters" id="estatus_pago_filter" name="estatus_pago_filter" autocomplete="estatus_pago_filter">
                    <option value="">{{__('messages.todos')}}</option>
                    @foreach ($estatus_pago as $key=>$item)
                        <option value="{{$key}}">{{$item}}</option>
                    @endforeach
                </select>    
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-sm-3">
                <label for="fecha_ini_poliza_filter" class="form-label">{{__('messages.fecha_ini_registro_poliza')}}: </label>
                <input placeholder="{{__('messages.seleccionar_fecha_ini')}}" type="date" id="fecha_ini_poliza_filter" name="fecha_ini_poliza_filter" data-date-format="dd/mm/yyyy" class="form-control datepicker filters">  
            </div>
            <div class="col-sm-3">
                <label for="fecha_fin_poliza_filter" class="form-label">{{__('messages.fecha_fin_registro_poliza')}}: </label>
                <input placeholder="{{__('messages.seleccionar_fecha_fin')}}" type="date" id="fecha_fin_poliza_filter" name="fecha_fin_poliza_filter" data-date-format="dd/mm/yyyy" class="form-control datepicker filters">   
            </div>
            <div class="col-sm-2 offset-md-4">
                @if(verifyPermission('concesiones.administrador_de_concesiones.exportar'))
                <button type="button" id="btn_export" class="btn" style="color:#0d6efd"><i class="fas fa-print"> {{__("messages.export_excel")}}</i></button>
                @endif
            </div>
        </div>
    </form>
    <br>
    <div class="row">
        <div class="col-sm-12">
            <table id="catalogo" class="table table-striped table-bordered text-center " style="width:100%">
                <thead>
                    <tr class="colorMorado">
                        <th>{{ __('messages.concesion') }}</th>
                        <th>{{ __('messages.placa') }}</th>
                        <th>{{ __('messages.num_serie') }}</th>
                        <th>{{ __('messages.rfc') }}</th>
                        <th>{{ __('messages.tipo_servicio') }}</th>
                        <th>{{ __('messages.fecha_registro_poliza') }}</th>
                        <th>{{ __('messages.modalidad') }}</th>
                        <th>{{ __('messages.num_poliza') }}</th>
                        <th>{{ __('messages.aseguradora') }}</th>
                        <th>{{ __('messages.poliza') }}</th>
                        <th>{{ __('messages.fecha_vencimiento_poliza') }}</th>
                        <th>{{ __('messages.user_creacion') }}</th>
                        <th>{{ __('messages.estatus_pago') }}</th>
                        <th>{{ __('messages.acciones') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@isset($dataSet)
    @include('panels.datatablepagination')
@endisset

<script type="text/javascript">
    //inicializamos el data table
    $(document).ready(function() {
        getData();
        dropifyInit();

        $("#modalNuevaPoliza").on('click','.close_modal, .btn-close',function(e){
            if($("#modalNuevaPoliza").find("#archivo").get(0).files.length > 0){
                eventDropi = $('.dropify').dropify().data('dropify');
                eventDropi.resetPreview();
                eventDropi.clearElement();
                eventDropi.destroy();
                eventDropi.init();
                dropifyInit();
            }
            $("#modalNuevaPoliza").find("#archivo").removeClass('is-invalid');
            $('#modalNuevaPoliza').find('#archivo_error').removeClass('d-block').attr('style','display: none;');
            $("#modalNuevaPoliza").modal('toggle');
        });

        $("#modalReplaceFilePoliza").on('click','.close_modal, .btn-close',function(e){
            if($("#modalReplaceFilePoliza").find("#archivo").get(0).files.length > 0){
                eventDropi = $('.dropify').dropify().data('dropify');
                eventDropi.resetPreview();
                eventDropi.clearElement();
                eventDropi.destroy();
                eventDropi.init();
                dropifyInit();
            }
            $("#modalReplaceFilePoliza").find("#archivo").removeClass('is-invalid');
            $('#modalReplaceFilePoliza').find('#archivo_error').removeClass('d-block').attr('style','display: none;');
            $("#modalReplaceFilePoliza").modal('toggle');
        });

        $("#buscarForm").on('change',"#fecha_ini_poliza_filter",function(){
            date_ini = $(this).val();
            $("#buscarForm").find("#fecha_fin_poliza_filter").attr('min', date_ini);
        });

        $(".container").on('click','#btn_export',function(e){
            e.preventDefault();
            $("#exportForm").find("#tipo_servicio_export").val($("#buscarForm").find("#tipo_servicio_filter").val());
            $("#exportForm").find("#aseguradora_export").val($("#buscarForm").find("#aseguradora_filter").val());
            $("#exportForm").find("#modalidad_export").val($("#buscarForm").find("#modalidad_filter").val());
            $("#exportForm").find("#fecha_ini_poliza_export").val($("#buscarForm").find("#fecha_ini_poliza_filter").val());
            $("#exportForm").find("#fecha_fin_poliza_export").val($("#buscarForm").find("#fecha_fin_poliza_filter").val());
            $("#exportForm").find("#user_creacion_export").val($("#buscarForm").find("#user_creacion_filter").val());
            $("#exportForm").find("#estatus_pago_export").val($("#buscarForm").find("#estatus_pago_filter").val());
            $("#exportForm").submit();
        });

        $("#catalogo").on("click",".btn_replace_file",function(e){
            id_poliza = $(this).attr('data-id');
            $("#modalReplaceFilePoliza").find("#id_poliza").val(id_poliza);
            $("#modalReplaceFilePoliza").modal('show');
        });

        $("#catalogo").on("click",".btn_add_poliza",function(e){
            limpiarCamposAddPoliza();
            modal = $("#modalNuevaPoliza");
            modal.find("#no_consesion").val($(this).attr('data-concesion'));
            modal.find("#aseguradora").find(".opt_aseg").remove();
            $.ajax({
                url: "{{ route('get_aseguradoras')}}",
                data: {},
                type:'GET',
                dataType: 'json',
                success: function(response) {
                    $.each( response.aseguradoras, function( key, value ) {
                        modal.find("#aseguradora").append('<option class="opt_aseg" id="aseg_'+value.id+'" value="'+value.id+'">'+value.nombre+'</option>');
                    });
                    modal.modal('show');
                },
                error: function(response) {
                    console.log('Error: ' + response);
                }
            });
        });

        $("#modalNuevaPoliza").on("change","#aseguradora",function(){
            if($(this).val() == 11){
                $("#modalNuevaPoliza").find("#name_aseg_otro_div").removeAttr('hidden');
            }
            else{
                $("#modalNuevaPoliza").find("#name_aseg_otro_div").attr('hidden','hidden');
            }
        });

        $("#modalNuevaPoliza").on('click','#checkterms',function() {
            if ($(this).is(':checked')) {
                // Hacer algo si el checkbox ha sido seleccionado

                $("#modalNuevaPoliza").find('#btn_guardar').removeAttr('hidden');
                // alert("El checkbox con valor " + $(this).val() + " ha sido seleccionado");
            } else {
                $("#modalNuevaPoliza").find("#btn_guardar").attr("hidden", true);

                // Hacer algo si el checkbox ha sido deseleccionado
                // alert("El checkbox con valor " + $(this).val() + " ha sido deseleccionado");
            }
        });

        $("#modalNuevaPoliza").on("click","#btn_guardar",function(e){
            e.preventDefault();
            form = $(this).closest("#formRegistro");
            modal = $("#modalNuevaPoliza");
            var formData = new FormData();
            var archivo = modal.find("#archivo")[0].files[0];
            var csrf_tpken = modal.find("input[name='_token']").val();
            var no_consesion = modal.find("#no_consesion").val();
            var aseguradora = modal.find("#aseguradora").val();
            var name_aseg_otro = modal.find("#name_aseg_otro").val();
            var no_poliza = modal.find("#no_poliza").val();
            var fecha_vencimiento = modal.find("#fecha_vencimiento").val();
            var email = modal.find("#email").val();
            var telefono = modal.find("#telefono").val();
            var observaciones = modal.find("#observaciones").val();

            if (archivo != "" && fecha_vencimiento != "" && no_poliza != "" && aseguradora != "" && email != "") {
                formData.append("_token", csrf_tpken);
                formData.append("archivo", archivo);
                formData.append("aseg", aseguradora);
                formData.append("aseg_vencim", fecha_vencimiento);
                formData.append("num_poliz", no_poliza);
                formData.append("No_Consesion", no_consesion);
                formData.append("asegotro", name_aseg_otro);
                formData.append("email", email);
                formData.append("telefono", telefono);
                formData.append("observaciones", observaciones);
                
                $.ajax({
                    url: "{{ route('agregar_poliza') }}",
                    data: formData,
                    type:'POST',
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        modal.find("#btn_guardar").attr('disabled','disabled');
                        let timerInterval
                        Swal.fire({
                            title: '{{__("messages.msg_guardando_datos")}}',
                            html: ' <b></b>',
                            allowOutsideClick: false,
                            timer: 2000000,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });
                    },
                    success: function(response) {
                        modal.find("#btn_guardar").removeAttr('disabled');
                        Swal.close();
                        Swal.fire({
                            icon: response.status,
                            title: response.title,
                            text: response.message,
                            confirmButtonText: "{{__('messages.aceptar')}}",
                        });
                        if(response.status == "success"){
                            getData();
                            limpiarCamposAddPoliza();
                            modal.modal('toggle');
                        }
                    },
                    error: function(response) {
                        console.log('Error: ' + response);
                        modal.find("#btn_guardar").removeAttr('disabled');
                        Swal.fire({
                            icon: response.status,
                            title: response.title,
                            text: response.message,
                            confirmButtonText: "{{__('messages.aceptar')}}",
                        });
                    }
                });
            }
            else{
                validarCampos(archivo,fecha_vencimiento,no_poliza,aseguradora,email);
            }
        });

        $("#catalogo").on("click",".btn_detalle",function(e){
            var id_poliza = $(this).attr('data-id');
            var route = $(this).attr('data-route');
            modal = $("#modal_detalle_poliza");
            modal.find("#conceptos").empty();
            form = modal.find("#form_modal_detalle_poliza");
            modal.find("#id_poliza").val(id_poliza);
            modal.find("#action").val('detalle');

            $.ajax({
                url: route,
                data: form.serializeArray(),
                type:'POST',
                dataType: 'json',
                success: function(response) {
                    iterateValuesDetalle(response.objPoliza,modal);
                    iterateValuesDetalle(response.objConcesion,modal);
                    iterateValuesDetalle(response.objDetallePago,modal);
                    modal.modal('show');
                },
                error: function(response) {
                    console.log('Error: ' + response);
                    Swal.fire({
                        icon: response.status,
                        title: response.title,
                        text: response.message,
                        confirmButtonText: "{{__('messages.aceptar')}}",
                    });
                }
            });
        });

        $("#modal_detalle_poliza").on("click","#btn_print_pdf",function(e){
            e.preventDefault();
            modal = $("#modal_detalle_poliza");
            form = modal.find("#form_modal_detalle_poliza");
            modal.find("#action").val('pdf');
            form.attr('action','{{route("detalle_datos_concesion")}}');
            form.submit();
        });

        $("#modalReplaceFilePoliza").on('click','#btn_guardar',function(e){
            e.preventDefault();
            modal = $("#modalReplaceFilePoliza");
            form = modal.find("#formReplaceFilePoliza");
            var formData = new FormData();
            var archivo = modal.find("#archivo")[0].files[0];
            var id_poliza = modal.find("#id_poliza").val();
            var csrf_tpken = modal.find("input[name='_token']").val();

            if(archivo != null){
                formData.append("_token", csrf_tpken);
                formData.append("archivo", archivo);
                formData.append("id_poliza", id_poliza);
                
                $.ajax({
                    url: form.attr('action'),
                    data: formData,
                    type:'POST',
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        modal.find("#btn_guardar").attr('disabled','disabled');
                        let timerInterval
                        Swal.fire({
                            title: '{{__("messages.msg_guardando_datos")}}',
                            html: ' <b></b>',
                            allowOutsideClick: false,
                            timer: 2000000,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });
                    },
                    success: function(response) {
                        modal.find("#btn_guardar").removeAttr('disabled');
                        Swal.close();
                        Swal.fire({
                            icon: response.status,
                            title: response.title,
                            text: response.message,
                            confirmButtonText: "{{__('messages.aceptar')}}",
                        });
                        if(response.status == "success"){
                            getData();
                            if(modal.find("#archivo").get(0).files.length > 0){
                                eventDropi = $('.dropify').dropify().data('dropify');
                                eventDropi.resetPreview();
                                eventDropi.clearElement();
                                eventDropi.destroy();
                                eventDropi.init();
                                dropifyInit();
                            }
                            modal.find("#archivo").removeClass('is-invalid');
                            modal.find('#archivo_error').removeClass('d-block').attr('style','display: none;');
                            modal.modal('toggle');
                        }
                    },
                    error: function(response) {
                        console.log('Error: ' + response);
                        modal.find("#btn_guardar").removeAttr('disabled');
                        Swal.fire({
                            icon: response.status,
                            title: response.title,
                            text: response.message,
                            confirmButtonText: "{{__('messages.aceptar')}}",
                        });
                    }
                });
            }
            else{
                camposmal = "<ol>";
                if (archivo == null) {
                    camposmal += "<li>Documento de la póliza  </li>";
                    modal.find('#archivo').addClass('is-invalid');
                    modal.find('#archivo_error').addClass('d-block');
                    modal.find('#archivo_error').html('<strong>El documento de la póliza es un campo requerido</strong>');
                }
                camposmal += "</ol>";

                Swal.fire({
                    title: '<strong><u>Complete los campos</u></strong>',
                    icon: 'warning',
                    html: camposmal.toString(),
                    showCloseButton: true,
                    showCancelButton: true,
                    focusConfirm: false,
                });
            }
        });

        function iterateValuesDetalle(obj,modal){
            $.each( obj, function( key, value ) {
                switch(key){
                    case 'fecha_vencimiento_poliza': case 'fecha_creacion_poliza': case 'fecha_vencimiento':
                        valor = new Date(value).toLocaleDateString("es-MX");
                        modal.find("#lbl_"+key).html(valor);
                        break;
                    case 'importe_total': case 'importe_concesion': case 'importe_refrendo':
                        modal.find("#lbl_"+key).html("$ "+value);
                        break;
                    case 'estatus_pago':
                        valor = value == 0 ? "Pendiente de pago" : "Pagado";
                        modal.find("#lbl_"+key).html(valor);
                        break;
                    case 'estatus':
                        valor = value == 0 ? "Pendiente" : (value == 1 ? "Inconsistente" : "Revisada");
                        modal.find("#lbl_"+key).html(valor);
                        break;
                    case 'detalle_conceptos':
                        indice = 0;
                        total_vu = 0;
                        total_imp = 0;
                        nf = new Intl.NumberFormat("en-US", {style: "currency",currency: "USD",maximumFractionDigits: 2,});
                        if(value != 'N/A'){
                            modal.find("#div_tabla_conceptos").attr('style','display:block;');
                            modal.find("#div_detalle_pago").attr('style','display:block;');
                            modal.find("#section_detalle_pago").find("#alert_message").remove();
                            $.each( JSON.parse(value), function( key, json_obj ) {
                                row_class = parseInt(key) % 2 == 0 ? "color:#212529;" : "odd"; 
                                
                                modal.find("#table_conceptos tbody").append('<tr role="row" id="tr_'+key+'" style="'+row_class+'"></tr>')
                                modal.find("#tr_"+key).append('<td>'+json_obj['PERIODO']+'</td>');
                                modal.find("#tr_"+key).append('<td>'+json_obj['CANTIDAD']+'</td>');
                                modal.find("#tr_"+key).append('<td>'+json_obj['CLAVE']+'</td>');

                                if('CONCEPTO' in json_obj){
                                    modal.find("#tr_"+key).append('<td>'+json_obj['CONCEPTO']+'</td>');
                                }
                                else{
                                    modal.find("#tr_"+key).append('<td></td>');
                                }
                                
                                modal.find("#tr_"+key).append('<td style="text-align: right;">'+nf.format(json_obj['VALOR_UNITARIO'].replace('-',''))+'</td>');
                                modal.find("#tr_"+key).append('<td style="text-align: right;">'+nf.format(json_obj['IMPORTE'].replace('-',''))+'</td>');

                                indice = key;
                                total_vu += +json_obj['VALOR_UNITARIO'].replace('-','');
                                total_imp += +json_obj['IMPORTE'].replace('-','');
                            });
                            
                            indice++;
                            row_class = parseInt(indice) % 2 == 0 ? "color:#212529;" : "odd"; 
                            modal.find("#table_conceptos tbody").append('<tr role="row" id="tr_'+indice+'" style="'+row_class+'"></tr>')
                            modal.find("#tr_"+indice).append('<td></td>');
                            modal.find("#tr_"+indice).append('<td></td>');
                            modal.find("#tr_"+indice).append('<td></td>');
                            modal.find("#tr_"+indice).append('<th style="text-align: right;">{{ __("messages.total") }}:</th>');
                            modal.find("#tr_"+indice).append('<td style="text-align: right;">'+nf.format(total_vu)+'</td>');
                            modal.find("#tr_"+indice).append('<td style="text-align: right;">'+nf.format(total_imp)+'</td>');
                        }
                        else{
                            modal.find("#div_tabla_conceptos").attr('style','display:none;');
                            modal.find("#div_detalle_pago").attr('style','display:none;');
                            modal.find("#section_detalle_pago").find("#alert_message").remove();
                            modal.find("#section_detalle_pago").append('<div class="alert alert-warning" id="alert_message" role="alert"><h4>Sin adeudos</h4><br>Periodo '+obj.ejercicio+'</div>')
                        }
                        break;
                    default:
                        modal.find("#lbl_"+key).html(value);
                        break;
                }
            });
        }

        $("#catalogo").on("click",".btn_change_status",function(e){
            var id_poliza = $(this).attr('data-id');
            var route = $(this).attr('data-route');
            $("#modal_change_estatus").find("#form_modal_change_estatus").attr('action',route);
            $("#modal_change_estatus").find("#id_poliza").val(id_poliza);
            $("#modal_change_estatus").modal('show');
        });

        $("#modal_change_estatus").on("change","#estatus",function(){
            var estatus = $(this).val();
            $("#modal_change_estatus").find("#observaciones").val('');

            if(estatus != ""){
                $("#modal_change_estatus").find("#btn_guardar").removeAttr('disabled');
            }
            else{
                $("#modal_change_estatus").find("#btn_guardar").attr('disabled','disabled');
            }

            if(estatus == "1"){
                $("#modal_change_estatus").find("#div_obs").removeAttr('style');
                $("#modal_change_estatus").find("#observaciones").attr('required','required');
            }
            else{
                $("#modal_change_estatus").find("#div_obs").attr('style','display: none;');
                $("#modal_change_estatus").find("#observaciones").removeAttr('required');
            }
        });

        $("#modal_change_estatus").on("click","#btn_guardar",function(e){
            e.preventDefault();
            form = $(this).closest("#form_modal_change_estatus");
            modal = $("#modal_change_estatus");
            
            $.ajax({
                url: form.attr('action'),
                data: form.serializeArray(),
                type:'POST',
                dataType: 'json',
                beforeSend: function() {
                    modal.find("#btn_guardar").attr('disabled','disabled');
                    let timerInterval
                    Swal.fire({
                        title: '{{__("messages.msg_guardando_datos")}}',
                        html: ' <b></b>',
                        allowOutsideClick: false,
                        timer: 2000000,
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                },
                success: function(response) {
                    modal.find("#btn_guardar").removeAttr('disabled');
                    Swal.close();
                    Swal.fire({
                        icon: response.status,
                        title: response.title,
                        text: response.message,
                        confirmButtonText: "{{__('messages.aceptar')}}",
                    });
                    if(response.status == "success"){
                        getData();
                        limpiarCamposEstatusModal();
                        modal.modal('toggle');
                    }
                },
                error: function(response) {
                    console.log('Error: ' + response);
                    modal.find("#btn_guardar").removeAttr('disabled');
                    Swal.fire({
                        icon: response.status,
                        title: response.title,
                        text: response.message,
                        confirmButtonText: "{{__('messages.aceptar')}}",
                    });
                }
            });
        });

        $("#catalogo").on("click",".btnViewFile",function(e){
            e.preventDefault();
            var poliza_id = $(this).attr('data-id');
            var form = $("#previewFileForm");
            form.find('#id_hidden').val(poliza_id);
            var datos = form.serializeArray();
            
            $.ajax({
                url: form.attr('action'),
                data: datos,
                type:'POST',
                dataType: 'json',
                success: function(response) {
                    $("#modal_view_archivo").find("#title_modal").empty();
                    $("#modal_view_archivo").find("#title_modal").html('{{__("messages.view_poliza")}}');
                    $("#modal_view_archivo").find("#view_file").empty();
                    var fileView = '';
                    if(response.extension == "pdf"){
                        fileView = '<embed class="fileView" src="'+response.ruta+'" type="'+response.content_type+'" width="100%" height="750px">';
                    }
                    else if(response.extension == "jpg" || response.extension == "png"){
                        fileView = '<img class="fileView" src="'+response.ruta+'" alt="{{ __("messages.poliza") }}">';
                    }
                    else{
                        fileView = '<a class="fileView btn btn-primary" type"button" href="'+response.ruta+'">{{__("messages.descargar_poliza")}} '+response.extension+'</a>';
                    }
                    
                    $("#modal_view_archivo").find("#view_file").append(fileView);
                    $("#modal_view_archivo").modal("show");
                },
                error: function(response) {
                    console.log('Error: ' + response);
                }
            });
        });

        //Validacion de correo
        var emailInput = document.getElementById("email");
        emailInput.onkeyup = function() {
            var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            if(emailInput.value.length == 0){
                $("#modalNuevaPoliza").find('#email').addClass('is-invalid');
                $("#modalNuevaPoliza").find('#email_error').addClass('d-block');
                $("#modalNuevaPoliza").find('#email_error').html('<strong>El correo electrónico es un campo requerido</strong>');
            }
            else{
                $("#modalNuevaPoliza").find("#email").removeClass('is-invalid');
                $('#modalNuevaPoliza').find('#email_error').removeClass('d-block').attr('style','display: none;');
            }
            if(emailInput.value.match(mailformat)){
                $("#email-invalido").css("display","none");
            }
            else{
                $("#email-invalido").css("display","initial");
            }
        }

        var phoneInput = document.getElementById("telefono");
        phoneInput.onkeyup = function() {
            var phoneformat = /^\(?(\d{3})\)?[- ]?(\d{3})[- ]?(\d{4})$/;
            if(phoneInput.value.match(phoneformat)){
                $("#telefono-invalido").css("display","none");
            }
            else{
                $("#telefono-invalido").css("display","initial");
            }
        }

        var noPolizaInput = document.getElementById("no_poliza");
        noPolizaInput.onkeyup = function() {
            if(noPolizaInput.value.length == 0){
                $("#modalNuevaPoliza").find('#no_poliza').addClass('is-invalid');
                $("#modalNuevaPoliza").find('#no_poliza_error').addClass('d-block');
                $("#modalNuevaPoliza").find('#no_poliza_error').html('<strong>El número de póliza es un campo requerido</strong>');
            }
            else{
                $("#modalNuevaPoliza").find("#no_poliza").removeClass('is-invalid');
                $('#modalNuevaPoliza').find('#no_poliza_error').removeClass('d-block').attr('style','display: none;');
            }
                
            if(noPolizaInput.value.length >= 3 && noPolizaInput.value.length <= 20){
                $("#no_poliza-invalido").css("display","none");
            }
            else{
                $("#no_poliza-invalido").css("display","initial");
            }
        }

        var fechaVenInput = $("#modalNuevaPoliza");
        fechaVenInput.on('change','#fecha_vencimiento',function() {
            if(fechaVenInput.find('#fecha_vencimiento').val().length == 0){
                $("#modalNuevaPoliza").find('#fecha_vencimiento').addClass('is-invalid');
                $("#modalNuevaPoliza").find('#fecha_vencimiento_error').addClass('d-block');
                $("#modalNuevaPoliza").find('#fecha_vencimiento_error').html('<strong>La fecha de vencimiento es un campo requerido</strong>');
            }
            else{
                $("#modalNuevaPoliza").find("#fecha_vencimiento").removeClass('is-invalid');
                $('#modalNuevaPoliza').find('#fecha_vencimiento_error').removeClass('d-block').attr('style','display: none;');
            }
        });

        function dropifyInit(){
            $('.dropify').dropify({
                allowedFiles: ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar'],
                messages: {
                    'default': 'Arrastre y suelte el archivo aquí o haga click',
                    'replace': 'Arrastre y suelte aquí o haga click para reemplazar',
                    'remove':  'Remover',
                    'error':   'Ooops, algo ha salido mal.'
                },
                error: {
                    'fileSize': 'El archivo es muy grande (máximo 6 Mb).',
                    'minWidth': 'El ancho de la imágen es muy pequeño (mínimo px).',
                    'maxWidth': 'El ancho de la imágen es muy grande (máximo px).',
                    'minHeight': 'El alto de la imágen es muy pequeño (mínimo px).',
                    'maxHeight': 'El alto de la imágen es muy grande (máxima px).',
                    'imageFormat': 'El formato de esta imágen no esta permitido (solo JPG).',
                    'fileFormat': 'El formato de archivo no esta permitido (solamente pdf, doc, docx, xls, xlsx, zip, rar).',
                }
            });
        }

        function limpiarCamposEstatusModal(){
            $('#modal_change_estatus').find("#id_act").val("");
            $('#modal_change_estatus').find("#estatus").val("");
            $('#modal_change_estatus').find("#observaciones").val("");
            $('#modal_change_estatus').find("#div_obs").attr('style','display: none;');
        }

        function validarCampos(archivo,fecha_vencimiento,no_poliza,aseguradora,email){
            camposmal = "<ol>";
            if (archivo == null) {
                camposmal += "<li>Documento de la póliza  </li>";
                $("#modalNuevaPoliza").find('#archivo').addClass('is-invalid');
                $("#modalNuevaPoliza").find('#archivo_error').addClass('d-block');
                $("#modalNuevaPoliza").find('#archivo_error').html('<strong>El archivo es un campo requerido</strong>');
            }
            if (fecha_vencimiento == "") {
                camposmal += "<li> Fecha de expiración de la póliza  </li>";
                $("#modalNuevaPoliza").find('#fecha_vencimiento').addClass('is-invalid');
                $("#modalNuevaPoliza").find('#fecha_vencimiento_error').addClass('d-block');
                $("#modalNuevaPoliza").find('#fecha_vencimiento_error').html('<strong>La fecha de vencimiento es un campo requerido</strong>');
            }
            if (no_poliza == "") {
                camposmal += "<li> Número de poliza </li>";
                $("#modalNuevaPoliza").find('#no_poliza').addClass('is-invalid');
                $("#modalNuevaPoliza").find('#no_poliza_error').addClass('d-block');
                $("#modalNuevaPoliza").find('#no_poliza_error').html('<strong>El número de póliza es un campo requerido</strong>');
            }
            if (aseguradora == "") {
                camposmal += "<li>Selecciona una aseguradora </li>";
                $("#modalNuevaPoliza").find('#no_poliza').addClass('is-invalid');
                $("#modalNuevaPoliza").find('#no_poliza_error').addClass('d-block');
                $("#modalNuevaPoliza").find('#no_poliza_error').html('<strong>El número de póliza es un campo requerido</strong>');
            }
            if (email == "") {
                camposmal += "<li>Ingresa un email </li>";
                $("#modalNuevaPoliza").find('#email').addClass('is-invalid');
                $("#modalNuevaPoliza").find('#email_error').addClass('d-block');
                $("#modalNuevaPoliza").find('#email_error').html('<strong>El correo electrónico es un campo requerido</strong>');
            } else {
                //  block of code to be executed if the condition1 is false and condition2 is false
            }
            camposmal += "</ol>";

            Swal.fire({
                title: '<strong><u>Complete los campos</u></strong>',
                icon: 'warning',
                html: camposmal.toString(),
                showCloseButton: true,
                showCancelButton: true,
                focusConfirm: false,
            });
        }

        function limpiarCamposAddPoliza(){
            $("#modalNuevaPoliza").find("#no_consesion").val("");
            $("#modalNuevaPoliza").find("#aseguradora").val("").removeClass('is-invalid');
            $("#modalNuevaPoliza").find("#name_aseg_otro").val("").removeClass('is-invalid');
            $("#modalNuevaPoliza").find("#no_poliza").val("").removeClass('is-invalid');
            $("#modalNuevaPoliza").find("#fecha_vencimiento").val("").removeClass('is-invalid');
            $("#modalNuevaPoliza").find("#archivo").val("").removeClass('is-invalid');
            $("#modalNuevaPoliza").find("#email").val("").removeClass('is-invalid');
            $("#modalNuevaPoliza").find("#telefono").val("").removeClass('is-invalid');
            $("#modalNuevaPoliza").find("#observaciones").val("");
            $("#modalNuevaPoliza").find("#checkterms").val("");
            $("#modalNuevaPoliza").find("#name_aseg_otro_div").attr('hidden','hidden');

            $('#modalNuevaPoliza').find('#aseguradora_error').removeClass('d-block').attr('style','display: none;');
            $('#modalNuevaPoliza').find('#name_aseg_otro_error').removeClass('d-block').attr('style','display: none;');
            $('#modalNuevaPoliza').find('#no_poliza_error').removeClass('d-block').attr('style','display: none;');
            $('#modalNuevaPoliza').find('#fecha_vencimiento_error').removeClass('d-block').attr('style','display: none;');
            $('#modalNuevaPoliza').find('#archivo_error').removeClass('d-block').attr('style','display: none;');
            $('#modalNuevaPoliza').find('#email_error').removeClass('d-block').attr('style','display: none;');
            $('#modalNuevaPoliza').find('#telefono_error').removeClass('d-block').attr('style','display: none;');
            
            if($("#modalNuevaPoliza").find("#archivo").get(0).files.length > 0){
                eventDropi = $("#modalNuevaPoliza").find('.dropify').dropify().data('dropify');
                eventDropi.resetPreview();
                eventDropi.clearElement();
                eventDropi.destroy();
                eventDropi.init();
                dropifyInit();
            }
        }
    });
</script>
@endsection