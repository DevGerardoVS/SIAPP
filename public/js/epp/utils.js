$(document).ready(function() {
    getData();

    $("buscarForm").keypress(function(e) {
        //Enter key
        if (e.which == 13) {
            return false;
        }
    });
});

function actualizarTabla(updateUR){
    var e = document.getElementById("filters_anio");
    var anio = e.value;
    console.log(anio);
    var upp = '000';
    var ur = '00';

    var e = document.getElementById("filters_upp");
    console.log(e);
    if(typeof(e) != 'undefined' && e != null){
        console.log('Null');
        var e = document.getElementById("filters_upp");
        var upp = e.value;

        var e = document.getElementById("filters_ur");
        var ur = e.value;
    }
    
    //RECARGAR TABLA
    var opt = document.getElementById("buscarForm");
    var largo = opt.action.length - 11;
    var accion = opt.action.substring(0,largo)+anio+"/"+upp;
    if(updateUR) actualizarListaUR(upp);

    accion += "/" + ur;
    opt.action = accion;
    //console.log(accion);
    getData();
}

function actualizarListaUR(clv_upp,ruta){
    let select = document.getElementById("filters_ur");
    let ejercicio = document.getElementById("filters_anio");
    select.options.length = 1;
    
    $.ajax({
        url: "get-ur",
        data: {upp: clv_upp, anio: ejercicio.value},
        type:'POST',
        dataType: 'json',
        success: function(response) {
            listaur = response.listaUR;
            listaur.forEach((c) => {
                var ur = c.clv_ur + " - " + c.ur;
                var newOption = new Option(ur,c.clv_ur);
                select.add(newOption,undefined);
            });
        },
        error: function(response) {
            console.log('Error: ' + response);
        }
    });
}

function exportPdf(){
    var e = document.getElementById("filters_anio");
    var anio = e.value;

    console.log('entro en la funcion');
    _url = "/epp-exportPdf/"+anio;
    window.open(_url, '_blank');
}