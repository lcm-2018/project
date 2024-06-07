(function($) {
    $(document).ready(function() {
        $('#tb_lista_activos_fijos').DataTable({
            dom: setdom,
            buttons: [{
                action: function(e, dt, node, config) {
                    $.post("acf_reg_activofijo_detalle.php", { id_sede: $('#id_txt_sede').val(), id_bodega: $('#id_txt_nom_bod').val() }, function(he) {
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

    $('#divFormsReg').on('click', '#tb_lista_activos_fijos .btn_editar', function() {
        let idIngresoDetalle = $(this).attr('value');
        $.post("acf_reg_activofijo_detalle.php", { idIngresoDetalle: idIngresoDetalle }, function(he) {
            $('#divTamModalBus').removeClass('modal-lg');
            $('#divTamModalBus').removeClass('modal-sm');
            $('#divTamModalBus').addClass('modal-xl');
            $('#divModalBus').modal('show');
            $("#divFormsBus").html(he);
        });
    });

    $('#divFormsBus').on("click", "#btn_guardar_activofijo", function() {
        $('.is-invalid').removeClass('is-invalid');

        var error = verifica_vacio($('#txt_placa'));
        error += verifica_vacio($('#txt_serial'));
        error += verifica_vacio($('#sl_marca'));
        error += verifica_vacio($('#sl_tipoactivo'));

        if (error >= 1) {
            $('#divModalError').modal('show');
            $('#divMsgError').html('Los datos resaltados son obligatorios');
        } else if (!verifica_valmin($('#txt_can_ing'), 1, "La cantidad debe ser mayor igual a 1")) {
            var data = $('#acf_reg_activofijo_detalles').serialize();
            $.ajax({
                type: 'POST',
                url: 'editar_activofijo_detalle.php',
                dataType: 'json',
                data: data + "&id_ingreso=" + $('#id_ingreso').val() + '&oper=add'
            }).done(function(r) {
                if (r.mensaje == 'ok') {
                    let pag = ($('#id_detalle').val() == -1) ? 0 : $('#tb_ingresos_detalles').DataTable().page.info().page;
                    reloadtable('tb_ingresos_detalles', pag);
                    pag = $('#tb_ingresos').DataTable().page.info().page;
                    reloadtable('tb_ingresos', pag);

                    $('#id_detalle').val(r.id);
                    $('#txt_val_tot').val(r.val_total);

                    $('#divModalReg').modal('hide');
                    $('#divModalDone').modal('show');
                    $('#divMsgDone').html("Proceso realizado con éxito");
                } else {
                    $('#divModalError').modal('show');
                    $('#divMsgError').html(r.mensaje);
                }
            }).always(function() {}).fail(function(xhr, textStatus, errorThrown) {
                console.error(xhr.responseText)
                alert('Error al guardar activo');
            });
        }
    });

})(jQuery);