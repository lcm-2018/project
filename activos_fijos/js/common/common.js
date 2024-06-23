var setIdioma = {
    "decimal": "",
    "emptyTable": "No hay información",
    "info": "Mostrando _START_ - _END_ registros de _TOTAL_",
    "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
    "infoFiltered": "(Filtrado de _MAX_ en total)",
    "infoPostFix": "",
    "thousands": ",",
    "lengthMenu": "Ver _MENU_ Filas",
    "loadingRecords": "Cargando...",
    "processing": "Procesando...",
    "search": '<i class="fas fa-search fa-flip-horizontal" style="font-size:1.5rem; color:#2ECC71;"></i>',
    "zeroRecords": "No se encontraron registros",
    "paginate": {
        "first": "&#10096&#10096",
        "last": "&#10097&#10097",
        "next": "&#10097",
        "previous": "&#10096"
    }
};
var setdom;
if ($("#peReg").val() === '1') {
    setdom = "<'row'<'col-md-5'l><'bttn-plus-dt col-md-2'B><'col-md-5'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>";
} else {
    setdom = "<'row'<'col-md-6'l><'col-md-6'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>";
}

//CONFIRMAIÓN DE BORRAR REGISTROS
var confirmar_del = function(tipo, id) {
    var msg = "Esta seguro de esta Operación?";
    let btns = '<button class="btn btn-primary btn-sm" id="' + tipo + '" value=' + id + '>Aceptar</button><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>';
    $('#divModalConfDel').modal('show');
    $('#divMsgConfdel').html(msg);
    $('#divBtnsModalDel').html(btns);
    return false;
};

//CONFIRMACIÓN DE CERRAR, ANULAR PARA ORDENES DE INGRESO, ORDENES DE EGRESO, TRASLADOS, ETC.
var confirmar_proceso = function(tipo) {
    var msg = "Esta seguro de esta Operación?, <p style='color:red'>ESTE PROCESO ES IRREVERSIBLE</p>";
    let btns = '<button class="btn btn-primary btn-sm" id="' + tipo + '">Continuar</button><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</button>';
    $('#divModalConfDel').modal('show');
    $('#divMsgConfdel').html(msg);
    $('#divBtnsModalDel').html(btns);
    return false;
};

var reloadtable = function(nom, pag = 0) {
    $(document).ready(function() {
        var table = $('#' + nom).DataTable();
        table.page(pag).draw(false);
        //table.ajax.reload();
    });
};

//VERIFICA SI UN OBJETO ES VACIÓ Y LO RESALTA. SE PUEDE ENVIAR MESAJE COMO PARAMETRO PARA VISUALIZARSE
var verifica_vacio = function(objeto, msg = "") {
    var error = 0;
    if (objeto.val().trim() == "") {
        objeto.addClass('is-invalid');
        objeto.focus();
        error = 1;
        if (msg != "") {
            $('#divModalError').modal('show');
            $('#divMsgError').html(msg);
        }
    }
    return error;
};

//VERIFICA SI UN OBJETO ES VACIÓ Y RESALTA OTRO OBJETO RELACIONADO. SE PUEDE ENVIAR MESAJE COMO PARAMETRO PARA VISUALIZARSE
var verifica_vacio_2 = function(objeto1, objeto2, msg = "") {
    var error = 0;
    if (objeto1.val().trim() == "") {
        objeto2.addClass('is-invalid');
        objeto2.focus();
        error = 1;
        if (msg != "") {
            $('#divModalError').modal('show');
            $('#divMsgError').html(msg);
        }
    }
    return error;
};

//VERIFICA SI UN OBJETO TIENE UN VALOR MÍNIMO ESPECÍFICO
var verifica_valmin = function(objeto, val = 0, msg = "") {
    var error = 0;
    if (parseInt(objeto.val()) < val) {
        objeto.addClass('is-invalid');
        objeto.focus();
        error = 1;
        if (msg != "") {
            $('#divModalError').modal('show');
            $('#divMsgError').html(msg);
        }
    }
    return error;
};

var showError = function(error) {
    $('#divModalError').modal('show');
    $('#divMsgError').html(error);
}

$(function() {
    //clase con Dato numerico
    $('#divForms,#divModalReg').on("input", ".number", function() {
        var that = $(this);
        that.val(that.val().replace(/[^0-9]/g, ''));
        if (isNaN(that.val())) {
            e.preventDefault();
        }
    });

    //clase con Dato numerico entero >=0
    $('#divForms,#divModalReg').on("input", ".numberint", function() {
        var that = $(this);
        that.val(that.val().replace(/[^0-9]/g, ''));
        if (that.val().substring(0, 1).trim() == '0') {
            that.val('0');
        }
        if (isNaN(that.val())) {
            e.preventDefault();
        }
    });

    //clase con Dato numerico flotante
    $('#divForms,#divModalReg').on("input", ".numberfloat", function() {
        var that = $(this);
        that.val(that.val().replace(/[^0-9\.]/g, ''));
        if (that.val().substring(0, 1).trim() == '0' && that.val().substring(1, 2).trim() != '.') {
            that.val('0');
        }
        if (that.val().split('.').length >= 3) {
            that.val(that.val().substring(0, that.val().length - 1));
        }
        if (isNaN(hat.val())) {
            e.preventDefault();
        }
    });

    //clase con Dato letras, numeros, y -
    $('#divForms,#divModalReg').on("input", ".valcode", function() {
        var that = $(this);
        that.val(that.val().replace(/[^0-9a-zA-Z\-]/g, ''));
        if (isNaN(that.val())) {
            e.preventDefault();
        }
    });

    //Boton de Imprimir de formulario Impresión
    $('#divModalImp').on('click', '#btnImprimir', function() {
        function imprSelec() {
            var div = $('#areaImprimir').html();
            var ventimp = window.open(' ', '');
            ventimp.document.write('<!DOCTYPE html><html><head><title>Imprimir</title></head><body>');
            ventimp.document.write('<div>' + div + '</div>');
            ventimp.document.write('</body></html>');
            ventimp.print();
            ventimp.close();
        }
        $('#divModalForms .collapse').addClass('show');
        imprSelec();
    });

    //Boton de Excel de formulario Impresión
    $('#divModalImp').on('click', '#btnExcelEntrada', function() {
        let xls = ($('#areaImprimir').html());
        var encoded = window.btoa(xls);
        $('<form action="../common/reporte_excel.php" method="post"><input type="hidden" name="xls" value="' + encoded + '" /></form>').appendTo('body').submit();
    });
});