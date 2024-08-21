$(document).ready(function() {
    getData();

    $("buscarForm").keypress(function(e) {
        //Enter key
        if (e.which == 13) {
            return false;
        }
    });
});

function actualizarTabla(updateUR,updateUPP){
    var e = document.getElementById("filters_anio");
    var anio = e.value;
    var upp = '000';
    var ur = '00';

    var e = document.getElementById("filters_upp");
    if(typeof(e) != 'undefined' && e != null){
        var e = document.getElementById("filters_upp");
        var upp = e.value;
    }

    var e = document.getElementById("filters_ur");
    if(typeof(e) != 'undefined' && e != null){
        var e = document.getElementById("filters_ur");
        var ur = e.value;
    }
    
    //RECARGAR TABLA
    var opt = document.getElementById("buscarForm");
    var largo = opt.action.length - 11;
    var accion = opt.action.substring(0,largo)+anio+"/"+upp;
    if(updateUR) actualizarListaUR(upp);
    if(updateUPP) actualizarListaUPP(anio);

    accion += "/" + ur;
    opt.action = accion;
    getData();
}

function actualizarListaUPP(ejercicio){
    let select = document.getElementById("filters_upp");
    let cantidadOpt = select.options.length;

    for(i = cantidadOpt; i > 0; i--){
        select.remove(i);
    }

    let selectUR = document.getElementById("filters_ur");
    selectUR.options.length = 1;

    $.ajax({
        url: 'get-upp',
        data: {anio: ejercicio},
        type:'POST',
        dataType: 'json',
        success: function(response) {
            listaupp = response.listaUPP;
            listaupp.forEach((c) => {
                var upp = c.clv_upp + " - " + c.upp;
                var newOption = new Option(upp,c.clv_upp);
                select.add(newOption,undefined);
            });
        },
        error: function(response) {
            console.log('Error: ' + response);
        }
    });
}

function actualizarListaUR(clv_upp){
    let select = document.getElementById("filters_ur");
    let ejercicio = document.getElementById("filters_anio");
    let cantidadOpt = select.options.length;

    for(i = cantidadOpt; i > 0; i--){
        select.remove(i);
    }
    
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

let filtro_upp = '';
let filtro_ur = '';
let filtro_anio = document.getElementById('filters_anio');

if(document.getElementById('filters_upp') != null){
    filtro_upp = document.getElementById('filters_upp');
    filtro_ur = document.getElementById('filters_ur');
}

function activarFiltros(){
    if(filtro_upp != null){
        filtro_upp.disabled = false;
        filtro_ur.disabled = false;
    }
    filtro_anio.disabled = false;
    document.getElementById('btn_export_pdf').disabled = false;
    document.getElementById('btn_export_excel').disabled = false;
}

function desactivarFiltros(){
    if(filtro_upp != null){
        filtro_upp.disabled = true;
        filtro_ur.disabled = true;
    }
    filtro_anio.disabled = true;

    let export_pdf = document.getElementById('btn_export_pdf');
    let export_excel = document.getElementById('btn_export_excel');
    if(export_pdf){
        export_pdf.disabled = true;
        export_excel.disabled = true;
    }
}