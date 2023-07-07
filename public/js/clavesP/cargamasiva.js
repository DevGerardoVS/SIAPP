
    $(".container").on('click', '#button_modal_carga', function () {
        $('#ModalCargaMasiva').modal('show');
    })

    
    $(".container").on('click', '#button_modal_carga_adm', function () {
        $('#Modal_admin').modal('show');
    })

/*     $.ajax({
        url:"{{route('SaveErrors')}}",
        type: "POST",
        data: {
            "_token": "{{ csrf_token() }}",
            "failures":  {!! $errors !!},
        },

    }).then((response) => {
     var myblob = new Blob([response], {
     type: 'text/plain'
    });
    console.log(typeof myblob);
 });          
 */