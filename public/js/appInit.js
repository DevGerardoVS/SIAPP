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
        
    }
};