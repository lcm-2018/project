(function($) {
    $(document).ready(function() {
        $('#tb_lista_activos_fijos').DataTable({
            dom: setdom,
            buttons: [{
                action: function(e, dt, node, config) {
                    $.post("acf_reg_activofijo_detalle.php", { 
                            idIngresoDetalle: $('#id_ingreso_detalle').val(), 
                            idArticulo: $('#id_articulo').val(),
                            id_cod_articulo: $('#id_cod_articulo').val(),
                            id_nom_articulo: $('#id_nom_articulo').val(),
                            id_costo: $('#id_costo').val(),
                        }, function(he) {
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
        let idIngresoDetalle = $('#id_ingreso_detalle').val()
        let idArticulo = $('#id_articulo').val();
        let placa = $(this).attr('value');
        $.post("acf_reg_activofijo_detalle.php", 
        { 
            placa: placa,
            idIngresoDetalle: idIngresoDetalle,
            idArticulo: idArticulo,
        }, function(he) {
            $('#divTamModalBus').removeClass('modal-lg');
            $('#divTamModalBus').removeClass('modal-sm');
            $('#divTamModalBus').addClass('modal-xl');
            $('#divModalBus').modal('show');
            $("#divFormsBus").html(he);
        });
    });

    //GUARDAR ACTIVO FIJO
    $('#divFormsBus').on("click", "#btn_guardar_activofijo", function() {
        $('.is-invalid').removeClass('is-invalid');

        var error = verifica_vacio($('#txt_placa'));
        error += verifica_vacio($('#txt_serial'));
        error += verifica_vacio($('#sl_marca'));
        error += verifica_vacio($('#sl_tipoactivo'));

        if (error >= 1) {
            $('#divModalError').modal('show');
            $('#divMsgError').html('Los datos resaltados son obligatorios');
        } else {
            var data = $('#acf_reg_activofijo_detalles').serialize();

            let idIngresoDetalle = $('#id_ingreso_detalle').val();
            let idArticulo = $('#id_articulo').val();
            let placa = $('#id_placa').val();

            $.ajax({
                type: 'POST',
                url: 'editar_activofijo_detalle.php',
                dataType: 'json',
                data: data + "&id_ingreso_detalle=" + idIngresoDetalle + "&placa=" + placa + "&id_Articulo=" + idArticulo +'&oper=add'
            }).done(function(r) {
                if (r.mensaje == 'ok') {
                    pag = $('#tb_lista_activos_fijos').DataTable().page.info().page;
                    reloadtable('tb_lista_activos_fijos', 0);

                    $('#id_ingreso_detalle').val(r.id_ingreso_detalle);
                    $('#divModalBus').modal('hide');
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

    //ELEIMINAR ACTIVO FIJO DE ORDEN
    $('#divFormsReg').on('click', '#tb_lista_activos_fijos .btn_eliminar', function() {
        let placa = $(this).attr('value');
        confirmar_del('activofijo_del', placa);
    });
    $('#divModalConfDel').on("click", "#activofijo_del", function() {
        var placa = $(this).attr('value');
        let idIngresoDetalle = $('#id_ingreso_detalle').val()
        $.ajax({
            type: 'POST',
            url: 'editar_activofijo_detalle.php',
            dataType: 'json',
            data: { 
                    id_ingreso_detalle: idIngresoDetalle, 
                    placa: placa, 
                    oper: 'del' 
                }
        }).done(function(r) {
            $('#divModalConfDel').modal('hide');
            if (r.mensaje == 'ok') {
                let pag = $('#tb_lista_activos_fijos').DataTable().page.info().page;
                reloadtable('tb_lista_activos_fijos', pag);
                $('#divModalDone').modal('show');
                $('#divMsgDone').html("Proceso realizado con éxito");
            } else {
                $('#divModalError').modal('show');
                $('#divMsgError').html(r.mensaje);
            }
        }).always(function() {

        }).fail(function(xhr, textStatus, errorThrown) {
            console.error(xhr.responseText)
            alert('Ocurrió un error');
        });
    });


})(jQuery);