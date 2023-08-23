<@php
    $anio = date('Y');
    $title = "Presupuesto por fondo ".$anio;
@endphp
<div class="container d-flex justify-content-center">
    <div class="row">
        <div class="d-flex justify-content-center">
            <header>
                <h2 class="text-justify" style="text-align:center;"> {{$title}}
                </h2>
            </header>
        </div>
        <br>
        <div>
            <table style="border: solid 1px #777;">
                <thead>
                    <tr style="background-color: #6A0F49 !important;color: white !important; border: 1px solid #000;">
                        <th>Clave fondo</th>
                        <th>Fondo</th>
                        <th>$ Asignado</th>
                        <th>$ Programado</th>
                        <th>% Avance</th>
                    </tr>
                </thead>
                <tbody>
                    @isset($data)
                        @foreach ($data as $i)
                            <tr>
                                <td style="text-align: center;">{{ $i->clave }}</td>
                                <td>{{ $i->fondo }}</td>
                                <td style="text-align: right;">{{ $i->asignado }}</td>
                                <td style="text-align: right;">{{ $i->programado }}</td>
                                <td style="text-align: right;">{{ $i->avance }}</td>
                            </tr>
                        @endforeach
                    @endisset
                </tbody>
            </table>
        </div>


    </div>
</div>
</div>
