@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <button type="button" class="btn colorMorado"
            name="button_modal_carga" id="button_modal_carga">
      <i class="fas fa-plus"></i>
      {{ __('messages.carga_masiva') }}</button>
            <div class="card">
                <div class="card-header">{{ __('Bienvenido') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('Usted ha iniciado sesión en  Comisión Coordinadora del Transporte Público de Michoacán') }}
                </div>
            </div>
        </div>
    </div>
</div>

@include('.CargamasivaModal')

<script type="text/javascript">
    //boton para api soap

    $(".container").on('click', '#button_modal_carga', function () {
        $('#ModalCargaMasiva').modal('show');
    })
    </script>


@endsection

