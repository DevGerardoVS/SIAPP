<div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#6A0F49 !important; color:whitesmoke">
                <h5 class="modal-title" id="exampleModalLabel">Agregar usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
                    <a aria-hidden="true" style="color: whitesmoke" onclick="dao.limpiarFormularioCrear()">&times;</a>
                </button>
            </div>
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <div class="modal-body">
                <form id="frm_create">
                    @csrf
                    <textarea type="text" value="0" id="id_user" name="id_user" style="display: none"></textarea>
                    <div class="row">
                        <div class="col-sm-1">
                            <labe>Año</labe>
                        </div>
                        <div class="col-sm-2">
                            <select class="form-control filters" id="anio" name="anio"
                                    autocomplete="anio" placeholder="Seleccione un año" disabled>
                                <option id="anioOpt" value="" selected>2024</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <labe>Unidad Programática Presupuestal: </labe>
                        </div>
                        <?php $upp = DB::table('v_entidad_ejecutora')->get();?>
                        <div class="col-sm-5">
                            <select class="form-control filters" placeholder="Seleccione una UPP">
                                <option value="" selected>Seleccione una UPP</option>
                        @foreach($upp as $u)
                                <option value="" >{{$u->upp}}</option>
                        @endforeach
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-4">
                            <button class="btn btn-primary" id="agregar_fondo">Agregar fondo</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="widget-body no-padding ">
                            <div class="table-responsive ">
                                <table id="fondos" class="table table-hover table-striped ">
                                    <thead>
                                        <tr class="colorMorado">
                                            <th>Tipo</th>
                                            <th>Fondo</th>
                                            <th>Monto</th>
                                            <th>Ejercicio</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <br>
                </form>
            </div>
            <div class="modal-footer">
                <button  id="cerrar" type="button" class="btn btn-secondary " data-dismiss="modal" aria-label="Close" onclick="dao.limpiarFormularioCrear()">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSave">Guardar</button>
            </div>
        </div>
    </div>
</div>
