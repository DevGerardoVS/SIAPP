<div class="container d-flex justify-content-center">
    <div class="row">
        <div class="d-flex justify-content-center"> 
            <header  style=" border-bottom: 5px solid #17a2b8;">
                <h2 class="text-justify" style="text-align:center;">Proyectos con actividades</h2>
            </header>
        </div>
                <br>
                    <div >
                        <table id="catalogo"  style="border: 1px solid #000; ">
                            <thead>
                                <tr style="background-color: #6A0F49 !important;color: white !important; border: 1px solid #000;">
                                    <th>&nbsp;<h3 style="transform: rotate(-90deg); width: 20px;">ID</h3></th>
                                    <th>&nbsp;<h3 style="transform: rotate(-90deg); width: 20px;">Finalidad</h3></th>
                                    <th>&nbsp;<h3 style="transform: rotate(-90deg); width: 20px;">Función</h3></th>
                                    <th>&nbsp;<h3 style="transform: rotate(-90deg); width: 20px;">Subfunción</h3></th>
                                    <th>&nbsp;<h3 style="transform: rotate(-90deg); width: 20px;">Eje</h3></th>
                                    <th>&nbsp;<h3 style="transform: rotate(-90deg); width: 60px;">Linea de Accion</h3></th>
                                    <th>&nbsp;<h3 style="transform: rotate(-90deg); width: 40px;">Programa sectorial</h3></th>
                                    <th>&nbsp;<h3 style="transform: rotate(-90deg); width: 40px;">Tipologia CONAC</h3></th>
                                    <th>&nbsp;<h3 style="transform: rotate(-90deg); width: 20px;">UPP</h3></th>
                                    <th>&nbsp;<h3 style="transform: rotate(-90deg); width: 20px;">UR</h3></th>
                                    <th>&nbsp;<h3 style="transform: rotate(-90deg); width: 20px;">Programa</h3></th>
                                    <th>&nbsp;<h3 style="transform: rotate(-90deg); width: 50px;">Sub programa</h3></th>
                                    <th>&nbsp;<h3 style="transform: rotate(-90deg); width: 40px;">Proyecto</h3></th>
                                    <th>&nbsp;<h3 style="transform: rotate(-90deg); width: 30px;">Fondo</h3></th>
                                    <th>&nbsp;<h3 style="transform: rotate(-90deg); width: 40px;">Actividad</h3></th>
                                    <th>&nbsp;<h3 style="transform: rotate(-90deg); width: 80px;">Tipo Actividad</h3></th>
                                    <th>&nbsp;<h3 style="transform: rotate(-90deg); width: 40px;">Meta anual</h3></th>
                                    <th>&nbsp;<h3 style="transform: rotate(-90deg); width: 70px;">Numero de beneficiarios</h3></th>
                                    <th>&nbsp;<h3 style="transform: rotate(-90deg); width: 100px;">Beneficiarios</h3></th>
                                    <th>&nbsp;<h3 style="transform: rotate(-90deg); width: 120px;">Unidad de medida</h3></th>
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
