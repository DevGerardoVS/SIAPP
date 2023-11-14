var _gen = {
    block: function () {
        const local = '127.0.0.1';
        if (window.location.hostname != local) {
            window.onload = function () {
                document.addEventListener("contextmenu", function (e) {
                    e.preventDefault();
                }, false);
            }
        }
    },
    logOut: function () {
       
                $.ajax({
                    url: '/logout',
                    type: 'POST',
                    data: {
                        action: 'logout',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }, // Line A
                    success: function () {
                        console.log("Cerrando Session");
                        window.location.href = "/";
                    }
                });
          

    },
    logOutpestaña: function () {
       
        $.ajax({
            url: '/logout',
            type: 'POST',
            data: {
                action: 'logout',
                _token: $('meta[name="csrf-token"]').attr('content')
            }, // Line A
            success: function () {
                console.log("Cerrando Session");
                window.close();
                window.location.href = "/";
            }
        });
  

},
        regenerate: function () {
                    $.ajax({
                        url: '/logout',
                        type: 'POST',
                        data: {
                            action: 'regenerate',
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }, // Line A
                        success: function() {
                            console.log("regenerate Session");
                        }
                      });
            
        },


    essential: function (tiemporestante2,cargamasiva) {
       
            document.addEventListener("DOMContentLoaded", function (e) {
            
         
            }, false);
    
            // Tu código de SweetAlert2 aquí
      
        const currentPathname = window.location.pathname;
     

        var tiempo = parseInt("{{ $_ENV['SESSION_INACTIVITYTIME'] }}") * 60;
       
       



        if(currentPathname!="/login"){
        var reloj = setInterval(function () {
           
            // var tiemporestante2 ="{{Session::get('last_activity')}";
       
            var tiempoactual = new Date().getTime();
            var difFechas = tiempoactual - this.tiemporestante2;
            var segundos = Math.floor(difFechas / 1000);
            var minutos = Math.floor(segundos / 60);
            var horas = Math.floor(minutos / 60);

            if (minutos >= 480) {
                clearInterval(reloj);
                var urlactual = "{{ Request::path() }}";
                if (urlactual !== 'login') {
                   



                    Swal.fire({
                        title: 'Su sesión de '+horas+ ' horas ha expirado',
                        text: '¿Desea iniciar sesión nuevamente?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, iniciar sesión',
                        cancelButtonText: 'No, cerrar pestaña',
                        allowOutsideClick: false, // Evita que se cierre haciendo clic fuera del SweetAlert
                       
                       
                    }).then((result) => {
                        if (result.isConfirmed) {
                            _gen.logOut();
                        } else {
                            _gen.logOutpestaña();
                        }
                    });






                }
              
            }
        }, 1000);
    }}

};

var _notificaciones = {
    carga_masiva_alerts: function(estatus){
        
        if(estatus==1){
            $("#alerts_carga_masiva").on('click', function(e) {
                const tempLink = document.createElement('a');
                tempLink.href = `/borrar-sesion_excel`;
                tempLink.click();
                $('#alerts_carga_masiva').hide();
            });
        }else{
            $("#alerts_carga_masiva").on('click', function(e) {
                const tempLink = document.createElement('a');
                tempLink.href = `/calendarizacion/download-errors-excel`;
                tempLink.click();
                $('#alerts_carga_masiva').hide();
            });
        }

    },
}