(function($) {
    $(document).ready(function() {
        $('#tb_ingresos_detalles').DataTable({
            dom: setdom,
            buttons: [{
                action: function(e, dt, node, config) {
                    $.post("../common/buscar_articulos_acf.php", { id_sede: $('#id_txt_sede').val(), id_bodega: $('#id_txt_nom_bod').val() }, function(he) {
                        $('#divTamModalBus').removeClass('modal-lg');
                        $('#divTamModalBus').removeClass('modal-sm');
                        $('#divTamModalBus').addClass('modal-xl');
                        $('#divModalBus').modal('show');
                        $("#divFormsBus").html(he);
                    });
                }
            }],
            language: setIdioma,
            processing: true,
            serverSide: true,
            ajax: {
                url: 'listar_ingresos_detalles.php',
                type: 'POST',
                dataType: 'json',
                data: function(data) {
                    data.id_ingreso = $('#id_ingreso').val();
                }
            },
            columns: [
                { 'data': 'id_ing_detalle' }, //Index=0
                { 'data': 'cod_medicamento' },
                { 'data': 'nom_medicamento' },
                { 'data': 'cantidad' },
                { 'data': 'valor_sin_iva' },
                { 'data': 'iva' },
                { 'data': 'valor' },
                { 'data': 'val_total' },
                { 'data': 'observacion' },
                { 'data': 'botones' }
            ],
            columnDefs: [
                { class: 'text-wrap', targets: [2, 8] },
                { orderable: false, targets: 9 }
            ],
            order: [
                [0, "desc"]
            ],
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, 'TODO'],
            ],
        });
        $('.bttn-plus-dt span').html('<span class="icon-dt fas fa-plus-circle fa-lg"></span>');
        $('#tb_ingresos_detalles').wrap('<div class="overflow"/>');
    });
})(jQuery);