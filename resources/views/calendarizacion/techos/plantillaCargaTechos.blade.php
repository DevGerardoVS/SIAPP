<html lang="es">

<head>
    <meta charset="utf-8">
</head>
<table>
    <thead>
        <tr class="table-primary" style="background: #ccccff; color: black;">
            <th><b>EJERCICIO</b></th>
            <th><b>UPP</b></th>
            <th><b>FONDO</b></th>
            <th><b>OPERATIVO</b></th>
            <th><b>RECURSOS HUMANOS</b></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($upps as $upp)
            <tr class="table-secondary">
                <td>{{ $ejercicio}}</td>
                <td>{{ $upp->clave }}</td>
                <td>09</td>
                <td></td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>

</html>
