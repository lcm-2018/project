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

$id = isset($_POST['id_mantenimiento']) ? $_POST['id_mantenimiento'] : -1;
$sql = "SELECT 
            M.id_mantenimiento,
            M.fecha_mantenimiento,
            M.hora_mantenimiento,
            M.observaciones,
            M.tipo_mantenimiento,
            M.id_responsable,
            M.id_tercero,
            M.fecha_inicio_mantenimiento,
            M.fecha_fin_mantenimiento,
            CASE M.estado WHEN 1 THEN 'PENDIENTE' WHEN 2 THEN 'APROBADO' WHEN 3 THEN 'EN EJECUCION' WHEN 4 THEN 'FINALIZADO' END AS estado,
            M.fecha_creacion,
            M.usuaro_creacion,
            M.fecha_aprobacion,
            M.usuario_aprobacion,
            M.fecha_ejecucion,
            M.usuario_ejecucion
        FROM acf_mantenimiento M
        WHERE M.id_mantenimiento=" . $id . " LIMIT 1";
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
    $obj['estado'] = 'PENDIENTE';
    $obj['val_total'] = 0;

    $fecha = fecha_hora_servidor();
    $obj['fecha_mantenimiento'] = $fecha['fecha'];
    $obj['hora_mantenimiento'] = $fecha['hora'];
}
$guardar = in_array($obj['estado'],['PENDIENTE']) ? '' : 'disabled="disabled"';
$cerrar = in_array($obj['estado'],[1]) && $id != -1 ? '' : 'disabled="disabled"';
$anular = in_array($obj['estado'],[2]) ? '' : 'disabled="disabled"';
$imprimir = $id != -1 ? '' : 'disabled="disabled"';

?>

<div class="px-0">
    <div class="shadow">
        <div class="card-header mb-3" style="background-color: #16a085 !important;">
            <h5 style="color: white;">REGISRTAR ORDEN DE MANTENIMIENTO</h5>
        </div>
        <div class="px-2">
            <form id="frm_reg_mantenimiento">
                <input type="hidden" id="id_mantenimiento" name="id_mantenimiento" value="<?php echo $id ?>">
                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label for="id_mantenimiento" class="small">Id.</label>
                        <input type="text" class="form-control form-control-sm" id="id_mantenimiento" name="id_mantenimiento" class="small" value="<?php echo ($id==-1?'':$id) ?>" readonly="readonly">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="txt_fec_ing" class="small">Fecha</label>
                        <input type="text" class="form-control form-control-sm" id="fecha_mantenimiento" name="fecha_mantenimiento" class="small" value="<?php echo $obj['fecha_mantenimiento'] ?>" readonly="readonly">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="txt_hor_ing" class="small">Hora</label>
                        <input type="text" class="form-control form-control-sm" id="hora_mantenimiento" name="hora_mantenimiento" class="small" value="<?php echo $obj['hora_mantenimiento'] ?>" readonly="readonly">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="estado" class="small">Estado</label>
                        <input type="text" class="form-control form-control-sm" id="estado" name="estado" class="small" value="<?php echo $obj['estado'] ?>" readonly="readonly">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="tipo_mantenimiento" class="small" required>Tipo Mantenimiento</label>
                        <select class="form-control form-control-sm" id="tipo_mantenimiento" name="tipo_mantenimiento">
                            <?php tipos_mantenimiento('', $obj['tipo_mantenimiento']) ?>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="id_responsable" class="small">Reponsable</label>
                        <select class="form-control form-control-sm" id="id_responsable" name="id_responsable">
                            <?php usuarios($cmd, '', $obj['id_responsable']) ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="tercero" class="small">Tercero</label>
                        <select class="form-control form-control-sm" id="id_tercero" name="id_tercero">
                            <?php terceros($cmd, '', $obj['id_tercero']) ?>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="fecha_inicio_mantenimiento" class="small">Inicio Mantenimiento</label>
                        <input type="date" class="form-control form-control-sm" id="fecha_inicio_mantenimiento" name="fecha_inicio_mantenimiento" class="small" value="<?php echo $obj['fecha_inicio_mantenimiento'] ?>">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="fecha_fin_mantenimiento" class="small">Fin Mantenimiento</label>
                        <input type="date" class="form-control form-control-sm" id="fecha_fin_mantenimiento" name="fecha_fin_mantenimiento" class="small" value="<?php echo $obj['fecha_inicio_mantenimiento'] ?>">
                    </div>
                    <div class="form-group col-md-12">
                    <label for="txt_det_ing" class="small">Observaciones</label>                   
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="2"><?php echo $obj['observaciones'] ?></textarea>
                    </div>
                </div>
            </form>    
            <table id="tb_mantenimientos_detalles" class="table table-striped table-bordered table-sm nowrap table-hover shadow" style="width:100%; font-size:80%">
                <thead>
                    <tr class="text-center centro-vertical">
                        <th>Id</th>
                        <th>Articulo</th>
                        <th>Placa</th>
                        <th>Observacion Mantenimiento</th>
                        <th>Estado</th>
                        <th>Estado Finalizacion</th>
                        <th>Observacion Finalizacion</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-left centro-vertical"></tbody>
            </table>
        </div>
    </div>
    <div class="text-center pt-3">
        <button type="button" class="btn btn-primary btn-sm" id="btn_guardar" <?php echo $guardar ?>>Guardar</button>
        <button type="button" class="btn btn-primary btn-sm" id="btn_cerrar" <?php echo $cerrar ?>>Cerrar</button>
        <button type="button" class="btn btn-primary btn-sm" id="btn_anular" <?php echo $anular ?>>Anular</button>
        <button type="button" class="btn btn-primary btn-sm" id="btn_imprimir" <?php echo $imprimir ?>>Imprimir</button>
        <a type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</a>
    </div>
</div>

<script type="text/javascript" src="../../js/mantenimiento/mantenimiento_reg.js?v=<?php echo date('YmdHis') ?>"></script>