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

$id = isset($_POST['id_detalle_mantenimiento']) ? $_POST['id_detalle_mantenimiento'] : -1;
$sql = "SELECT 
            MD.id_detalle_mantenimiento,
            MD.id_mantenimiento,
            m.nom_medicamento articulo,
            HV.placa,
            HV.id_activo_fijo id_activofijo
        FROM acf_mantenimiento_detalle MD
        INNER JOIN acf_hojavida HV ON HV.id_activo_fijo = MD.id_activo_fijo
        INNER JOIN far_medicamentos M ON M.id_med = HV.id_articulo
        WHERE MD.id_detalle_mantenimiento=" . $id . " LIMIT 1";
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
}

$imprimir = $id != -1 ? '' : 'disabled="disabled"';

?>

<div class="px-0">
    <div class="shadow">
        <div class="card-header mb-3" style="background-color: #16a085 !important;">
            <h5 style="color: white;">REGISRTAR NOTA DE MANTENIMIENTO</h5>
        </div>
        <div class="px-2">
            <form id="frm_nota_mantenimiento">
                <input type="hidden" id="id_detalle_mantenimiento" name="id_detalle_mantenimiento" value="<?php echo $id ?>">
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label for="id_mantenimiento" class="small">Id. Detalle</label>
                        <input type="text" class="form-control form-control-sm" id="txt_id_mantenimiento" name="txt_id_mantenimiento" class="small" value="<?php echo ($id==-1?'':$id) ?>" readonly="readonly">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="articulo" class="small">Articulo</label>
                        <input type="text" class="form-control form-control-sm" id="articulo" name="articulo" class="small" value="<?php echo $obj['articulo'] ?>" readonly="readonly">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="placa" class="small">Placa</label>
                        <input type="text" class="form-control form-control-sm" id="placa" name="placa" class="small" value="<?php echo $obj['placa'] ?>" readonly="readonly">
                    </div>
                </div>
            </form>    
            <table id="tb_mantenimientos_notas" class="table table-striped table-bordered table-sm nowrap table-hover shadow" style="width:100%; font-size:80%">
                <thead>
                    <tr class="text-center centro-vertical">
                        <th>Id</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Observacion</th>
                        <th>Archivo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-left centro-vertical"></tbody>
            </table>
        </div>
    </div>
    <div class="text-center pt-3">
        <button type="button" class="btn btn-primary btn-sm" id="btn_guardar">Guardar</button>
        <button type="button" class="btn btn-primary btn-sm" id="btn_imprimir" <?php echo $imprimir ?>>Imprimir</button>
        <a type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</a>
    </div>
</div>

<script type="text/javascript" src="../../js/mantenimiento/mantenimiento_notas_reg.js?v=<?php echo date('YmdHis') ?>"></script>