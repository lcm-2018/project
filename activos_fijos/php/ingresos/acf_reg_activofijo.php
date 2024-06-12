<?php
session_start();
if (!isset($_SESSION['user'])) {
    echo '<script>window.location.replace("../../../index.php");</script>';
    exit();
}
include '../../../conexion.php';
include '../common/cargar_combos.php';
include '../common/funciones_generales.php';

$cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
$cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

$id = isset($_POST['id']) ? $_POST['id'] : -1;
$sql = "SELECT OID.id_ing_detalle,
            FM.cod_medicamento,
            FM.nom_medicamento,
            OID.cantidad,
            OID.valor_sin_iva,
            OID.iva,
            OID.valor,
            (OID.valor*OID.cantidad) AS val_total,
            OID.observacion
            FROM acf_orden_ingreso_detalle OID
            INNER JOIN far_medicamentos FM ON (FM.id_med = OID.id_medicamento_articulo)
        WHERE OID.id_ing_detalle=" . $id . " LIMIT 1";
$rs = $cmd->query($sql);
$obj = $rs->fetch();

if ($obj === false) {
    $obj = array(); // Inicializa $obj como un array vacío
}

$guardar =  $id != -1 ? '' : 'disabled="disabled"';
$imprimir = $id != -1 ? '' : 'disabled="disabled"';

?>

<div class="px-0">
    <div class="shadow">
        <div class="card-header mb-3" style="background-color: #16a085 !important;">
            <h5 style="color: white;">REGISRTAR ACTIVOS FIJO</h5>
        </div>
        <div class="px-2">
            <!--Formulario de registro de Ordenes de Ingreso-->
            <form id="acf_reg_orden_ingreso">
                <input type="hidden" id="id_ingreso_detalle" name="id_ingreso_detlle" value="<?php echo $id ?>">
                <input type="hidden" id="id_cod_articulo" name="id_cod_articulo" value="<?php echo $obj['cod_medicamento'] ?>">
                <input type="hidden" id="id_nom_articulo" name="id_nom_articulo" value="<?php echo $obj['nom_medicamento'] ?>">
                <input type="hidden" id="id_costo" name="id_costo" value="<?php echo $obj['valor'] ?>">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="txt_cod_med" class="small">Codigo</label>
                        <input type="text" class="form-control form-control-sm" id="txt_cod_med" class="small" value="<?php echo $obj['cod_medicamento'] ?>" readonly="readonly">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="txt_desc_med" class="small">Descripción</label>
                        <input type="text" class="form-control form-control-sm" id="txt_desc_med" class="small" value="<?php echo $obj['nom_medicamento'] ?>" readonly="readonly">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="txt_cdd_med" class="small">Cantidad</label>
                        <input type="text" class="form-control form-control-sm" id="txt_cdd_med" class="small" value="<?php echo $obj['cantidad'] ?>" readonly="readonly">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="txt_vrunit_med" class="small">Vr. Unitario</label>
                        <input type="text" class="form-control form-control-sm" id="txt_vrunit_med" class="small" value="<?php echo formato_valor($obj['valor_sin_iva']) ?>" readonly="readonly">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="txt_iva_med" class="small">%IVA</label>
                        <input type="text" class="form-control form-control-sm" id="txt_iva_med" class="small" value="<?php echo $obj['iva'] ?>" readonly="readonly">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="txt_costo_med" class="small">Costo</label>
                        <input type="text" class="form-control form-control-sm" id="txt_costo_med" class="small" value="<?php echo formato_valor($obj['valor']) ?>" readonly="readonly">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="txt_total_med" class="small">Total</label>
                        <input type="text" class="form-control form-control-sm" id="txt_total_med" class="small" value="<?php echo formato_valor($obj['val_total']) ?>" readonly="readonly">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="txt_observacion_med" class="small">%IVA</label>
                        <input type="text" class="form-control form-control-sm" id="txt_observacion_med" class="small" value="<?php echo $obj['observacion'] ?>" readonly="readonly">
                    </div>
          
                </div>
            </form>    
            <table id="tb_lista_activos_fijos" class="table table-striped table-bordered table-sm nowrap table-hover shadow" style="width:100%; font-size:80%">
                <thead>
                    <tr class="text-center centro-vertical">
                        <th>Placa</th>
                        <th>Serial</th>
                        <th>Marca</th>
                        <th>Valor</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-left centro-vertical"></tbody>
            </table>
        </div>
    </div>
    <div class="text-center pt-3">
        <button type="button" class="btn btn-primary btn-sm" id="btn_guardar" <?php echo $guardar ?>>Guardar</button>
        <button type="button" class="btn btn-primary btn-sm" id="btn_imprimir" <?php echo $imprimir ?>>Imprimir</button>
        <a type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</a>
    </div>
</div>

<script type="text/javascript" src="../../js/ingresos/activofijo_reg.js?v=<?php echo date('YmdHis') ?>"></script>