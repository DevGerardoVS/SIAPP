<div class="modal fade bd-example-modal-lg" id="exampleModal" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#6A0F49 !important; color:whitesmoke">
                <h5 class="modal-title" id="exampleModalLabel">Agregar techo financiero</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" >
                    <a aria-hidden="true" style="color: whitesmoke" onclick="dao.limpiarFormularioCrear()">&times;</a>
                </button>
            </div>
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <div class="modal-body">
                <form id="frm_create_techo">
                    @csrf
                    <div class="row align-items-start">
                        <div class="col-sm-1">
                            <label><b>UPP:</b></label>
                        </div>
                        <?php $upp = DB::table('v_epp')->select('clv_upp','upp')->distinct()->get();?>
                        <div class="col-sm-7" >
                            <select class="form-control filters" placeholder="Seleccione una UPP" id="uppSelected" name="uppSelected" required>
                                <option value="0" selected>Seleccione una UPP</option>
                                @foreach($upp as $u)
                                <option value="{{$u->clv_upp}}" >{{$u->clv_upp.' - '.$u->upp}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div> <br>
                    <div class="row">
                        <div class="col-sm-1">
                             <label><b>Año</b></label>
                        </div>
                        <div class="col-sm-7">
                            <?php $ejercicio = DB::table('epp') ->select('ejercicio')->groupBy('ejercicio')->orderByDesc('ejercicio')->limit(1)->get();?>
                            <select class="form-control filters" id="anio" name="anio" autocomplete="anio" placeholder="Seleccione un año">
                                @foreach($ejercicio as $e)
                                <option value="{{$e->ejercicio}}" >{{$e->ejercicio}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <button class="btn btn-primary" id="agregar_fondo">Agregar fondo</button>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        
                    </div>
                    <br>
                    <div class="row">
                        <div class="widget-body no-padding ">
                            <div class="table-responsive" id="tableScroll">
                                <table id="fondos" class="table table-hover table-striped ">
                                    <thead>
                                        <tr class="colorMorado">
                                            <th>Tipo</th>
                                            <th>Fondo</th>
                                            <th>Presupuesto</th>
                                            <th>Ejercicio</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="background-color: darkgray; margin: 5px;padding: 10px">
                        <div class="col-md-5"></div>
                        <div class="col-md-2"><h5><b>Total UPP: </b></h5></div>
                        <div class="col-md-3"><input class="form-control totales" id="total-presupuesto" placeholder="$0" type="text" disabled></div>
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
