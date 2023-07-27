<!DOCTYPE html>
<html lang="es">
    
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>
    <div class="row">
        <table>
            <thead style="background-color: aqua !important;">
                <tr class="table-primary">
                    <th><b>Clave UPP</b></th>
                    <th><b>UPP</b></th>
                    <th><b>TIPO</b></th>
                    <th><b>Clave FONDO</b></th>
                    <th><b>FONDO</b></th>
                    <th><b>PRESUPUESTO</b></th>
                    <th><b>EJERCICIO</b></th>
                </tr>
            </thead>
        <tbody>
            @foreach ($data as $d)
            <tr class="table-secondary">
                <td>{{ $d->clv_upp }}</td>
                <td>{{$d->descPre}}</td>
                <td>{{$d->tipo}}</td>
                <td>{{$d->clv_fondo}}</td>
                <td>{{$d->fondo_ramo}}</td>
                <td>{{'$ '.number_format($d->presupuesto,2)}}</td>
                <td>{{$d->ejercicio}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>

</html>
