<?php
    $titleDesc = __("messages.configuracion");
    //{{$titleDesc}}
?>
@extends('layouts.menulateral')

@section('content_page')

    <div>@yield('content_configuraciones')</div>
    
    @include('permisos.modulos_modal')

    @include('permisos.funciones_modal')

    @include('permisos.perfiles_modal')

    <script src="{{ asset('vendor/sweetalert/sweetalert.all.js') }}"></script>

    <script type="text/javascript">
        //inicializamos el data table

        @if (session('success'))
            $('#successModal').modal('show');
        @endif

        @if ($errors->any())
            $('#errorModal').modal('show');
        @endif

        $("form").keypress(function(e) {
            //Enter key
            if (e.which == 13) {
                return false;
            }
        });

        //menu lateral
        var toggler = document.getElementsByClassName("caret");
        var i;

        for (i = 0; i < toggler.length; i++) {
            toggler[i].addEventListener("click", function() {
                this.parentElement.querySelector(".nested").classList.toggle("active");
                this.classList.toggle("caret-down");
            });
        }

    </script>

@endsection