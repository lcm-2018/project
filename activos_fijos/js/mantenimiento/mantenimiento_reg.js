(function($) {
    $(document).ready(function() {
        $('#tb_mantenimientos_detalles').DataTable({
            dom: setdom,
            buttons: [{
                action: function(e, dt, node, config) {
                    $.post("../common/buscar_articulos_act_frm.php", function(he) {
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
                url: 'listar_mantenimientos_detalles.php',
                type: 'POST',
                dataType: 'json',
                data: function(data) {
                    data.id_mantenimiento = $('#id_mantenimiento').val();
                }
            },
            columns: [
                { 'data': 'id_detalle_mantenimiento' }, //Index=0
                { 'data': 'articulo' },
                { 'data': 'placa' },
                { 'data': 'observacion_mantenimiento' },
                { 'data': 'estado' },
                { 'data': 'estado_fin' },
                { 'data': 'observacio_fin_mantenimiento' },
                { 'data': 'botones' }
            ],
            columnDefs: [
                { class: 'text-wrap', targets: [1, 2] },
                { orderable: false, targets: 7 }
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
        $('#tb_mantenimientos_detalles').wrap('<div class="overflow"/>');
    });
})(jQuery);