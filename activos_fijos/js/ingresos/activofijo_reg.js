(function($) {
    $(document).ready(function() {
        $('#tb_lista_activos_fijos').DataTable({
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
                url: 'listar_activosfijos_detalles.php',
                type: 'POST',
                dataType: 'json',
                data: function(data) {
                    data.id_ingreso_detalle = $('#id_ingreso_detalle').val();
                }
            },
            columns: [
                { 'data': 'placa' }, //Index=0
                { 'data': 'serial' },
                { 'data': 'marca' },
                { 'data': 'valor' },
                { 'data': 'tipo_activo' },
                { 'data': 'botones' }
            ],
            columnDefs: [
                { class: 'text-wrap', targets: [1, 2] },
                { orderable: false, targets: 5 }
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
        $('#tb_lista_activos_fijos').wrap('<div class="overflow"/>');
    });
})(jQuery);