<?php
use App\Models\Perfiles;
use Illuminate\Support\Facades\Auth;

//{{$titleDesc}}
if (isset(Auth::user()->id)) {
    $menu = Perfiles::select('perfiles.id', 'perfiles.menu')
        ->join('users', 'users.perfil_id', '=', 'perfiles.id')

        ->where('users.id', Auth::user()->id)
        ->first();
    $menu = $menu->menu;
    $menu = json_decode($menu, true);

    $route = Route::current()->getName();
}
$urlcongion = Request::path();
$urlsinguion = explode('/', $urlcongion);

?>
@extends('layouts.app')

@section('content')
    @include('mensaje.mensajes')

    <div id="contenedor">
        <div id="menuLateral">
            <ul id="myUL">
                <li>
                    @foreach ($menu as $item)
                        @if (mb_strtolower($item['namemodulo']) == $urlsinguion[0])
                            <div class="caret colorMorado categoria d-flex align-items-center" data-bs-toggle="collapse"
                                href="#collapse{{ $item['namemodulo'] }}"
                                style="min-height:40px; padding-left:2px; padding-right:2px;">
{{--                                 <div class="caret text-center" style="width: 50%">
                                </div> --}}
                                {{ $item['namemodulo'] }}

                               </div>
                            @if (strpos($item['modruta'], 'A_') === false)
                            @foreach ($item['submodulos'] as $item2)
                            <?php
                            $searchString = ' ';
                            $replaceString = '-';
                            $refnmod = str_replace($searchString, $replaceString, $item2['namesubmodulo']);
                            ?>
                            <li>
                                <div class="caret categoria d-flex align-items-center Regular shadow
                                " data-bs-toggle="collapse"
                                    href="#collapse{{ $refnmod }}"
                                    style="background-Color: rgb(239, 237, 203 );border: 1px solid rgb(223, 222, 190);  min-height:40px; padding-left:2px; padding-bottom:10px; padding-right:2px;">

                                    {{ $item2['namesubmodulo'] }}

                                    </div>
                                <ul class=" lista  {{ array_search($route, array_column($item2['funciones'], 'ruta')) || array_column($item2['funciones'], 'ruta')[0] == $route ? '' : 'collapse' }}"
                                    id="collapse{{ $refnmod }}">
                                    @foreach ($item2['funciones'] as $item3)
                                        <li>
                                            <a type="button" href="{{ route($item3['ruta']) }}"
                                                class="nav-link btn text-left  }}"
                                                style="{{ $route == $item3['ruta'] ? 'background-color: #8cb6dd;' : 'background-color: #e6e6e6;' }}   
                                             "><label>{{ $item3['nombre'] }}</label></a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
     
                            @elseif (strpos($item['modruta'], 'A_') === true)
                            @endif
                        @endif
                    @endforeach
                </li>
            </ul>
        </div>
        <div class="">@yield('content_page')</div>
    </div>
@endsection