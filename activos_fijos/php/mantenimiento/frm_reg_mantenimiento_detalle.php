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

$id_art = isset($_POST['idart']) ? $_POST['idart'] : -1;
$id = isset($_POST['id_detalle_mantenimiento']) ? $_POST['id_detalle_mantenimiento'] : -1;
$id_mantenimiento = isset($_POST['id_mantenimiento']) ? $_POST['id_mantenimiento'] : -1;
$sql = "SELECT 
            MD.id_detalle_mantenimiento,
            MD.id_mantenimiento,
            m.nom_medicamento articulo,
            HV.placa,
            HV.id_activo_fijo id_activofijo,
            CONCAT(HV.placa,' (',M.nom_medicamento,')') as nombre_activofijo,
            MD.observacion_mantenimiento,
            MD.estado estado,
            MD.estado_fin_mantenimiento estado_fin,
            MD.observacio_fin_mantenimiento
        FROM acf_mantenimiento_detalle MD
            INNER JOIN acf_hojavida HV ON HV.id_activo_fijo = MD.id_activo_fijo
            INNER JOIN far_medicamentos M ON M.id_med = HV.id_articulo
        WHERE MD.id_detalle_mantenimiento=" . $id . " LIMIT 1";
$rs = $cmd->query($sql);
$obj = $rs->fetch();

if ($obj === false) {
    $obj = array(); 
}

if (empty($obj)) {
    $n = $rs->columnCount();
    for ($i = 0; $i < $n; $i++) :
        $col = $rs->getColumnMeta($i);
        $name = $col['name'];
        $obj[$name] = NULL;
    endfor;
}
?>

<div class="px-0">
    <div class="shadow">
        <div class="card-header mb-3" style="background-color: #16a085 !important;">
            <h7 style="color: white;">REGISRTAR DETALLE EN ORDEN DE MANTENIMIENTO</h7>
        </div>
        <div class="px-2">

            <!--Formulario de registro de Detalle-->
            <form id="frm_reg_mantenimiento_detalle">
                <input type="hidden" id="id_detalle_mantenimiento" name="id_detalle_mantenimiento" value="<?php echo $id ?>">
                <input type="hidden" id="id_mantenimiento" name="id_mantenimiento" value="<?php echo $id_mantenimiento ?>">
                <div class=" form-row">
                    <div class="form-group col-md-10">
                    <label for="txt_activo_fijo" class="small">Activo Fijo</label>
                        <input type="text" class="form-control form-control-sm" id="txt_activo_fijo" required value="<?php echo $obj['nombre_activofijo'] ?>">
                        <input type="hidden" id="id_txt_activo_fijo" name="id_txt_activo_fijo" value="<?php echo $obj['id_activofijo'] ?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="estado_detalle" class="small" required>Estado</label>
                        <select class="form-control form-control-sm" id="estado_detalle" name="estado_detalle">
                            <?php estados_detalle_mantenimiento('', $obj['estado']) ?>
                        </select>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="observacion_mantenimiento" class="small">Observación Mantenimiento</label>
                        <input type="text" class="form-control form-control-sm" id="observacion_mantenimiento" name="observacion_mantenimiento" value="<?php echo $obj['observacion_mantenimiento'] ?>">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="text-center pt-3">
        <button type="button" class="btn btn-primary btn-sm" id="btn_guardar_detalle">Guardar</button>
        <a type="button" class="btn btn-secondary  btn-sm" data-dismiss="modal">Cancelar</a>
    </div>
</div>
