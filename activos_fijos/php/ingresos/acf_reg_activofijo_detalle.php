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

$idMed = isset($_POST['idMed']) ? $_POST['idMed'] : -1;
$idIngresoDetalle = isset($_POST['idIngresoDetalle']) ? $_POST['idIngresoDetalle'] : -1;
$sql = "SELECT
            OID.id_orden_ingreso,
            OID.id_ing_detalle,
            OID.valor costo,
            FM.cod_medicamento cod_articulo,
            FM.nom_medicamento nom_articulo,
            AF.placa,
            AF.serial,
            m.id id_marca,
            m.descripcion marca,
            AF.valor,
            CASE AF.tipo_activo WHEN 1 THEN 'PROPIEDAD, PLANTA Y EQUIPO' WHEN 2 THEN 'PROPIDAD PARA LA VENTA' WHEN 3 THEN 'PROPIEDAD DE INVERSION' END AS tipo_activo 
        FROM acf_activofijo_ordeningresodetalle AFOD
            INNER JOIN acf_orden_ingreso_detalle OID ON OID.id_ing_detalle = AFOD.id_ordeningresodetalle
            INNER JOIN far_medicamentos FM ON (FM.id_med = OID.id_medicamento_articulo)
            INNER JOIN acf_activofijo AF ON AF.placa = AFOD.placa_activofijo
            INNER JOIN acf_marca M ON M.id = AF.id_marca
        WHERE OID.id_ing_detalle=" . $idIngresoDetalle . " LIMIT 1";
$rs = $cmd->query($sql);
$obj = $rs->fetch();

if (empty($obj)) {
    $n = $rs->columnCount();
    for ($i = 0; $i < $n; $i++) :
        $col = $rs->getColumnMeta($i);
        $name = $col['name'];
        $obj[$name] = NULL;
    endfor;
    $articulo = datos_articulo_acf($cmd, $idMed);
    $obj['id_med'] = $idMed;
    $obj['nom_articulo'] = $articulo['nom_articulo'];
    $obj['cod_articulo'] = $articulo['cod_articulo'];

}
?>

<div class="px-0">
    <div class="shadow">
        <div class="card-header mb-3" style="background-color: #16a085 !important;">
            <h7 style="color: white;">REGISRTAR DETALLE ACTIVO FIJO</h7>
        </div>
        <div class="px-2">

            <!--Formulario de registro de Detalle-->
            <form id="acf_reg_ingresos_detalles">
                <input type="hidden" id="id_detalle" name="id_detalle" value="<?php echo $id ?>">
                <div class=" form-row">
                    <div class="form-group col-md-6">
                        <label for="txt_cod_art" class="small">Codigo</label>
                        <input type="text" class="form-control form-control-sm" id="txt_cod_art" class="small" value="<?php echo $obj['cod_articulo'] ?>" readonly="readonly">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="txt_nom_art" class="small">Articulo</label>
                        <input type="text" class="form-control form-control-sm" id="txt_nom_art" class="small" value="<?php echo $obj['nom_articulo'] ?>" readonly="readonly">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="txt_observacion" class="small">Placa</label>
                        <input type="text" class="form-control form-control-sm" id="txt_placa" name="txt_placa" value="<?php echo $obj['placa'] ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="txt_observacion" class="small">Serial</label>
                        <input type="text" class="form-control form-control-sm" id="txt_serial" name="txt_serial" value="<?php echo $obj['serial'] ?>">
                    </div>

                    <div class="form-group col-md-3">
                        <label for="sl_tercero" class="small">Marca</label>
                        <select class="form-control form-control-sm" id="sl_marca" name="sl_marca">
                            <?php marcas($cmd, '', $obj['id_marca']) ?>
                        </select>
                    </div>
            
                    <div class="form-group col-md-4">
                        <label for="txt_val_uni" class="small">Vr. Unitario</label>
                        <input type="text" class="form-control form-control-sm numberfloat" id="txt_val_uni" name="txt_val_uni" required value="<?php echo $obj['costo'] ?>" readonly="readonly">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="sl_tercero" class="small">Tipo Activo</label>
                        <select class="form-control form-control-sm" id="sl_tipoactivo" name="sl_tipoactivo">
                            <?php tiposActivo($cmd, '', $obj['tipo_activo']) ?>
                        </select>
                    </div>

                </div>
            </form>
        </div>
    </div>
    <div class="text-center pt-3">
        <button type="button" class="btn btn-primary btn-sm" id="btn_guardar_activofijo">Guardar</button>
        <a type="button" class="btn btn-secondary  btn-sm" data-dismiss="modal">Cancelar</a>
    </div>
</div>