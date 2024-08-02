(function($) {
    $(document).ready(function() {
        $('#tb_mantenimientos_notas').DataTable({
            dom: setdom,
            buttons: [{
                action: function(e, dt, node, config) {
                    $.post("frm_reg_mantenimiento_detalle.php", function(he) {
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
                url: 'listar_mantenimientos_notas.php',
                type: 'POST',
                dataType: 'json',
                data: function(data) {
                    data.id_detalle_mantenimiento = $('#id_detalle_mantenimiento').val();
                }
            },
            columns: [
                { 'data': 'id' }, //Index=0
                { 'data': 'fecha' },
                { 'data': 'hora' },
                { 'data': 'observaciones' },
                { 'data': 'archivo' },
                { 'data': 'botones' }
            ],
            columnDefs: [
                { class: 'text-wrap', targets: [3, 4] },
                { orderable: false, targets: 5 }
            ],
            order: [
                [0, "desc"]
            ],
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, 'TODO'],
            ],
            "searching": false
        });
        $('.bttn-plus-dt span').html('<span class="icon-dt fas fa-plus-circle fa-lg"></span>');
        $('#tb_mantenimientos_notas').wrap('<div class="overflow"/>');
    });

    //Editar 
    $('#tb_mantenimientos_notas').on('click', '.btn_editar', function() {
        let id = $(this).attr('value');
        $.post("frm_reg_mantenimiento_detalle.php", { 
            id_detalle_mantenimiento: id,
            id_mantenimiento: $('#id_mantenimiento').val()
        }, function(he) {
            $('#divTamModalBus').removeClass('modal-lg');
            $('#divTamModalBus').removeClass('modal-sm');
            $('#divTamModalBus').addClass('modal-xl');
            $('#divModalBus').modal('show');
            $("#divFormsBus").html(he);
        });
    }); 

    //Editar 
    $('#tb_mantenimientos_notas').on('click', '.btn_notas', function() {
        let id = $(this).attr('value');
        $.post("frm_reg_mantenimiento_nota.php", { 
            id_detalle_mantenimiento: id,
            id_mantenimiento: $('#id_mantenimiento').val()
        }, function(he) {
            $('#divTamModalBus').removeClass('modal-lg');
            $('#divTamModalBus').removeClass('modal-sm');
            $('#divTamModalBus').addClass('modal-xl');
            $('#divModalBus').modal('show');
            $("#divFormsBus").html(he);
        });
    }); 

    //Borrar
    $('#tb_mantenimientos_notas').on('click', '.btn_eliminar', function() {
        let id = $(this).attr('value');
        confirmar_del('mantenimiento_detalle_del', id);
    });
    $('#divModalConfDel').on("click", "#mantenimiento_detalle_del", function() {
        let id = $(this).attr('value');
        $.ajax({
            type: 'POST',
            url: 'editar_mantenimiento_detalle.php',
            dataType: 'json',
            data: { id_detalle_mantenimiento: id, oper: 'del' }
        }).done(function(r) {
            $('#divModalConfDel').modal('hide');
            if (r.mensaje == 'ok') {
                let pag = $('#tb_mantenimientos_detalles').DataTable().page.info().page;
                reloadtable('tb_mantenimientos_detalles', pag);
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