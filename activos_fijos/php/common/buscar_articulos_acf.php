<?php
session_start();
if (!isset($_SESSION['user'])) {
    echo '<script>window.location.replace("../../../index.php");</script>';
    exit();
}
include '../../../conexion.php';

$cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
$cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

$id_sede = isset($_POST['id_sede']) ? $_POST['id_sede'] : -1;

$sql = "SELECT nom_sede FROM tb_sedes WHERE id_sede=$id_sede";
$rs = $cmd->query($sql);
$obj = $rs->fetch();
$nom_sede = isset($obj['nom_sede']) ? $obj['nom_sede'] : '';
?>

<div class="px-0">
    <div class="shadow">
        <div class="card-header mb-3" style="background-color: #16a085 !important;">
            <h7 style="color: white;">BUSCAR ARTICULOS - ACTIVO FIJO</h7>
        </div>
        <div class="px-2">

            <!--Formulario de busqueda de lotes-->
            <form id="frm_buscar_lotes">
                <input type="hidden" id="id_sede_fil" value="<?php echo $id_sede ?>">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <input type="text" class="form-control form-control-sm" class="small" value="<?php echo $nom_sede ?>" readonly="readonly">
                    </div>
                    <div class="form-group col-md-2">
                        <input type="text" class="filtro_lot form-control form-control-sm" id="txt_codigo_art_fil" placeholder="Codigo">
                    </div>
                    <div class="form-group col-md-2">
                        <input type="text" class="filtro_lot form-control form-control-sm" id="txt_nombre_art_fil" placeholder="Nombre">
                    </div>
                    <div class="form-group col-md-2">
                        <div class="form-control form-control-sm">
                            <input class="filtro_lot form-check-input" type="checkbox" id="chk_conexistencia_lot_fil" checked>
                            <label class="filtro_lot form-check-label small" for="chk_conexistencia_lot_fil">Con Existencias</label>
                        </div>
                    </div>
                    <div class="form-group col-md-1">
                        <a type="button" id="btn_buscar_lot_fil" class="btn btn-outline-success btn-sm" title="Filtrar">
                            <span class="fas fa-search fa-lg" aria-hidden="true"></span>
                        </a>
                    </div>
                </div>
            </form>
            <div style="height:400px" class="overflow-auto"> 
                <table id="tb_lotes_articulos" class="table table-striped table-bordered table-sm nowrap table-hover shadow" style="width:100%; font-size:80%">
                    <thead>
                        <tr class="text-center centro-vertical">
                            <th>Id</th>
                            <th>Código</th>
                            <th>Artículo</th>
                            <th>Existencia</th>
                            <th>Vr. Promedio</th>
                        </tr>
                    </thead>
                    <tbody class="text-left centro-vertical"></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="text-right pt-3 rigth">
        <a type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Salir</a>
    </div>
</div>

<script>
    (function($) {
        $(document).ready(function() {
            $('#tb_lotes_articulos').DataTable({
                language: setIdioma,
                processing: true,
                serverSide: true,
                searching: false,
                ajax: {
                    url: '../common/buscar_articulos_acf_lista.php',
                    type: 'POST',
                    dataType: 'json',
                    data: function(data) {
                        data.id_sede = $('#id_sede_fil').val();
                        data.id_bodega = $('#id_bodega_fil').val();
                        data.codigo = $('#txt_codigo_art_fil').val();
                        data.nombre = $('#txt_nombre_art_fil').val();
                        data.no_vencidos = $('#chk_novencido_lot_fil').is(':checked') ? 1 : 0;
                        data.con_existencia = $('#chk_conexistencia_lot_fil').is(':checked') ? 1 : 0;
                    }
                },
                columns: [
                    { 'data': 'id_med' }, //Index=0
                    { 'data': 'cod_medicamento' },
                    { 'data': 'nom_medicamento' },                   
                    { 'data': 'existencia'  },
                    { 'data': 'val_promedio' },
                ],
                columnDefs: [{
                    targets: [2, 4],
                    class: 'text-wrap'
                }],
                order: [
                    [0, "desc"]
                ],
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'TODO'],
                ]
            });
            $('#tb_lotes_articulos').wrap('<div class="overflow"/>');
        });
    })(jQuery);

    //Buascar registros de Lotes de Articulos
    $('#btn_buscar_lot_fil').on("click", function() {
        reloadtable('tb_lotes_articulos');
    });

    $('.filtro_lot').keypress(function(e) {
        if (e.keyCode == 13) {
            reloadtable('tb_lotes_articulos');
        }
    });

    $('.filtro_lot').mouseup(function(e) {
        reloadtable('tb_lotes_articulos');
    });
    
</script>