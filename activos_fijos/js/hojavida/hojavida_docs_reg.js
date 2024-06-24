(function($) {
    $(document).ready(function() {
        $('#tb_lista_documentos_acf').DataTable({
            dom: setdom,
            buttons: [{
                action: function(e, dt, node, config) {
                    $.post("frm_reg_activofijo_detalle.php", {
                        id_ing_detalle: $('#id_hv').val(),
                        val_unitario: $('#txt_vrunit_med').val()
                    }, function(he) {
                        $('#divTamModalReg').removeClass('modal-xl');
                        $('#divTamModalReg').removeClass('modal-sm');
                        $('#divTamModalReg').addClass('modal-lg');
                        $('#divModalReg').modal('show');
                        $("#divFormsReg").html(he);
                    });
                }
            }],
            language: setIdioma,
            processing: true,
            serverSide: true,
            ajax: {
                url: 'listar_activosfijos_documentos.php',
                type: 'POST',
                dataType: 'json',
                data: function(data) {
                    data.id_hv = $('#id_hv').val();
                }
            },
            columns: [
                { 'data': 'id' }, //Index=0
                { 'data': 'placa' },
                { 'data': 'tipo' },
                { 'data': 'descripcion' },
                { 'data': 'archivo' },
                { 'data': 'usuario' },
                { 'data': 'botones' }
            ],
            columnDefs: [
                { orderable: false, targets: 6 }
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
        $('#tb_lista_documentos_acf').wrap('<div class="overflow"/>');
    });
})(jQuery);