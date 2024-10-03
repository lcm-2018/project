<?php
session_start();
if (!isset($_SESSION['user'])) {
    echo '<script>window.location.replace("../../../index.php");</script>';
    exit();
}
include '../../../conexion.php';
include '../../../permisos.php';
include '../common/funciones_generales.php';

$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$limit = "";
if ($length != -1) {
    $limit = "LIMIT $start, $length";
}
$col = $_POST['order'][0]['column'] + 1;
$dir = $_POST['order'][0]['dir'];

$where = " WHERE 1";

if (isset($_POST['id_mantenimiento']) && $_POST['id_mantenimiento']) {
    $where .= " AND M.id_mantenimiento='" . $_POST['id_mantenimiento'] . "'";
}
if (isset($_POST['fec_ini']) && $_POST['fec_ini'] && isset($_POST['fec_fin']) && $_POST['fec_fin']) {
    $where .= " AND M.fecha_mantenimiento BETWEEN '" . $_POST['fec_ini'] . "' AND '" . $_POST['fec_fin'] . "'";
}
if (isset($_POST['id_tercero']) && $_POST['id_tercero']) {
    $where .= " AND M.id_tercero=" . $_POST['id_tercero'] . "";
}
if (isset($_POST['sl_tipomantenimiento_filtro']) && $_POST['sl_tipomantenimiento_filtro']) {
    $where .= " AND M.tipo=" . $_POST['sl_tipomantenimiento_filtro'] . "";
}
if (isset($_POST['estado']) && strlen($_POST['estado'])) {
    $where .= " AND M.estado=" . $_POST['estado'];
}

try {
    $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
    $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

    //Consulta el total de registros de la tabla
    $sql = "SELECT COUNT(*) AS total FROM acf_mantenimiento M";
    $rs = $cmd->query($sql);
    $total = $rs->fetch();
    $totalRecords = $total['total'];

    //Consulta el total de registros aplicando el filtro
    $sql = "SELECT COUNT(*) AS total FROM acf_mantenimiento M $where";
    $rs = $cmd->query($sql);
    $total = $rs->fetch();
    $totalRecordsFilter = $total['total'];

    //Consulta los datos para listarlos en la tabla
    $sql = "SELECT 
                MT.id_mantenimiento,
                CASE MT.tipo_mantenimiento WHEN 1 THEN 'PREVENTIVO' WHEN 2 THEN 'CORRECTIVO INTERNO' WHEN 3 THEN 'CORRECTIVO EXTERNO' END AS tipo,
                MD.id_detalle_mantenimiento,
                m.nom_medicamento articulo,
                HV.placa,
                MD.observacion_mantenimiento,
                CASE MD.estado WHEN 1 THEN 'PENDIENTE' WHEN 2 THEN 'EN MANTENIMIENTO' WHEN 3 THEN 'FINALIZADO' END AS estado,
                CASE MD.estado_fin_mantenimiento WHEN 1 THEN 'BUENO' WHEN 2 THEN 'REGULAR' WHEN 3 THEN 'MALO' END AS estado_fin,
                MD.observacio_fin_mantenimiento
            FROM acf_mantenimiento_detalle MD
                INNER JOIN acf_mantenimiento MT ON MT.id_mantenimiento = MD.id_mantenimiento
                INNER JOIN acf_hojavida HV ON HV.id = MD.id_activo_fijo
                INNER JOIN far_medicamentos M ON M.id_med = HV.id_articulo
            $where ORDER BY $col $dir $limit";

    $rs = $cmd->query($sql);
    $objs = $rs->fetchAll();
    $cmd = null;
} catch (PDOException $e) {
    echo $e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage();
}

$editar = NULL;
$eliminar = NULL;
$data = [];
if (!empty($objs)) {
    foreach ($objs as $obj) {
        $id = $obj['id_mantenimiento'];
        $id_detalle = $obj['id_detalle_mantenimiento'];
        //Permite crear botones en la cuadricula si tiene permisos de 1-Consultar,2-Crear,3-Editar,4-Eliminar,5-Anular,6-Imprimir
        if (PermisosUsuario($permisos, 5703, 3) || $id_rol == 1) {
            $editar = '<a value="' . $id_detalle . '" class="btn btn-outline-primary btn-sm btn-circle shadow-gb btn_editar" title="Editar"><span class="fas fa-pencil-alt fa-lg"></span></a>';
        }
        if (PermisosUsuario($permisos, 5703, 3) || $id_rol == 1) {
            $notas = '<a value="' . $id_detalle . '" class="btn btn-outline-primary btn-sm btn-circle shadow-gb btn_notas" title="Notas"><span class="fas fa-clipboard-list fa-lg"></span></a>';
        }
        if (PermisosUsuario($permisos, 5703, 4) || $id_rol == 1) {
            $eliminar =  '<a value="' . $id_detalle . '" class="btn btn-outline-danger btn-sm btn-circle shadow-gb btn_eliminar" title="Eliminar"><span class="fas fa-trash-alt fa-lg"></span></a>';
        }
        $data[] = [
            "id_mantenimiento" => $id,
            "id_detalle_mantenimiento" => $id_detalle,
            "tipo_mantenimiento" => $obj['tipo'],
            "articulo" => $obj['articulo'],
            "placa" => $obj['placa'],
            "observacion_mantenimiento" => $obj['observacion_mantenimiento'],
            "estado" => $obj['estado'],
            "botones" => '<div class="text-center centro-vertical">' . $editar . $notas . $eliminar . '</div>',
        ];
    }
}
$datos = [
    "data" => $data,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecordsFilter
];

echo json_encode($datos);
