<div class="container d-flex justify-content-center">
    <div class="row">
        <div class="d-flex justify-content-center"> 
            <header  style=" border-bottom: 5px solid #17a2b8;">
                <h2 class="text-justify" style="text-align:center;">Proyectos con actividades</h2>
            </header>
        </div>
                <br>
                    <div>
                        <table id="catalogo"  style="border: 1px solid #000; text-align: left; vertical-align: top; margin-left: auto; margin-right: auto; text-align: left">
                            <thead>
                                <tr style="background-color: #6A0F49 !important;color: white !important; border: 1px solid #000; text-align: left; vertical-align: top;">
                                    <th style="writing-mode:vertical-rl; text-align: center; rotate: 180deg;">Finalidad</th>
                                    <th style="writing-mode:vertical-rl; text-align: center; rotate: 180deg;">Función</th>
                                    <th style="writing-mode:vertical-rl; text-align: center; rotate: 180deg;">Subfunción</th>
                                    <th style="writing-mode:vertical-rl; text-align: center; rotate: 180deg;">Eje</th>
                                    <th style="writing-mode:vertical-rl; text-align: center; rotate: 180deg;">Linea de Accion</th>
                                    <th style="writing-mode:vertical-rl; text-align: center; rotate: 180deg;">Programa sectorial</th>
                                    <th style="writing-mode:vertical-rl; text-align: center; rotate: 180deg;">Tipologia CONAC</th>
                                    <th style="writing-mode:vertical-rl; text-align: center; rotate: 180deg;">UP</th>
                                    <th style="writing-mode:vertical-rl; text-align: center; rotate: 180deg;">UR</th>
                                    <th style="writing-mode:vertical-rl; text-align: center; rotate: 180deg;">Programa</th>
                                    <th style="writing-mode:vertical-rl; text-align: center; rotate: 180deg;">Subprograma</th>
                                    <th style="writing-mode:vertical-rl; text-align: center; rotate: 180deg;">Proyecto</th>
                                    <th style="writing-mode:vertical-rl; text-align: center; rotate: 180deg;">Fondo</th>
                                    <th style="writing-mode:vertical-rl; text-align: center; rotate: 180deg;">Actividad</th>
                                    <th style="writing-mode:vertical-rl; text-align: center; rotate: 180deg;">Tipo Actividad</th>
                                    <th style="writing-mode:vertical-rl; text-align: center; rotate: 180deg;">Meta anual</th>
                                    <th style="writing-mode:vertical-rl; text-align: center; rotate: 180deg;"># Beneficiarios</th>
                                    <th style="writing-mode:vertical-rl; text-align: center; rotate: 180deg;">Beneficiarios</th>
                                    <th style="writing-mode:vertical-rl; text-align: center; rotate: 180deg;">U de medida</th>
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
