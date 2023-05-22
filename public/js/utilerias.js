var otable;
var obj_updt = {};
var _g = {
    func: {},
    dao: {},
    dao_g: {},
    datelist: {},
    tblDatatable: {},
    currentDates: {},
    currentDatesNE: {},
    listas: {},
};
//Configuracion de Notificaciones
var _gen = {
    notificacion: function (titulo, content, style) {
        if (style == 1) {
            var _c = '#739E73',
                _i = 'fa fa-check',
                _n = '';
        }
        if (style == 2) {
            var _c = '#3276B1',
                _i = 'fa fa-bell swing animated',
                _n = '';
        }
        if (style == 3) {
            var _c = '#C79121',
                _i = 'fa fa-shield fadeInLeft animated',
                _n = '';
        }
        if (style == 4) {
            var _c = '#C46A69',
                _i = 'fa fa-warning shake animated',
                _n = '';
        }
        $.bigBox({
            title: titulo,
            content: content,
            color: _c,
            timeout: 8000,
            icon: _i,
            number: _n,
        });
    },

    notificacion_min: function (titulo, content, style) {
        if (style == 1) {
            var _c = '#739E73',
                _i = 'fa fa-check',
                _n = '';
        }
        if (style == 2) {
            var _c = '#3276B1',
                _i = 'fa fa-bell swing animated',
                _n = '';
        }
        if (style == 3) {
            var _c = '#C79121',
                _i = 'fa fa-shield fadeInLeft animated',
                _n = '';
        }
        if (style == 4) {
            var _c = '#C46A69',
                _i = 'fa fa-warning shake animated',
                _n = '';
        }
        $.smallBox({
            title: titulo,
            content: content,
            color: _c,
            iconSmall: _i + 'bounce animated',
            timeout: 6000,
        });
    },

    setTable: function (tabla, options) {
        var settings = $.extend(
            {
                columns: [],
                url: null,
                height: 500,
                pagination: 50,
                order: [],
                render: true,
                rowCB: null,
            },
            options
        );

        var responsiveHelper_datatable_tabletools = undefined;
        var breakpointDefinition = {
            tablet: 640,
            phone: 480,
        };

        tabla.DataTable({
            destroy: true,
            processing: true,
            ajax: settings.url,
            deferRender: settings.render,
            //	dom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-6 hidden-xs text-right'T>r>"+
            dom:
                "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-6 hidden-xs text-right'>r>" +
                't' +
                "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-sm-6 col-xs-12'p>>",
            tableTools: {
                aButtons: [
                    {
                        sExtends: 'xls',
                        sButtonText: 'Descargar a Excel',
                        sFileName: '*.xls',
                    },
                ],
                sSwfPath:
                    'assets/js/plugin/datatables/swf/copy_csv_xls_pdf.swf',
            },
            language: {
                info: 'Página _PAGE_ de _PAGES_',
                infoEmpty: 'No hay registros disponibles',
                zeroRecords: 'No hay registros disponibles',
                infoFiltered: '(filtrados de _MAX_ registros)',
                paginate: {
                    previous: 'Anterior',
                    next: 'Siguiente',
                },
                processing: 'Cargando...',
            },
            pageLength: settings.pagination,
            scrollX: true,
            order: settings.order,
            scrollY: settings.height + 'px',
            columns: settings.columns,
            preDrawCallback: function () {
                if (!responsiveHelper_datatable_tabletools) {
                    responsiveHelper_datatable_tabletools = new ResponsiveDatatablesHelper(
                        tabla,
                        breakpointDefinition
                    );
                }
            },
            rowCallback: function (nRow, data) {
                if (settings.rowCB != null) settings.rowCB(data, nRow);
                responsiveHelper_datatable_tabletools.createExpandIcon(nRow);
            },
            drawCallback: function (oSettings) {
                responsiveHelper_datatable_tabletools.respond();
                tabla.$('[data-toggle="popover"]').popover();
                tabla.$('[data-toggle="tooltip"]').tooltip();
            },
            initComplete: function () {
                otable = tabla.DataTable().columns.adjust().draw();
            },
        });
    },

    setTableB: function (
        tabla,
        columns,
        url,
        height,
        pagination,
        order,
        render,
        rowFunction
    ) {
        height = height || 500;
        pagination = pagination || 50;
        order = order || [];
        render = render || true;
        rowFunction = rowFunction || null;
        if (render == 'false') render = false;

        var responsiveHelper_datatable_tabletools = undefined;
        var breakpointDefinition = {
            tablet: 640,
            phone: 480,
        };

        tabla.DataTable({
            destroy: true,
            processing: true,
            ajax: url,
            deferRender: render,
            //dom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-6 hidden-xs text-right'T>r>"+
            dom:
                "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-6 hidden-xs text-right'>r>" +
                't' +
                "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-sm-6 col-xs-12'p>>",
            tableTools: {
                aButtons: [
                    {
                        sExtends: 'xls',
                        sButtonText: 'Descargar a Excel',
                        sFileName: '*.xls',
                    },
                ],
                sSwfPath:
                    'assets/js/plugin/datatables/swf/copy_csv_xls_pdf.swf',
            },
            language: {
                info: 'Página _PAGE_ de _PAGES_',
                infoEmpty: 'No hay registros disponibles',
                zeroRecords: 'No hay registros disponibles',
                infoFiltered: '(filtrados de _MAX_ registros)',
                paginate: {
                    previous: 'Anterior',
                    next: 'Siguiente',
                },
                processing: 'Cargando...',
            },
            pageLength: pagination,
            scrollX: true,
            order: order,
            scrollY: height + 'px',
            columns: columns,
            preDrawCallback: function () {
                if (!responsiveHelper_datatable_tabletools) {
                    responsiveHelper_datatable_tabletools = new ResponsiveDatatablesHelper(
                        tabla,
                        breakpointDefinition
                    );
                }
            },
            rowCallback: function (nRow, data) {
                if (rowFunction != null) rowFunction(data, nRow);
                responsiveHelper_datatable_tabletools.createExpandIcon(nRow);
            },
            drawCallback: function (oSettings) {
                responsiveHelper_datatable_tabletools.respond();
                tabla.$('[data-toggle="popover"]').popover();
                tabla.$('[data-toggle="tooltip"]').tooltip();
            },
            initComplete: function () {
                otable = tabla.DataTable().columns.adjust().draw();
            },
        });
    },

    setTableScroll: function (
        tabla,
        columnDefs,
        datelist,
        height,
        pagination,
        order
    ) {
        height = height || 500;
        pagination = pagination || 50;
        order = order || [];

        var responsiveHelper_datatable_tabletools = undefined;
        var breakpointDefinition = {
            tablet: 640,
            phone: 480,
        };
        if ($.fn.DataTable.isDataTable(tabla)) {
            tabla.DataTable().clear().rows.add(datelist);
        } else {
            tabla.DataTable({
                //	dom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-6 hidden-xs text-right'T>r>"+
                dom:
                    "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-6 hidden-xs text-right'>r>" +
                    't' +
                    "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-sm-6 col-xs-12'p>>",
                tableTools: {
                    aButtons: [
                        {
                            sExtends: 'xls',
                            sButtonText: 'Descargar a Excel',
                            sFileName: '*.xls',
                        },
                    ],
                    sSwfPath:
                        'assets/js/plugin/datatables/swf/copy_csv_xls_pdf.swf',
                },
                language: {
                    info: 'Página _PAGE_ de _PAGES_',
                    infoEmpty: 'No hay registros disponibles',
                    zeroRecords: 'No hay registros disponibles',
                    infoFiltered: '(filtrados de _MAX_ registros)',
                    paginate: {
                        previous: 'Anterior',
                        next: 'Siguiente',
                    },
                },
                pageLength: pagination,
                scrollX: true,
                order: order,
                scrollY: height + 'px',
                data: datelist,
                columnDefs: columnDefs,
                preDrawCallback: function () {
                    if (!responsiveHelper_datatable_tabletools) {
                        responsiveHelper_datatable_tabletools = new ResponsiveDatatablesHelper(
                            tabla,
                            breakpointDefinition
                        );
                    }
                },
                rowCallback: function (nRow) {
                    responsiveHelper_datatable_tabletools.createExpandIcon(
                        nRow
                    );
                },
                drawCallback: function (oSettings) {
                    responsiveHelper_datatable_tabletools.respond();
                    tabla.$('[data-toggle="popover"]').popover();
                    tabla.$('[data-toggle="tooltip"]').tooltip();
                },
                initComplete: function () {
                    otable = tabla.DataTable().columns.adjust().draw();
                },
            });
        }
        otable = tabla.DataTable().columns.adjust().draw();
        otable.$('[data-toggle="popover"]').popover();
    },

    setTableScrollEspecial: function (
        tabla,
        columnDefs,
        datelist,
        height,
        pagination,
        order
    ) {
        height = height || 200;
        pagination = pagination || 15;
        order = order || [];

        var responsiveHelper_datatable_tabletools = undefined;
        var breakpointDefinition = {
            tablet: 640,
            phone: 480,
        };
        if ($.fn.DataTable.isDataTable(tabla)) {
            tabla.DataTable().clear().rows.add(datelist);
        } else {
            tabla.DataTable({
                //  dom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-6 hidden-xs text-right'T>r>"+
                dom:
                    "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-6 hidden-xs text-right'>r>" +
                    't' +
                    "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-sm-6 col-xs-12'p>>",
                tableTools: {
                    aButtons: [
                        {
                            sExtends: 'xls',
                            sButtonText: 'Descargar a Excel',
                            sFileName: '*.xls',
                        },
                    ],
                    sSwfPath:
                        'assets/js/plugin/datatables/swf/copy_csv_xls_pdf.swf',
                },
                language: {
                    info: 'Página _PAGE_ de _PAGES_',
                    infoEmpty: 'No hay registros disponibles',
                    zeroRecords: 'No hay registros disponibles',
                    infoFiltered: '(filtrados de _MAX_ registros)',
                    paginate: {
                        previous: 'Anterior',
                        next: 'Siguiente',
                    },
                },
                pageLength: pagination,
                scrollX: true,
                order: order,
                scrollY: height + 'px',
                data: datelist,
                columnDefs: columnDefs,
                preDrawCallback: function () {
                    if (!responsiveHelper_datatable_tabletools) {
                        responsiveHelper_datatable_tabletools = new ResponsiveDatatablesHelper(
                            tabla,
                            breakpointDefinition
                        );
                    }
                },
                rowCallback: function (nRow) {
                    responsiveHelper_datatable_tabletools.createExpandIcon(
                        nRow
                    );
                },
                drawCallback: function (oSettings) {
                    responsiveHelper_datatable_tabletools.respond();
                    tabla.$('[data-toggle="popover"]').popover();
                    tabla.$('[data-toggle="tooltip"]').tooltip();
                },
                initComplete: function () {
                    otable = tabla.DataTable().columns.adjust().draw();
                },
            });
        }
        otable = tabla.DataTable().columns.adjust().draw();
        otable.$('[data-toggle="popover"]').popover();
    },

    setTableScrollEspecial2: function (
        tabla,
        columnDefs,
        datelist,
        height,
        pagination,
        order
    ) {
        height = height || 600;
        pagination = pagination || 700;
        order = order || [];

        var responsiveHelper_datatable_tabletools = undefined;
        var breakpointDefinition = {
            tablet: 640,
            phone: 480,
        };
        if ($.fn.DataTable.isDataTable(tabla)) {
            tabla.DataTable().clear().rows.add(datelist);
        } else {
            tabla.DataTable({
                //  dom: "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-6 hidden-xs text-right'T>r>"+
                dom:
                    "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-6 hidden-xs text-right'>r>" +
                    't' +
                    "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-sm-6 col-xs-12'p>>",
                tableTools: {
                    aButtons: [
                        {
                            sExtends: 'xls',
                            sButtonText: 'Descargar a Excel',
                            sFileName: '*.xls',
                        },
                    ],
                    sSwfPath:
                        'assets/js/plugin/datatables/swf/copy_csv_xls_pdf.swf',
                },
                language: {
                    info: 'Página _PAGE_ de _PAGES_',
                    infoEmpty: 'No hay registros disponibles',
                    zeroRecords: 'No hay registros disponibles',
                    infoFiltered: '(filtrados de _MAX_ registros)',
                    paginate: {
                        previous: 'Anterior',
                        next: 'Siguiente',
                    },
                },
                pageLength: pagination,
                scrollX: true,
                order: order,
                scrollY: height + 'px',
                data: datelist,
                columnDefs: columnDefs,
                preDrawCallback: function () {
                    if (!responsiveHelper_datatable_tabletools) {
                        responsiveHelper_datatable_tabletools = new ResponsiveDatatablesHelper(
                            tabla,
                            breakpointDefinition
                        );
                    }
                },
                rowCallback: function (nRow) {
                    responsiveHelper_datatable_tabletools.createExpandIcon(
                        nRow
                    );
                },
                drawCallback: function (oSettings) {
                    responsiveHelper_datatable_tabletools.respond();
                    tabla.$('[data-toggle="popover"]').popover();
                    tabla.$('[data-toggle="tooltip"]').tooltip();
                },
                initComplete: function () {
                    otable = tabla.DataTable().columns.adjust().draw();
                },
            });
        }
        otable = tabla.DataTable().columns.adjust().draw();
        otable.$('[data-toggle="popover"]').popover();
    },

    loadSelect: function (elem, data) {
        elem.empty();
        elem.append(new Option('Seleccione', -1));
        $.each(data, function (i) {
            elem.append(new Option(this.name, this.id));
        });
    },

    loadSelect2: function (elem, data, deft) {
        elem.empty();
        if (deft == null) elem.append(new Option('Seleccione', -1));
        else if (deft != 'false') {
            $.each(deft, function (i) {
                elem.append(new Option(this.name, this.id));
            });
        }
        $.each(data, function (i) {
            elem.append(new Option(this.name, this.id));
        });
        elem.select2();
    },

    select: function (select, options) {
        var settings = $.extend(
            {
                default: [{ id: 'false', name: '--- Seleccione un valor ---' }],
                data: [],
                selected: 'false',
            },
            options
        );

        select.empty();

        $.each(settings.default, function (i) {
            select.append(
                new Option(settings.default[i].name, settings.default[i].id)
            );
        });

        $.each(settings.data, function (i) {
            select.append(
                new Option(settings.data[i].name, settings.data[i].id)
            );
        });

        select.select2();

        if (settings.selected != 'false' && settings.selected != undefined)
            select.select2('val', settings.selected);
    },

    validation: function (_form, _rules, _messages) {

        _form.validate({
            errorElement: 'span',
            errorClass: 'has-error',
            focusInvalid: true,
            rules: _rules,
            messages: _messages,
            success: function (e) {
                $('.form-group').removeClass('has-error');
                $(e).remove();
            },
            highlight: function (e, errorClass) {
                $(e.parentNode).addClass(errorClass);
            },
            unhighlight: function (e, errorClass) {
                $(e.parentNode).removeClass(errorClass);
            },
        });
    },

    validate: function (form, options) {
        var settings = $.extend(
            {
                rules: {},
                messages: {},
            },
            options
        );

        form.validate({
            errorElement: 'span',
            errorClass: 'has-error',
            focusInvalid: true,
            rules: settings.rules,
            messages: settings.messages,
            success: function (e) {
                $('.form-group').removeClass('has-error');
                $(e).remove();
            },
            highlight: function (e, errorClass) {
                $(e.parentNode).addClass(errorClass);
            },
            unhighlight: function (e, errorClass) {
                $(e.parentNode).removeClass(errorClass);
            },
        });
    },

    panel: function (div) {
        $.ajax({
            type: 'GET',
            url: '/sistemas/panel',
            dataType: 'json',
        }).done(function (data) {
            content = '';
            $.each(data, function (i) {
                content += '<div class="superbox-list">';
                if (data[i].estatus == 1)
                    target =
                        "<a href='/" +
                        data[i].ruta +
                        "' class='btn btn-primary btn-sm' id='open-link'>Abrir Sistema</a>";
                else if (data[i].estatus == 2) {
                    target =
                        "<div class='col-md-10 col-md-offset-1 alert alert-warning'>No cuenta con los permisos suficientes para acceder a este sistema</div>";
                } else {
                    target =
                        "<div class='col-md-10 col-md-offset-1 alert alert-danger'>No se ha adquirido este servicio</div>";
                }
                content +=
                    '<img src="/assets/img/' +
                    data[i].logo +
                    '" data-img="/assets/img/' +
                    data[i].logo_min +
                    '" alt="' +
                    data[i].descripcion +
                    '" title="' +
                    data[i].nombre_sistema +
                    '" class="superbox-img" target="' +
                    target +
                    '">';
                content += '</div>';
            });
            div.append(content);
            div.SuperBox();
        });
    },

    getUsrModal: function (id) {
        _gen.getUserData(id);
        $('#userModal').modal('show');
    },

    getUserData: function (id) {
        $.ajax({
            url: '/usuarios/user/' + id,
            type: 'GET',
            dataType: 'json',
        }).done(function (data) {
            $('#user-data #username').val(data[0].username);
            $('#user-data #nombre_completo').val(
                data[0].nombre +
                ' ' +
                data[0].primer_apellido +
                ' ' +
                data[0].segundo_apellido
            );
            if (data[0].cve_upp != 00) {
                $('#user-data #cve_upp').val(
                    data[0].cve_upp + ' ' + data[0].nombre_upp
                );
                $('#user-data #cve_ur').val(
                    data[0].cve_ur + ' ' + data[0].nombre_ur
                );
            } else {
                $('#user-data #cve_upp').parent().parent().parent().hide();
                $('#user-data #cve_ur').parent().parent().parent().hide();
            }
            $('#user-data #email').val(data[0].email);
        });
    },

    alphaCheck: function (e) {
        var numeric = e.which > 47 && e.which < 58;
        var alpha = e.which > 64 && e.which < 91;
        var bkspace = e.which == 8;
        var tab = e.which == 0;

        return (
            numeric == false &&
            alpha == false &&
            bkspace == false &&
            tab == false
        );
    },

    floatCheck: function (e) {
        var numeric = e.which > 47 && e.which < 58;
        var dot = e.which == 46;
        var bkspace = e.which == 8;
        var tab = e.which == 0;

        return (
            numeric == false && dot == false && bkspace == false && tab == false
        );
    },

    numCheck: function (e) {
        var numeric = e.which > 47 && e.which < 58;
        var bkspace = e.which == 8;
        var tab = e.which == 0;

        return numeric == false && bkspace == false && tab == false;
    },

    numberCheck: function (e) {
        var numeric = e.which >= 46 && e.which < 58;
        var bkspace = e.which == 8;
        var tab = e.which == 0;

        return numeric == false && bkspace == false && tab == false;
    },

    dropMenu: function (items, type, title) {
        type = type || 'success';
        title = title || 'Administrar';

        menu = "<div class='btn-group'>";
        menu +=
            "<a href='javascript:void(0);' class='btn btn-" +
            type +
            "'>" +
            title +
            '</a>';
        menu +=
            "<a href='javascript:void(0);' data-toggle='dropdown' class='btn btn-" +
            type +
            " dropdown-toggle'><span class='caret'></span></a>";
        menu += "<ul class='dropdown-menu'>";

        li = '';
        $.each(items, function (i) {
            li += '<li>';
            if (items[i].type == 'link') {
                target = items[i].target || false;
                if (target != false)
                    li +=
                        "<a target='" +
                        target +
                        "' href='" +
                        items[i].action +
                        "'>" +
                        items[i].title +
                        '</a>';
                else
                    li +=
                        "<a href='" +
                        items[i].action +
                        "'>" +
                        items[i].title +
                        '</a>';
            } else {
                li +=
                    "<a onclick='" +
                    items[i].action +
                    "'>" +
                    items[i].title +
                    '</a>';
            }
            li += '</li>';
        });
        menu += li;
        menu += '</ul></div>';

        return menu;
    },
};
//Funcion para Serializar Objeto
$.fn.serializeObject = function () {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function () {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};
//Variables Globales
var _globals = {
    max_size: 100,

    map_zoom: 15,

    coordenadas: { lat: 19.546, lng: -101.4384 },

    map: null,

    marker: null,

    field_lat: 'mp_latitud',

    field_lng: 'mp_longitud',

    field_search_map: 'pac-input',
};

function DisableBackButton() {
    window.history.forward();
}
DisableBackButton();
window.onload = DisableBackButton;
window.onpageshow = function (evt) {
    if (evt.persisted) DisableBackButton();
};
window.onunload = function () {
    void 0;
};
//Formato Texto
$.formatText = function (input) {
    var text = input.val();
    var text_formated = text.toLowerCase();
    text_formated =
        text_formated.charAt(0).toUpperCase() + text_formated.slice(1);
    input.val(text_formated);
};
//Formato Moneda
$.formatCurrency = function (amount, format) {
    var aumont2 = amount;
    amount = amount || 0;
    amount = parseFloat(amount.toString().replace(/[^0-9\.]+/g, ''));
    var signo;
    signo = Math.sign(aumont2);

    if (format) {
        if (signo == "-1") {
            return '$- ' + amount.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
        } else {
            return '$ ' + amount.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
        }
    }
    else return amount.toFixed(2);
};
//Funcion on Blur
$(document).on('blur', '.form-currency', function () {
    current = 0;
    if ($(this).val() != '') current = $(this).val();

    currency = $.formatCurrency(current, true);
    $(this).val(currency);
});
//Funcion de Validacion de Calendario
$(document).on('blur', '.cal-control', function () {
    var total = 0;
    var target = $(this).attr('target');

    $('.cal-control').each(function () {
        if (!$(this).val() == '') {
            total += $.formatCurrency($(this).val(), false);
        }
    });

    $(target).val($.formatCurrency(total, true));
});
//Configuracion de Modal
$.widget(
    'ui.dialog',
    $.extend({}, $.ui?.dialog.prototype, {
        _title: function (title) {
            if (!this.options.title) {
                title.html('&#160;');
            } else {
                title.html(this.options.title);
            }
        },
    })
);
//Validacion Campo Obligatorio
$.validator.addMethod(
    'valueNotEquals',
    function (value, element, arg) {
        return arg != value;
    },
    'El campo es obligatorio'
);
//Validacion de solo Letras
$.validator.addMethod(
    'lettersonly',
    function (value, element) {
        return this.optional(element) || /^[a-z\s]*$/i.test(value);
    },
    'Sólamente letras sin acentos porfavor'
);
//Validacion Alfanumerica
$.validator.addMethod(
    'alphanumeric',
    function (value, element) {
        return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
    },
    'Introduzca sólamente caracteres alfanuméricos'
);
//Configuracion de AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content'),
    },
    async: false,
    success: function (data) { },
    error: function (error, status, err) {
        if (error.status == 401)
            _gen.notificacion_min(
                'Movimiento no autorizado',
                'No cuenta con los permisos suficientes para realizar esta acción',
                4
            );
        else if (error.status == 500)
            _gen.notificacion_min(
                'Error del servidor',
                'Ha ocurrido un error interno. Intentelo más tarde o contacte a soporte técnico',
                4
            );
    },
});
//Deshabilitacion de Tecla Enter
$(document).on('keyup keypress', 'form', function (e) {
    if (e.which == 13) {
        e.preventDefault();
        return false;
    }
});
//Funcion Documento on Change
$(document).on('change', "[type='file']:not('.pdf-ignore')", function () {
    file = $(this).prop('files')[0];
    button = $('#' + $(this).attr('bt-target'));
    var doc = $(this).val();
    var ext = doc.substring(doc.lastIndexOf('.'));
    //console.log(ext);
    if (ext != '.xlsx') {
        if (file.size > _globals.max_size * 1024 * 1024) {
            /*if(ext != ".xml" && ext != ".pdf"){

        }*/
            _gen.notificacion_min(
                'Error',
                'El archivo cargado pesa más de ' +
                _globals.max_size +
                'mb, seleccione un archivo más ligero',
                4
            );
            button.attr('disabled', true);
        } else {
            button.attr('disabled', false);
        }
    }

});
//Funcion on Click Evento Submit
$(document).on('click', '[type=submit]', function (e) {
    e.preventDefault();
    form = $(this).parents('form:first');
    if (form.valid() && $(this).attr('disabled', false)) {
        $(this).attr('disabled', true);
        form.submit();
    }
    return false;
});
//Funcion Selector Tooltip
$('body').tooltip({
    selector: '[data-toggle="tooltip"]',
});
//Modal para Actualizacion de Usuario
$(document).on('click', '#user', function (e) {
    $.ajax({
        type: 'GET',
        url: '/usuarios/profile',
        dataType: 'json',
    }).done(function (user) {
        user = user[0];

        upp = user.upp == null ? 'No Asignado' : user.upp;
        ur = user.ur == null ? 'No Asignado' : user.ur;

        lc_modal =
            '<div aria-hidden="false" aria-labelledby="myModalLabel" role="dialog" id="profile" class="modal fade in" style="display:none">';
        lc_modal += '<div class="modal-dialog">';
        lc_modal += '<div class="modal-content">';
        lc_modal += '<div class="modal-header">';
        lc_modal +=
            '<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>';
        lc_modal +=
            '<h4 id="myModalLabel" class="modal-title">Perfil de Usuario</h4>';
        lc_modal += '</div><div class="modal-body"><div id="profile-info">';
        lc_modal += '<div class="row" id="user-data">';
        lc_modal +=
            '<div class="col-md-3"><img src="assets/img/logo-o.png"></div>';
        lc_modal += '<div class="col-md-9"><h1>' + user.fullName + '</h1>';
        lc_modal +=
            '<h6><i class="fa fa-envelope"></i> ' +
            user.email +
            '</h6></div></div>';
        lc_modal += '<div style="clear:both"></div><div class="row">';
        lc_modal += '<div class="col-md-2 text-right"><h3>UPP</h3></div>';
        lc_modal += '<div class="col-md-10"><h2>' + upp + '</h2></div></div>';
        lc_modal += '<div style="clear:both"></div><div class="row">';
        lc_modal += '<div class="col-md-2 text-right"><h3>UR</h3></div>';
        lc_modal += '<div class="col-md-10"><h2>' + ur + '</h2></div></div>';
        lc_modal += '<div style="clear:both"></div><div class="row">';
        lc_modal +=
            '<div class="col-md-4 col-md-offset-4"><button class="btn btn-primary btn-labeled" id="updatePasswordTrigger" type="button">';
        lc_modal +=
            '<span class="btn-label"><i class="glyphicon glyphicon-lock"></i></span>Cambiar Contraseña</button></div></div>';
        lc_modal += '</div><div id="password-update" style="display : none;">';
        lc_modal += '<div class="row form-group"><h3>Cambio de Contraseña</h3>';
        lc_modal += '<div class="col-md-4 text-right">Contraseña Actual</div>';
        lc_modal +=
            '<div class="col-md-8"><input type="password" id="old_password" class="form-control"></div>';
        lc_modal += '</div><div class="row form-group">';
        lc_modal += '<div class="col-md-4 text-right">Contraseña Nueva</div>';
        lc_modal +=
            '<div class="col-md-8"><input type="password" id="new_password" class="form-control"></div>';
        lc_modal += '</div><div class="row form-group">';
        lc_modal +=
            '<div class="col-md-4 text-right">Confirmar Contraseña</div>';
        lc_modal +=
            '<div class="col-md-8"><input type="password" id="password_confirmation" class="form-control"></div>';
        lc_modal += '</div><div class="row form-group">';
        lc_modal +=
            '<div class="col-md-6 text-right"><button class="btn btn-success" id="btnUpdatePassword">Guardar</button></div>';
        lc_modal +=
            '<div class="col-md-6"><button class="btn btn-danger" id="btnProfileTrigger">Cancelar</button></div>';
        lc_modal += '</div></div>';
        lc_modal += '</div></div></div></div>';

        $('body').append(lc_modal);
        $('#profile').modal('show');
        $('#profile-info').slideDown(0);
        $('#password-update').slideUp(0);
    });
});

