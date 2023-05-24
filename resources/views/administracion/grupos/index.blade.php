@extends('layouts.app')
@section('content')
<div class="container">

<section id="widget-grid">
        <div class="row">
            <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                <div class="jarviswidget" id="wid-id-1" data-widget-editbutton="false" data-widget-colorbutton="false" data-widget-deletebutton="false">
                    <header>
                        <h2>Gestor de Grupos</h2>
                    </header>
                    <div>
                        <div class="jarviswidget-editbox">
                        </div>
                        <div class="widget-body-toolbar">
                            <div class="row">
                                <div class="col-xs-9 col-sm-5 col-md-5 col-lg-5">
                                </div>
                                <div class="col-xs-3 col-sm-7 col-md-7 col-lg-7 text-right">
                                    <a class="btn btn-success" id="btnNew" href="/adm-grupos/create">
                                        <i class="fa fa-plus"></i> <span class="hidden-mobile">Agregar Nuevo Grupo</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('getGroups') }}" id="buscarForm" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-sm-2">
                            </div>
                        </form>
                        <br>
                        <table id="catalogo" class="table table-striped table-bordered text-center " style="width:100%">
                            <thead>
                            <tr class="colorMorado">
                                <th>Nombre</th>
                                <th>Administración</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </article>
        </div>
    </section>

    <!--modal delete-->
    <div class="modal fade" id="modaldel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header colorMorado">
                    <h5 class="modal-title" id="exampleModalLabel">Eliminar grupo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <input type="number" id="idHidden" hidden >
                </div>
                <div class="modal-body">
                    ¿Está seguro que desea eliminar el grupo? <strong id="deshabilitar-user"></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__("messages.cancelar")}}</button>
                    <button id="confirmar-baja" type="button" class="btn btn-primary">Eliminar grupo</button>
                </div>
            </div>
        </div>
    </div>
</div>
@isset($dataSet)
@include('panels.datatable')
@endisset

<script src="/js/administracion/grupos/init.js"></script>
<script>
	dao.getData();
    $(document).ready(function () {
       getData();
    });

    function eliminarRegistro(i){
        $('#idHidden').val(i)
    }

    $('#confirmar-baja').on('click', function (e) {
        e.preventDefault()

        $.ajax({
            url:"{{route('postDelete')}}",
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                "id" : $('#idHidden').val()
            },
            success:function(response){
                console.log(response)
                if (response == "done") {
                    window.location.href = '/adm-grupos';
                }
            },
            error: function(response) {
                console.log('Error: ' + response);
            }
        });
    })

</script>
@endsection
