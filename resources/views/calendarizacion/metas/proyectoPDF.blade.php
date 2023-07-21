<div class="container d-flex justify-content-center">
    <div class="row">
        <div class="d-flex justify-content-center"> 
            <header  style=" border-bottom: 5px solid #17a2b8;">
                <h2 class="text-justify" style="text-align:center;">Proyectos con actividades</h2>
            </header>
        </div>
                <br>
                    <div>
                        <table id="catalogo" style="border: 1px solid #000; text-align: left; vertical-align: top;">
                            <thead>
                                <tr style="background-color: #6A0F49 !important;color: white !important; border: 1px solid #000; text-align: left; vertical-align: top;">
                                    <th >Finalidad</th>
                                    <th >Función</th>
                                    <th >Subfunción</th>
                                    <th >Eje</th>
                                    <th >Linea de Accion</th>
                                    <th >Programa sectorial</th>
                                    <th >Tipologia CONAC</th>
                                    <th >UP</th>
                                    <th >UR</th>
                                    <th >Programa</th>
                                    <th >Subprograma</th>
                                    <th >Proyecto</th>
                                    <th >Fondo</th>
                                    <th >Actividad</th>
                                    <th >Tipo Actividad</th>
                                    <th >Meta anual</th>
                                    <th ># Beneficiarios</th>
                                    <th >Beneficiarios</th>
                                    <th >U de medida</th>
                                </tr>
                            </thead>
                            <tbody>
                            </thead>
                            <tbody >
                                @isset($data)
                                @foreach ($data as $i)
                                <tr style="border: 1px solid #000; text-align: left; vertical-align: top;">

                                    <td>{{{$i->finalidad}}}</td>
                                    <td>{{{$i->funcion}}}</td>
                                    <td>{{{$i->subfuncion}}}</td>
                                    <td>{{{$i->eje}}}</td>
                                    <td>{{{$i->linea}}}</td>
                                    <td>{{{$i->programaSec}}}</td>
                                    <td>{{{$i->tipologia}}}</td>
                                    <td>{{{$i->upp}}}</td>
                                    <td>{{{$i->ur}}}</td>
                                    <td>{{{$i->programa}}}</td>
                                    <td>{{{$i->subprograma}}}</td>
                                    <td>{{{$i->proyecto}}}</td>
                                    <td>{{{$i->fondo}}}</td>
                                    <td>{{{$i->actividad}}}</td>
                                    <td>{{{$i->tipo}}}</td>
                                    <td>{{{$i->total}}}</td>
                                    <td>{{{$i->cantidad_beneficiarios}}}</td>
                                    <td>{{{$i->beneficiario}}}</td>
                                    <td>{{{$i->unidad_medida}}}</td>
                                  </tr>
                                @endforeach
                                    
                                @endisset
                            </tbody>
                            </tbody>
                        </table>
                </div>
             

            </div>
    </div>
</div>
