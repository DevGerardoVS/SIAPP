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
                
               
                if (cargamasiva==2){

                    // var serializada ='hola';
                    // Swal.fire({
                    //     title: 'Su sesión de '+serializada+ ' horas ha expirado',
                    //     text: '¿Desea iniciar sesión nuevamente?',
                    //     icon: 'warning',
                    //     showCancelButton: true,
                    //     confirmButtonColor: '#3085d6',
                    //     cancelButtonColor: '#d33',
                    //     confirmButtonText: 'Sí, iniciar sesión',
                    //     cancelButtonText: 'No, cerrar pestaña',
                    //     allowOutsideClick: false, // Evita que se cierre haciendo clic fuera del SweetAlert
                       
                       
                    // }).then((result) => {
                    //     if (result.isConfirmed) {
                    //         _gen.logOut();
                    //     } else {
                    //         _gen.logOutpestaña();
                    //     }
                    // });
                    
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al importar la carga masiva',
                        text: 'fallo la carga',
                        confirmButtonText: "Aceptar",
                        showCancelButton: false,
                        cancelButtonText: "Cancelar",
                        allowOutsideClick: false, 
                         footer: '<a href="/calendarizacion/download-errors-excel' + '" target="_blank">Descargar Errores</a>',
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            cargamasiva=3;
                            // El usuario hizo clic en "Aceptar", puedes realizar alguna acción si es necesario
                            location.href = "/home";
                        }
                    });
                    
                    }
                    if (cargamasiva==1){

                        // var serializada ='hola';
                        // Swal.fire({
                        //     title: 'Su sesión de '+serializada+ ' horas ha expirado',
                        //     text: '¿Desea iniciar sesión nuevamente?',
                        //     icon: 'warning',
                        //     showCancelButton: true,
                        //     confirmButtonColor: '#3085d6',
                        //     cancelButtonColor: '#d33',
                        //     confirmButtonText: 'Sí, iniciar sesión',
                        //     cancelButtonText: 'No, cerrar pestaña',
                        //     allowOutsideClick: false, // Evita que se cierre haciendo clic fuera del SweetAlert
                           
                           
                        // }).then((result) => {
                        //     if (result.isConfirmed) {
                        //         _gen.logOut();
                        //     } else {
                        //         _gen.logOutpestaña();
                        //     }
                        // });
                        
                 
                      
                      
                      
                      
                      
                      
                      
                      
                        Swal.fire({
                            icon: 'success',
                            title: 'Exito',
                            text: 'El excel se cargo correctamente',
                            confirmButtonText: "Aceptar",
                            showCancelButton: false,
                            cancelButtonText: "Cancelar",
                           }).then(function(result) {
                            if (result.isConfirmed) {
                                cargamasiva=3;
                                // var token = document.querySelector('meta[name="csrf-token"]').content;

                                // var xhr = new XMLHttpRequest();
                                // xhr.open('GET', '/borrar-sesion_excel', true);
                                // xhr.setRequestHeader('X-CSRF-TOKEN', token);
                                // xhr.onload = function() {
                                //     if (xhr.status === 200) {
                                //         // Las variables de sesión se eliminaron con éxito
                                //         location.href = "/home";
                                //     }
                                // };
                        
                                // xhr.send();


                                $.ajax({
                                    url:'/borrar-sesion_excel',
                                    type: "GET",
                                    data: {},
                                    dataType: 'json',
                                    beforeSend: function() {
                                        let timerInterval
                                        Swal.fire({
                                            title: '{{__("messages.msg_guardando_datos")}}',
                                            html: ' <b></b>',
                                            allowOutsideClick: false,
                                            timer: 2000000,
                                            timerProgressBar: true,
                                            didOpen: () => {
                                                Swal.showLoading()
                                            }
                                        });
                                    },
                                    success:function(response){
                                        console.log(response);
                                        
                                        Swal.close();
                                        
                                    },
                                    error: function(response) {
                                        console.log(response);
                                    }
                                });

                            }
                        });
                        
                        }

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