//Validaciones por Default
$.validator.setDefaults({
    errorElement: 'span',
    errorClass: 'help-block',
    highlight: function (element, errorClass, validClass) {
        $(element).closest('.form-group').addClass('has-error');
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).closest('.form-group').removeClass('has-error');
    },
    errorPlacement: function (error, element) {
        if (
            element.parent('.input-group').length ||
            element.prop('type') === 'checkbox' ||
            element.prop('type') === 'radio'
        ) {
            error.insertAfter(element.parent());
        } else {
            error.insertAfter(element);
        }
    },
});
//Validador de Navegador
if ('localStorage' in window && window['localStorage'] !== null) {
    var storage = localStorage;
} else {
    alert(
        'Favor de utilizar un navegador web moderno, ya que es necesario para el funcionamiento correcto de la aplicacion'
    );
}
//Funcion para Actualizacion de Password
$(document).on('click', '#updatePasswordTrigger', function (e) {
    $('#profile-info').slideUp(200);
    $('#password-update').slideDown(200);
});
//Funcion para Ejecucion de Perfil
$(document).on('click', '#btnProfileTrigger', function (e) {
    $('#profile-info').slideDown(200);
    $('#password-update').slideUp(200);
});
//Funcion de Actualizacion de Password
$(document).on('click', '#btnUpdatePassword', function (e) {
    lc_old_password = $('#old_password').val();
    lc_new_password = $('#new_password').val();
    lc_password_confirmation = $('#password_confirmation').val();

    if (lc_new_password != lc_password_confirmation) {
        $('#new_password').parent().addClass('has-error');
        $('#password_confirmation').parent().addClass('has-error');
        _gen.notificacion_min('Error', 'Las contraseñas no coinciden', 4);
    } else {
        $('#new_password').parent().removeClass('has-error');
        $('#password_confirmation').parent().removeClass('has-error');
        $.ajax({
            type: 'POST',
            url: '/usuarios/updatepwd',
            data: {
                old_password: lc_old_password,
                new_password: lc_new_password,
            },
        }).done(function (response) {
            if (response == 'success') {
                $('#profile').modal('hide');
                _gen.notificacion_min(
                    'Contraseña Actualizada',
                    'La contraseña se ha acutalizado correctamente. En su próximo inicio de sesión se verá reflejado',
                    1
                );
            } else {
                _gen.notificacion_min(
                    'Error',
                    'La contraseña actual no es correcta. Intentelo de nuevo',
                    4
                );
            }
        });
    }
});
//Funcion para deteccion de Accesible
function hostReachable() {
    // Handle IE and more capable browsers
    var xhr = new (window.ActiveXObject || XMLHttpRequest)('Microsoft.XMLHTTP');
    var status;

    // Open new request as a HEAD to the root hostname with a random param to bust the cache
    xhr.open('HEAD', '/assets/js/plugin/jquery/1.8/jquery.min', false);

    // Issue request and handle response
    try {
        xhr.send();
        return (xhr.status >= 200 && xhr.status < 300) || xhr.status === 304;
    } catch (error) {
        return false;
    }
}

