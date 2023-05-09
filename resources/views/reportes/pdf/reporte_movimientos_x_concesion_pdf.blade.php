<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>{{$title}}</title>
        <style>
            h5{
                text-align: center;
                text-transform: uppercase;
            }
            .center-img,
            .center-txt {
              display: inline-block;
              vertical-align: middle;
            }
            .contenido{
                font-size: 12px;
            }
            #tb_encabezado, #tb_contenido, #tb_pie{
                width: 100%;
            }
            .col_encabezado{
                width: 50%;
            }
            .col_pie{
                width: 40%;
                text-align: center;
            }
            .col_inter{
                width: 20%;
            }
            .col_firma{
                border-top: 1px solid #000000;
            }
            .tr_space{
                height: 300px;
            }
            .tr_head_content {
                background-color: #6A0F49 !important;
                color: white !important;
            }
            #tb_contenido tbody tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            .column_data{
                text-align: center;
            }
            .column_numbers{
                text-align: right;
            }
            #wrapper {
                text-align: left;
            }
        </style>
    </head>
    <body>
        <div id="wrapper">
            <img class="center-img" src="{{ public_path('img/SFA LogoHorizontalN.png') }}" style="width: 175px; height: 88px; margin-right: 50px;" alt="logo">
            <div class="center-txt">
               <h5 >{{$title}}</h5>
            </div>
        </div>
        <div class="contenido">
            <table id="tb_contenido">
                <thead>
                    <tr class="tr_head_content">
                        <th>{{ __('messages.no_concesion') }}</th>
                        <th>{{ __('messages.user') }}</th>
                        <th>{{ __('messages.movimiento') }}</th>
                        <th>{{ __('messages.datos') }}</th>
                        <th>{{ __('messages.fecha_hora') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $d)
                        <tr class="tr_body_content">
                            <td class="column_data">{{ $d['no_concesion'] }}</td>
                            <td class="column_data">{{ $d['usuario'] }}</td>
                            <td class="column_data">{{ $d['accion'] }}</td>
                            <td class="column_data"><b>Aseguradora:</b> {{ $d['aseguradora'] }}<br><b>Num. de poliza:</b> {{ $d['no_poliza'] }}<br><b>Vencimiento:</b> {{ $d['vencimiento'] }}<br></td>
                            <td class="column_data">{{ $d['created_at'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </body>
</html>