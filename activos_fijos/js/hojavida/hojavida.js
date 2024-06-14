(function($) {
    $(document).on('show.bs.modal', '.modal', function() {
        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function() {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    });

    $(document).ready(function() {
        //Tabla de Registros
        $('#tb_hojavida').DataTable({
            dom: setdom,
            buttons: [{
                action: function(e, dt, node, config) {
                    $.post("acf_reg_orden_ingreso.php", function(he) {
                        $('#divTamModalForms').removeClass('modal-sm');
                        $('#divTamModalForms').removeClass('modal-lg');
                        $('#divTamModalForms').addClass('modal-xl');
                        $('#divModalForms').modal('show');
                        $("#divForms").html(he);
                    });
                }
            }],
            language: setIdioma,
            processing: true,
            serverSide: true,
            searching: false,
            ajax: {
                url: 'listar_activosfijos.php',
                type: 'POST',
                dataType: 'json',
                data: function(data) {
                    data.id_ing = $('#txt_iding_filtro').val();
                    data.num_ing = $('#txt_numing_filtro').val();
                    data.fec_ini = $('#txt_fecini_filtro').val();
                    data.fec_fin = $('#txt_fecfin_filtro').val();
                    data.id_tercero = $('#sl_tercero_filtro').val();
                    data.id_tiping = $('#sl_tiping_filtro').val();
                    data.estado = $('#sl_estado_filtro').val();
                }
            },
            columns: [
                { 'data': 'id' }, //Index=0
                { 'data': 'cod_articulo' },
                { 'data': 'nom_articulo' },
                { 'data': 'placa' },
                { 'data': 'serial' },
                { 'data': 'marca' },
                { 'data': 'valor' },
                { 'data': 'tipo_activo' },
                { 'data': 'botones' }
            ],
            columnDefs: [
                { class: 'text-wrap', targets: [4, 5] },
                { type: "numeric-comma", targets: 6 },
                { orderable: false, targets: 8 }
            ],
            rowCallback: function(row, data) {
                var estado = $($(row).find("td")[12]).text();
                if (estado == 'PENDIENTE') {
                    $($(row).find("td")[0]).css("background-color", "yellow");
                } else if (estado == 'ANULADO') {
                    $($(row).find("td")[0]).css("background-color", "gray");
                }
            },
            order: [
                [0, "desc"]
            ],
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, 'TODO'],
            ],
        });

        $('.bttn-plus-dt span').html('<span class="icon-dt fas fa-plus-circle fa-lg"></span>');
        $('#tb_ingresos').wrap('<div class="overflow"/>');
    });

    //Buascar registros de Ingresos
    $('#btn_buscar_filtro').on("click", function() {
        $('.is-invalid').removeClass('is-invalid');
        reloadtable('tb_ingresos');
    });

    $('.filtro').keypress(function(e) {
        if (e.keyCode == 13) {
            reloadtable('tb_ingresos');
        }
    });

    //Editar un registro Orden Ingreso
    $('#tb_ingresos').on('click', '.btn_editar', function() {
        let id = $(this).attr('value');
        $.post("acf_reg_orden_ingreso.php", { id: id }, function(he) {
            $('#divTamModalForms').addClass('modal-xl');
            $('#divModalForms').modal('show');
            $("#divForms").html(he);
        });
    });

    //Guardar registro Orden Ingreso
    $('#divForms').on("click", "#btn_guardar", function() {
        $('.is-invalid').removeClass('is-invalid');

        var error = verifica_vacio_2($('#id_txt_sede'), $('#txt_nom_sede'));
        error += verifica_vacio($('#txt_fec_ing'));
        error += verifica_vacio($('#txt_hor_ing'));
        error += verifica_vacio($('#sl_tip_ing'));

        if ($('#sl_tip_ing').find('option:selected').attr('data-intext') == 2) {
            error += verifica_vacio($('#sl_tercero'));
        }

        error += verifica_vacio($('#txt_det_ing'));

        if (error >= 1) {
            $('#divModalError').modal('show');
            $('#divMsgError').html('Los datos resaltados son obligatorios');
        } else {
            var data = $('#acf_reg_orden_ingreso').serialize();
            $.ajax({
                type: 'POST',
                url: 'editar_orden_ingreso.php',
                dataType: 'json',
                data: data + "&oper=add"
            }).done(function(r) {
                if (r.mensaje == 'ok') {
                    let pag = ($('#id_ingreso').val() == -1) ? 0 : $('#tb_ingresos').DataTable().page.info().page;
                    reloadtable('tb_ingresos', pag);
                    $('#id_ingreso').val(r.id);

                    $('#btn_cerrar').prop('disabled', false);
                    $('#btn_imprimir').prop('disabled', false);

                    $('#divModalDone').modal('show');
                    $('#divMsgDone').html("Proceso realizado con éxito");
                } else {
                    $('#divModalError').modal('show');
                    $('#divMsgError').html(r.mensaje);
                }
            }).always(
                function() {}
            ).fail(function(xhr, textStatus, errorThrown) {
                console.error(xhr.responseText)
                alert('Ocurrió un error');
            });
        }
    });

    //Borrar un registro Orden Ingreso
    $('#tb_ingresos').on('click', '.btn_eliminar', function() {
        let id = $(this).attr('value');
        confirmar_del('ingresos_del', id);
    });
    $('#divModalConfDel').on("click", "#ingresos_del", function() {
        var id = $(this).attr('value');
        $.ajax({
            type: 'POST',
            url: 'editar_orden_ingreso.php',
            dataType: 'json',
            data: { id: id, oper: 'del' }
        }).done(function(r) {
            $('#divModalConfDel').modal('hide');
            if (r.mensaje == 'ok') {
                let pag = $('#tb_ingresos').DataTable().page.info().page;
                reloadtable('tb_ingresos', pag);
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

    //Cerrar un registro Orden Ingreso
    $('#divForms').on("click", "#btn_cerrar", function() {
        confirmar_proceso('ingresos_close');
    });
    $('#divModalConfDel').on("click", "#ingresos_close", function() {
        $.ajax({
            type: 'POST',
            url: 'editar_ingresos.php',
            dataType: 'json',
            data: { id: $('#id_ingreso').val(), oper: 'close' }
        }).done(function(r) {
            $('#divModalConfDel').modal('hide');
            if (r.mensaje == 'ok') {
                let pag = $('#tb_ingresos').DataTable().page.info().page;
                reloadtable('tb_ingresos', pag);

                $('#txt_num_ing').val(r.num_ingreso);
                $('#txt_est_ing').val('CERRADO');

                $('#btn_guardar').prop('disabled', true);
                $('#btn_cerrar').prop('disabled', true);
                $('#btn_anular').prop('disabled', false);

                $('#divModalDone').modal('show');
                $('#divMsgDone').html("Proceso realizado con éxito");
            } else {
                $('#divModalError').modal('show');
                $('#divMsgError').html(r.mensaje);
            }
        }).always(function() {}).fail(function(xhr, textStatus, errorThrown) {
            console.error(xhr.responseText)
            alert('Ocurrió un error');
        });
    });

    //Anular un registro Orden Ingreso
    $('#divForms').on("click", "#btn_anular", function() {
        confirmar_proceso('ingresos_annul');
    });
    $('#divModalConfDel').on("click", "#ingresos_annul", function() {
        $.ajax({
            type: 'POST',
            url: 'editar_ingresos.php',
            dataType: 'json',
            data: { id: $('#id_ingreso').val(), oper: 'annul' }
        }).done(function(r) {
            $('#divModalConfDel').modal('hide');
            if (r.mensaje == 'ok') {
                let pag = $('#tb_ingresos').DataTable().page.info().page;
                reloadtable('tb_ingresos', pag);

                $('#txt_est_ing').val('ANULADO');

                $('#btn_guardar').prop('disabled', true);
                $('#btn_cerrar').prop('disabled', true);
                $('#btn_anular').prop('disabled', true);

                $('#divModalDone').modal('show');
                $('#divMsgDone').html("Proceso realizado con éxito");
            } else {
                $('#divModalError').modal('show');
                $('#divMsgError').html(r.mensaje);
            }
        }).always(function() {}).fail(function() {
            alert('Ocurrió un error');
        });
    });

    /* ---------------------------------------------------
    DETALLES
    -----------------------------------------------------*/
    $('#divModalBus').on('dblclick', '#tb_lotes_articulos tr', function() {
        let idMed = $(this).find('td:eq(0)').text();
        $.post("acf_reg_orden_ingreso_detalle.php", { idMed: idMed }, function(he) {
            $('#divTamModalReg').addClass('modal-lg');
            $('#divModalReg').modal('show');
            $("#divFormsReg").html(he);

        });
    });

    $('#divForms').on('click', '#tb_ingresos_detalles .btn_editar', function() {
        let id = $(this).attr('value');
        $.post("acf_reg_orden_ingreso_detalle.php", { id: id }, function(he) {
            $('#divTamModalReg').addClass('modal-lg');
            $('#divModalReg').modal('show');
            $("#divFormsReg").html(he);
        });
    });

    //Editar activos fijos
    $('#divForms').on('click', '#tb_ingresos_detalles .btn_activofijo', function() {
        let id = $(this).attr('value');
        $.post("acf_reg_activofijo.php", { id: id }, function(he) {
            $('#divTamModalReg').addClass('modal-xl');
            $('#divModalReg').modal('show');
            $("#divFormsReg").html(he);
        });
    });

    // Autocompletar Presentacion de Lote
    $('#divFormsReg').on("input", "#txt_pre_lot", function() {
        $(this).autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "../common/cargar_prescomercial_ls.php",
                    dataType: "json",
                    type: 'POST',
                    data: { term: request.term }
                }).done(function(data) {
                    response(data);
                });
            },
            minLength: 2,
            select: function(event, ui) {
                $('#id_txt_pre_lot').val(ui.item.id);
                $('#txt_can_lot').val(ui.item.cantidad);
            }
        });
    });

    $('#divModalReg').on('input', '#txt_val_uni, #sl_por_iva', function() {
        var valor = $('#txt_val_uni').val() ? $('#txt_val_uni').val() : 0,
            iva = $('#sl_por_iva').val() ? $('#sl_por_iva').val() : 0;
        $('#txt_val_cos').val(parseFloat(valor) + parseFloat(valor) * parseFloat(iva) / 100);
    });

    //Guardar registro Detalle
    $('#divFormsReg').on("click", "#btn_guardar_detalle", function() {
        $('.is-invalid').removeClass('is-invalid');

        var error = verifica_vacio($('#txt_can_ing'));
        error += verifica_vacio($('#txt_val_uni'));
        error += verifica_vacio($('#txt_val_cos'));

        if (error >= 1) {
            $('#divModalError').modal('show');
            $('#divMsgError').html('Los datos resaltados son obligatorios');
        } else if (!verifica_valmin($('#txt_can_ing'), 1, "La cantidad debe ser mayor igual a 1")) {
            var data = $('#acf_reg_ingresos_detalles').serialize();
            $.ajax({
                type: 'POST',
                url: 'editar_orden_ingreso_detalle.php',
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
                alert('Error al guardar detalle');
            });
        }
    });

    //Borrarr un registro Detalle
    $('#divForms').on('click', '#tb_ingresos_detalles .btn_eliminar', function() {
        let id = $(this).attr('value');
        confirmar_del('detalle', id);
    });
    $('#divModalConfDel').on("click", "#detalle", function() {
        var id = $(this).attr('value');
        $.ajax({
            type: 'POST',
            url: 'editar_orden_ingreso_detalle.php',
            dataType: 'json',
            data: { id: id, id_ingreso: $('#id_ingreso').val(), oper: 'del' }
        }).done(function(r) {
            $('#divModalConfDel').modal('hide');
            if (r.mensaje == 'ok') {
                let pag = $('#tb_ingresos_detalles').DataTable().page.info().page;
                reloadtable('tb_ingresos_detalles', pag);
                pag = $('#tb_ingresos').DataTable().page.info().page;
                reloadtable('tb_ingresos', pag);

                $('#txt_val_tot').val(r.val_total);

                $('#divModalDone').modal('show');
                $('#divMsgDone').html("Proceso realizado con éxito");
            } else {
                $('#divModalError').modal('show');
                $('#divMsgError').html(r.mensaje);
            }
        }).always(function() {}).fail(function(xhr, textStatus, errorThrown) {
            console.error(xhr.responseText)
            alert('Ocurrió un error');
        });
    });

    //Imprimir listado de registros
    $('#btn_imprime_filtro').on('click', function() {
        reloadtable('tb_ingresos');
        $('.is-invalid').removeClass('is-invalid');
        var verifica = verifica_vacio($('#txt_fecini_filtro'));
        verifica += verifica_vacio($('#txt_fecfin_filtro'));
        if (verifica >= 1) {
            $('#divModalError').modal('show');
            $('#divMsgError').html('Debe escribir un rango de fechas');
        } else {
            $.post("imp_ingresos.php", {
                id_ing: $('#txt_iding_filtro').val(),
                num_ing: $('#txt_numing_filtro').val(),
                fec_ini: $('#txt_fecini_filtro').val(),
                fec_fin: $('#txt_fecfin_filtro').val(),
                id_tercero: $('#sl_tercero_filtro').val(),
                id_tiping: $('#sl_tiping_filtro').val(),
                estado: $('#sl_estado_filtro').val()
            }, function(he) {
                $('#divTamModalImp').removeClass('modal-sm');
                $('#divTamModalImp').removeClass('modal-lg');
                $('#divTamModalImp').addClass('modal-xl');
                $('#divModalImp').modal('show');
                $("#divImp").html(he);
            });
        }
    });

    //Imprimit una Orden de Ingreso
    $('#divForms').on("click", "#btn_imprimir", function() {
        $.post("imp_ingreso.php", {
            id: $('#id_ingreso').val()
        }, function(he) {
            $('#divTamModalImp').removeClass('modal-sm');
            $('#divTamModalImp').removeClass('modal-lg');
            $('#divTamModalImp').addClass('modal-xl');
            $('#divModalImp').modal('show');
            $("#divImp").html(he);
        });
    });

})(jQuery);