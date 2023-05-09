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
                        <th>{{ __('messages.nombre_propietario') }}</th>
                        <th>{{ __('messages.no_concesion') }}</th>
                        <th>{{ __('messages.tipo_servicio') }}</th>
                        <th>{{ __('messages.grupo') }}</th>
                        <th>{{ __('messages.modalidad') }}</th>
                        <th>{{ __('messages.num_poliza') }}</th>
                        <th>{{ __('messages.aseguradora') }}</th>
                        <th>{{ __('messages.fecha_vencimiento_poliza') }}</th>
                        <th>{{ __('messages.user_creacion') }}</th>
                        <th>{{ __('messages.observaciones') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $d)
                        <tr class="tr_body_content">
                            <td class="column_data">{{ $d->propietario }}</td>
                            <td class="column_data">{{ $d->no_concesion }}</td>
                            <td class="column_data">{{ $d->tipo_servicio }}</td>
                            <td class="column_data">{{ $d->grupo }}</td>
                            <td class="column_data">{{ $d->modalidad }}</td>
                            <td class="column_data">{{ $d->no_poliza }}</td>
                            <td class="column_data">{{ $d->aseguradora }}</td>
                            <td class="column_data">{{ $d->fecha_vencimiento_poliza }}</td>
                            <td class="column_data">{{ $d->user_creacion }}</td>
                            <td class="column_data">{{ $d->observaciones }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </body>
</html>