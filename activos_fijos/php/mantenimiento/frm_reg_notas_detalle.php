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

$id_nota = isset($_POST['id_nota_mantenimiento']) ? $_POST['id_nota_mantenimiento'] : -1;
$id_hv = isset($_POST['id_hv']) ? $_POST['id_hv'] : -1;

$fecha = fecha_hora_servidor();

$sql = "SELECT 
        id,
        fecha,
        hora,
        observaciones,
        archivo 
        FROM acf_detalle_mantenimiento_nota
        WHERE id=" . $id_nota . " LIMIT 1";
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
?>

<div class="px-0">
    <div class="shadow">
        <div class="card-header mb-3" style="background-color: #16a085 !important;">
            <h5 style="color: white;">ADJUNTAR NOTAS</h5>
        </div>
        <div class="px-2">
            <form id="acf_reg_docs_hoja_vida" enctype="multipart/formdata">
                <input type="hidden" id="id_hv" name="id_hv" value="<?php echo $id_hv ?>">
                <input type="hidden" id="id_hv_doc" name="id_hv_doc" value="<?php echo $id_nota ?>">

                <div class="form-row">
                <div class="form-group col-md-2">
                        <label for="txt_id_nota" class="small">Id. Nota</label>
                        <input type="text" class="form-control form-control-sm" id="txt_id_nota" name="txt_id_nota" class="small" value="<?php echo ($id_nota==-1?'':$id_nota) ?>" readonly="readonly">
                </div>
                <div class="form-group col-md-10">
                    <label for="observaciones_nota" class="small">Observacion</label>
                    <input type="text" class="form-control form-control-sm" id="observaciones_nota" name="observaciones_nota" value="<?php echo $obj['observaciones'] ?>">
                </div>

                <div class="form-group col-md-12">
                    <label for="uploadImageAcf" class="small text-left">Documento</label>
                    <div class="input-group mb-3"> 
                        <button type="button" id="btn_descargar_documento" class="btn btn-outline-primary btn-sm shadow-gb" title="Descargar"> <span class="fas fa-download"></span></button>
                        <input type="label" class="form-control form-control-sm" id="archivo" name="archivo" value="<?php echo $obj['archivo'] ?>" readonly="readonly">
                    </div> 
                    <div class="input-group mb-3"> 
                        <div class="custom-file">
                            <input type="file" class="custom-file-input form-control-sm" id="uploadDocNota" accept=".pdf">
                            <label class="custom-file-label" for="customFile">Seleccionar documento</label>
                        </div>
                    </div>
                </div>
                <hr>
                </div>

                <div class="form-group mt-3">
                    <button type="button" class="btn btn-primary btn-sm" id="btn_guardar_notas">Guardar</button>
                    <a type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Salir</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>


