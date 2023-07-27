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
                                    @foreach ($i as $item => $value)
                                    <td>{{{$value}}}</td> 
                                    @endforeach
                                  </tr>
                                @endforeach
                                    
                                @endisset
                        </table>
                </div>
             

            </div>
    </div>
</div>
