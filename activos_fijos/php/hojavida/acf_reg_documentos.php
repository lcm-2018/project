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
            HV.imagen,
            HV.id_usr_act,
            HV.fecha_act
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

    $fecha = fecha_hora_servidor();
    $obj['fec_ingreso'] = $fecha['fecha'];
    $obj['hor_ingreso'] = $fecha['hora'];
} else {
    
   
}

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
                        <input type="file" class="form-control-file form-control-sm" id="uploadImageAcf" name="uploadImageAcf" accept=".jpg,.jpeg,.png" value="<?php echo $obj['imagen'] ?>">
                    </div>
                </div>
                <div class="form-group mt-3">
                    <button type="button" class="btn btn-primary btn-sm" id="btn_guardar_archivos">Guardar</button>
                    <a type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Salir</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Aquí puedes agregar cualquier script adicional necesario para el funcionamiento del formulario
</script>


