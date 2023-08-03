<div class="container d-flex justify-content-center">
    <div class="row">
        <div class="d-flex justify-content-center"> 
            <header  style=" border-bottom: 5px solid #17a2b8;">
                <h2 class="text-justify" style="text-align:center;">Listado de Usuarios</h2>
            </header>
        </div>
                <br>
                    <div>
                        <table id="catalogo"  style="border: 1px solid #000;">
                            <thead>
                                <tr style="background-color: #6A0F49 !important;color: white !important; border: 1px solid #000;">
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Apellido paterno</th>
                                    <th>Apellido Materno</th>
                                    <th>Usuario</th>
                                    <th>Correo</th>
                                    <th>Teléfono</th>
                                </tr>
                            </thead>
                            <tbody>
                            </thead>
                            <tbody >
                                @isset($data)
                                @foreach ($data as $i)
                                <tr style="border: 1px solid #000;">
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
