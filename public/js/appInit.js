var _gen = {
    block: function () {
        const local = '127.0.0.1';
        if (window.location.hostname != local) {
            window.onload = function() {
            document.addEventListener("contextmenu", function(e) {
                e.preventDefault();
            }, false);
        }
        }
    },
    logOut: function () {
        Swal.fire({
            title: '¿Estás seguro de que quieres cerrar la sesión?',
            showDenyButton: true,
            confirmButtonText: 'Aceptar',
            denyButtonText: `Cancelar`,
          }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                $.ajax({
                    url: '/logout',
                    type: 'POST',
                    data: {
                      action: 'logout'
                    }, // Line A
                    success: function() {
                        console.log("Cerrando Session");
                        window.location.href = "/login";
                    }
                  });
            }
          })
        
    },
    essential: function () {
        function callbackThen(response) {
            // read HTTP status
            // read Promise object
            response.json().then(function(data) {
                console.log(data.action);
            });
        }
    
        function callbackCatch(error) {
            console.error('Error:', error)
        }
    
        var tiempo = parseInt("{{ $_ENV['SESSION_INACTIVITYTIME'] }}") * 60;
        var tiemporestante = new Date("{{Session::get('last_activity')}}").getTime();
        var reloj = setInterval(function() {
            var tiempoactual = new Date().getTime();
            var difFechas = tiempoactual - tiemporestante;
            var segundos = Math.floor(difFechas / 1000);
            var minutos = Math.floor(segundos / 60);
    
            // console.log(minutos);
    
            if (minutos >= 480) {
                clearInterval(reloj);
                var urlactual = "{{ Request::path() }}";
                if (urlactual !== 'login') {
                    Swal.fire({
                        title: 'Su sesión ha expirado',
                        text: '¿Desea iniciar sesión nuevamente?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, iniciar sesión',
                        cancelButtonText: 'No, cerrar sesión',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            _gen.logOut()
                        } else {
                            _gen.logOut()
                        }
                    });
                }
            }
        }, 1000);
    }

};