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
        $('#tb_bajas').DataTable({
            dom: setdom,
            buttons: [{
                action: function(e, dt, node, config) {
                    $.post("frm_reg_baja.php", function(he) {
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
                url: 'listar_bajas.php',
                type: 'POST',
                dataType: 'json',
                data: function(data) {
                    data.id_BAJA= $('#txt_idbaja_filtro').val();
                    data.fec_ini = $('#txt_fecini_filtro').val();
                    data.fec_fin = $('#txt_fecfin_filtro').val();
                    data.estado = $('#sl_estado_filtro').val();
                }
            },
            columns: [
                { 'data': 'id_baja' }, //Index=0
                { 'data': 'observaciones' },
                { 'data': 'fecha_baja' },
                { 'data': 'estado' },
                { 'data': 'botones' }
            ],
            columnDefs: [
                { class: 'text-wrap', targets: [2, 3] },
                { orderable: false, targets: 4 }
            ],
            rowCallback: function(row, data) {
                var estado = $($(row).find("td")[11]).text();
                if (estado == 'PENDIENTE') {
                    $($(row).find("td")[0]).css("background-color", "yellow");
                } else if (estado == 'CERRADO') {
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
        $('#tb_bajas').wrap('<div class="overflow"/>');
    });

    //Buascar registros de Ingresos
    $('#btn_buscar_filtro').on("click", function() {
        $('.is-invalid').removeClass('is-invalid');
        reloadtable('tb_bajas');
    });

    $('.filtro').keypress(function(e) {
        if (e.keyCode == 13) {
            reloadtable('tb_bajas');
        }
    });

    //Editar un registro Orden Ingreso
    $('#tb_bajas').on('click', '.btn_editar', function() {
        let id = $(this).attr('value');
        $.post("frm_reg_baja.php", { id_baja: id }, function(he) {
            $('#divTamModalForms').addClass('modal-xl');
            $('#divModalForms').modal('show');
            $("#divForms").html(he);
        });
    });

    //Guardar registro Orden mantenimiento
    $('#divForms').on("click", "#btn_guardar", function() {
        $('.is-invalid').removeClass('is-invalid');

        var error = verifica_vacio($('#tipo_mantenimiento'));
        error += verifica_vacio($('#id_responsable'));
        error += verifica_vacio($('#id_tercero'));
        error += verifica_vacio($('#fecha_inicio_mantenimiento'));
        error += verifica_vacio($('#fecha_fin_mantenimiento'));
        error += verifica_vacio($('#observaciones'));

        if (error >= 1) {
            $('#divModalError').modal('show');
            $('#divMsgError').html('Los datos resaltados son obligatorios');
        } else {
            var data = $('#frm_reg_mantenimiento').serialize();
            $.ajax({
                type: 'POST',
                url: 'editar_mantenimiento.php',
                dataType: 'json',
                data: data + "&oper=add"
            }).done(function(r) {
                if (r.mensaje == 'ok') {
                    let pag = ($('#id_mantenimiento').val() == -1) ? 0 : $('#tb_mantenimientos').DataTable().page.info().page;
                    reloadtable('tb_mantenimientos', pag);
                    $('#id_mantenimiento').val(r.id);

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

    //Borrar un registro Orden de mantenimiento
    $('#tb_bajas').on('click', '.btn_eliminar', function() {
        let id = $(this).attr('value');
        confirmar_del('bajas_del', id);
    });
    $('#divModalConfDel').on("click", "#bajas_del", function() {
        var id = $(this).attr('value');
        $.ajax({
            type: 'POST',
            url: 'editar_mantenimiento.php',
            dataType: 'json',
            data: { id_mantenimiento: id, oper: 'del' }
        }).done(function(r) {
            $('#divModalConfDel').modal('hide');
            if (r.mensaje == 'ok') {
                let pag = $('#tb_mantenimientos').DataTable().page.info().page;
                reloadtable('tb_mantenimientos', pag);
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

    //Aprobar orden de mantenimiento
    $('#divForms').on("click", "#btn_aprobado", function() {
        confirmar_proceso('mantenimiento_aprobar');
    });
    $('#divModalConfDel').on("click", "#mantenimiento_aprobar", function() {
        $.ajax({
            type: 'POST',
            url: 'editar_mantenimiento.php',
            dataType: 'json',
            data: { id_mantenimiento: $('#id_mantenimiento').val(), oper: 'aprobar' }
        }).done(function(r) {
            $('#divModalConfDel').modal('hide');
            if (r.mensaje == 'ok') {
                let pag = $('#tb_mantenimientos').DataTable().page.info().page;
                reloadtable('tb_mantenimientos', pag);

                $('#estado').val('APROBADO');

                $('#btn_guardar').prop('disabled', true);
                $('#btn_ejecucion').prop('disabled', false);
                $('#btn_aprobado').prop('disabled', true);

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

    //Ejecutar orden de mantenimiento
    $('#divForms').on("click", "#btn_ejecucion", function() {
        confirmar_proceso('mantenimiento_ejecutar');
    });
    $('#divModalConfDel').on("click", "#mantenimiento_ejecutar", function() {
        $.ajax({
            type: 'POST',
            url: 'editar_mantenimiento.php',
            dataType: 'json',
            data: { id_mantenimiento: $('#id_mantenimiento').val(), oper: 'ejecutar' }
        }).done(function(r) {
            $('#divModalConfDel').modal('hide');
            if (r.mensaje == 'ok') {
                let pag = $('#tb_mantenimientos').DataTable().page.info().page;
                reloadtable('tb_mantenimientos', pag);

                $('#estado').val('EN EJECUCION');

                $('#btn_guardar').prop('disabled', true);
                $('#btn_ejecucion').prop('disabled', true);
                $('#btn_aprobado').prop('disabled', true);

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
    $('#divForms').on('click', '#tb_ingresos_detalles .btn_editar', function() {
        let id = $(this).attr('value');
        $.post("frm_reg_ingresos_detalle.php", { id: id }, function(he) {
            $('#divTamModalReg').addClass('modal-lg');
            $('#divModalReg').modal('show');
            $("#divFormsReg").html(he);
        });
    });

    //Guardar registro Detalle
    $('#divModalBus').on("click", "#btn_guardar_detalle", function() {
        $('.is-invalid').removeClass('is-invalid');

        var error = verifica_vacio($('#txt_activo_fijo'));
        error += verifica_vacio($('#estado_detalle'));
        error += verifica_vacio($('#observacion_mantenimiento'));

        if (error >= 1) {
            $('#divModalError').modal('show');
            $('#divMsgError').html('Los datos resaltados son obligatorios');
        } else {
            var data = $('#frm_reg_mantenimiento_detalle').serialize();
            $.ajax({
                type: 'POST',
                url: 'editar_mantenimiento_detalle.php',
                dataType: 'json',
                data: data + "&id_detalle_mantenimiento=" + $('#id_detalle_mantenimiento').val() + "&id_mantenimiento=" + $('#id_mantenimiento').val() + '&oper=add'
            }).done(function(r) {
                if (r.mensaje == 'ok') {
                    let pag = ($('#id_detalle_mantenimiento').val() == -1) ? 0 : $('#tb_mantenimientos_detalles').DataTable().page.info().page;
                    reloadtable('tb_mantenimientos_detalles', pag);

                    $('#id_detalle_mantenimiento').val(r.id);
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

    // Autocompletar Activo fijo
    $('#divTamModalBus').on("input", "#txt_activo_fijo", function() {
        $(this).autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "../common/cargar_activos_fijos.php",
                    dataType: "json",
                    type: 'POST',
                    data: { term: request.term }
                }).done(function(data) {
                    response(data);
                });
            },
            minLength: 2,
            select: function(event, ui) {
                $('#id_txt_activo_fijo').val(ui.item.id);
            }
        });
    });

    /* ---------------------------------------------------
    NOTAS
    -----------------------------------------------------*/
    //Guardar documentos NOTAS MANTENIMIENTO
    $('#divTamModalReg').on("click", "#btn_guardar_notas", function() {
        $('.is-invalid').removeClass('is-invalid');

        var error = verifica_vacio($('#observaciones_nota'));
 
        var file =  $('#uploadDocNota')[0].files[0];
        if(!$('#archivo').val()) {
            if(!file) {
                showError('Por favor, selecciona un archivo')
                return;
            }
            
            var validImageTypes = ["application/pdf", "application/pdf"];
            
            if (!validImageTypes.includes(file.type)) {
                showError('Por favor, selecciona un documento válido')
                return;
            }
        }

        let datos = new FormData();
        datos.append('id_nota_mantenimiento', $('#id_nota_mantenimiento').val());
        datos.append('id_detalle_mantenimiento', $('#id_detalle_mantenimiento').val());
        datos.append('observaciones', $('#observaciones_nota').val());
        datos.append('archivo', $('#archivo').val());

        datos.append('oper','add');
        datos.append('uploadDocNota', file);

        if (error >= 1) {
            $('#divModalError').modal('show');
            $('#divMsgError').html('Los datos resaltados son obligatorios');
        } else {
            $.ajax({
                type: 'POST',
                url: 'editar_documentos_notas.php',
                contentType: false,
                data: datos,
                processData: false,
                cache: false,
            }).done(function(res) {
                var res = JSON.parse(res);
                if (res.mensaje == 'ok') {
                    let pag = ($('#tb_mantenimientos_notas').val() == -1) ? 0 : $('#tb_mantenimientos_notas').DataTable().page.info().page;
                    reloadtable('tb_mantenimientos_notas', pag);
                    $('#id_nota').val(res.id_nota);
                    $('#archivo').val(res.nombre_archivo);
                    $('#divModalDone').modal('show');
                    $('#divMsgDone').html("Proceso realizado con éxito");
                } else {
                    $('#divModalError').modal('show');
                    $('#divMsgError').html(res.mensaje);
                }
            }).always(
                function() {}
            ).fail(function(xhr, textStatus, errorThrown) {
                console.error(xhr.responseText)
                alert('Ocurrió un error');
            });
        }
    });

    //Descarar documento  hoja de vida
    $('#divTamModalReg').on("click", "#btn_descargar_documento_nota", function() {
        $('.is-invalid').removeClass('is-invalid');

        let nombreImagen = $('#archivo').val()

        // Construir la URL relativa al archivo
        var urlDescarga = '../../imagenes/activos_fijos/' + nombreImagen

        // Redirigir al usuario a la URL para iniciar la descarga
        window.open(urlDescarga, '_blank');
    });
    

})(jQuery);