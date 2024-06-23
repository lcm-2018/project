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

$id = isset($_POST['id_hv']) ? $_POST['id_hv'] : -1;
$sql = "SELECT 
            HV.id,
            HV.placa,
            HV.serial,
            HV.id_marca,
            HV.valor,
            HV.tipo_activo,
            HV.id_articulo,
            HV.modelo,
            HV.id_sede,
            HV.id_area,
            HV.id_proveedor,
            HV.lote,
            HV.fecha_fabricacion,
            HV.reg_invima,
            HV.fabricante,
            HV.lugar_origen,
            HV.representante,
            HV.dir_representante,
            HV.tel_representante,
            HV.imagen,
            HV.recom_fabricante,
            HV.tipo_adquisicion,
            HV.fecha_adquisicion,
            HV.fecha_instalacion,
            HV.periodo_garantia,
            HV.vida_util,
            HV.calif_4725,
            HV.calibracion,
            HV.vol_min,
            HV.vol_max,
            HV.frec_min,
            HV.frec_max,
            HV.pot_min,
            HV.pot_max,
            HV.cor_min,
            HV.cor_max,
            HV.temp_min,
            HV.temp_max,
            HV.riesgo,
            HV.uso,
            HV.cb_diagnostico,
            HV.cb_prevencion,
            HV.cb_rehabilitacion,
            HV.cb_analisis_lab,
            HV.cb_trat_mant,
            HV.estado_general,
            HV.causa_est_general,
            HV.fecha_fuera_servicio,
            HV.id_usr_reg,
            HV.fecha_reg,
            HV.id_usr_act,
            HV.fecha_act,
            HV.estado
        FROM acf_hojavida HV
        LEFT JOIN tb_sedes SD ON (SD.id_sede=HV.id_sede)
        WHERE HV.id=" . $id . " LIMIT 1";
$rs = $cmd->query($sql);
$obj = $rs->fetch();

if ($obj === false) {
    $obj = array(); // Inicializa $obj como un array vacío
}

if (empty($obj)) {
    $n = $rs->columnCount();
    for ($i = 0; $i < $n; $i++) :
        $col = $rs->getColumnMeta($i);
        $name = $col['name'];
        $obj[$name] = NULL;
    endfor;
    //Inicializa variable por defecto
    $obj['estado'] = 1;
    $obj['nom_estado'] = 'PENDIENTE';
    $obj['val_total'] = 0;

    $bodega = sede_principal($cmd);
    $obj['id_sede'] = $bodega['id_sede'];
    $obj['nom_sede'] = $bodega['nom_sede'];

    $area = area_principal($cmd);
    $obj['id_area'] = $area['id_area'];
    $obj['nom_area'] = $area['nom_area'];

    $fecha = fecha_hora_servidor();
    $obj['fec_ingreso'] = $fecha['fecha'];
    $obj['hor_ingreso'] = $fecha['hora'];
} else {
    
    $bodega = sede_principal($cmd);
    $obj['id_sede'] = $bodega['id_sede'];
    $obj['nom_sede'] = $bodega['nom_sede'];
    
    if($obj['id_area'] == null) {
        $area = area_principal($cmd);
        $obj['id_area'] = $area['id_area'];
        $obj['nom_area'] = $area['nom_area'];
    }
}
$guardar = in_array($obj['estado'],[1]) ? '' : 'disabled="disabled"';
$cerrar = in_array($obj['estado'],[1]) && $id != -1 ? '' : 'disabled="disabled"';
$anular = in_array($obj['estado'],[2]) ? '' : 'disabled="disabled"';
$imprimir = $id != -1 ? '' : 'disabled="disabled"';

?>

<div class="px-0">
    <div class="shadow">
        <div class="card-header mb-3" style="background-color: #16a085 !important;">
            <h5 style="color: white;">ADJUNTAR IMAGEN Y DOCUMENTOS</h5>
        </div>
        <div class="px-2">
            <form id="acf_reg_docs_hoja_vida" enctype="multipart/formdata">
                <input type="hidden" id="id_hv" name="id_hv" value="<?php echo $id ?>">
                    <div class="form-group col-md-12">
                        <label for="uploadImageAcf" class="small">Imagen</label>
                        <input type="file" class="form-control-file form-control-sm" id="uploadImageAcf" name="uploadImageAcf" accept=".jpg,.jpeg,.png">
                    </div>
                </div>
                <div class="form-group mt-3">
                    <button type="button" class="btn btn-primary btn-sm" id="btn_guardar_archivos" <?php echo $guardar ?>>Guardar</button>
                    <a type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Salir</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Aquí puedes agregar cualquier script adicional necesario para el funcionamiento del formulario
</script>


