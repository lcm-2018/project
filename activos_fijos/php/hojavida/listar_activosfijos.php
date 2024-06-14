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

if (isset($_POST['id_ing']) && $_POST['id_ing']) {
    $where .= " AND acf_orden_ingreso.id_ingreso='" . $_POST['id_ing'] . "'";
}
if (isset($_POST['num_ing']) && $_POST['num_ing']) {
    $where .= " AND acf_orden_ingreso.num_ingreso='" . $_POST['num_ing'] . "'";
}
if (isset($_POST['fec_ini']) && $_POST['fec_ini'] && isset($_POST['fec_fin']) && $_POST['fec_fin']) {
    $where .= " AND acf_orden_ingreso.fec_ingreso BETWEEN '" . $_POST['fec_ini'] . "' AND '" . $_POST['fec_fin'] . "'";
}
if (isset($_POST['id_tercero']) && $_POST['id_tercero']) {
    $where .= " AND acf_orden_ingreso.id_provedor=" . $_POST['id_tercero'] . "";
}
if (isset($_POST['id_tiping']) && $_POST['id_tiping']) {
    $where .= " AND acf_orden_ingreso.id_tipo_ingreso=" . $_POST['id_tiping'] . "";
}
if (isset($_POST['estado']) && strlen($_POST['estado'])) {
    $where .= " AND acf_orden_ingreso.estado=" . $_POST['estado'];
}

try {
    $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
    $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

    //Consulta el total de registros de la tabla
    $sql = "SELECT COUNT(*) AS total FROM acf_orden_ingreso";
    $rs = $cmd->query($sql);
    $total = $rs->fetch();
    $totalRecords = $total['total'];

    //Consulta el total de registros aplicando el filtro
    $sql = "SELECT COUNT(*) AS total FROM acf_orden_ingreso $where";
    $rs = $cmd->query($sql);
    $total = $rs->fetch();
    $totalRecordsFilter = $total['total'];

    //Consulta los datos para listarlos en la tabla
    /*
    $sql = "SELECT 
            acf_orden_ingreso.id_ingreso,
            acf_orden_ingreso.num_ingreso,
            acf_orden_ingreso.fec_ingreso,
            acf_orden_ingreso.hor_ingreso,
            acf_orden_ingreso.detalle,
            tb_terceros.nom_tercero,
            far_orden_ingreso_tipo.nom_tipo_ingreso,
            acf_orden_ingreso.val_total,
            tb_sedes.nom_sede,
	        CASE acf_orden_ingreso.estado WHEN 1 THEN 'PENDIENTE' WHEN 2 THEN 'CERRADO' WHEN 0 THEN 'ANULADO' END AS nom_estado
            FROM acf_orden_ingreso
            INNER JOIN far_orden_ingreso_tipo ON (far_orden_ingreso_tipo.id_tipo_ingreso=acf_orden_ingreso.id_tipo_ingreso)
            INNER JOIN tb_terceros ON (tb_terceros.id_tercero=acf_orden_ingreso.id_provedor)
            INNER JOIN tb_sedes ON (tb_sedes.id_sede=acf_orden_ingreso.id_sede)
            $where ORDER BY $col $dir $limit";*/
    
    $sql = "SELECT
                HV.id,
                FM.cod_medicamento cod_articulo,
                FM.nom_medicamento nom_articulo,
                HV.placa,
                HV.serial,
                m.descripcion marca,
                HV.valor,
                CASE HV.tipo_activo WHEN 1 THEN 'PROPIEDAD, PLANTA Y EQUIPO' WHEN 2 THEN 'PROPIDAD PARA LA VENTA' WHEN 3 THEN 'PROPIEDAD DE INVERSION' END AS tipo_activo 
            FROM acf_hojavida HV
                INNER JOIN far_medicamentos FM On FM.id_med = HV.id_articulo
                INNER JOIN acf_marca M ON M.id = HV.id_marca";

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
        $id = $obj['id'];
        //Permite crear botones en la cuadricula si tiene permisos de 1-Consultar,2-Crear,3-Editar,4-Eliminar,5-Anular,6-Imprimir
        if (PermisosUsuario($permisos, 5006, 3) || $id_rol == 1) {
            $editar = '<a value="' . $id . '" class="btn btn-outline-primary btn-sm btn-circle shadow-gb btn_editar" title="Editar"><span class="fas fa-pencil-alt fa-lg"></span></a>';
        }
        if (PermisosUsuario($permisos, 5006, 4) || $id_rol == 1) {
            $eliminar =  '<a value="' . $id . '" class="btn btn-outline-danger btn-sm btn-circle shadow-gb btn_eliminar" title="Eliminar"><span class="fas fa-trash-alt fa-lg"></span></a>';
        }
        $data[] = [
            "id" => $id,
            "cod_articulo" => $obj['cod_articulo'],
            "nom_articulo" => $obj['nom_articulo'],
            "placa" => $obj['placa'],
            "serial" => $obj['serial'],
            "marca" => $obj['marca'],
            "valor" => $obj['valor'],
            "tipo_activo" => $obj['tipo_activo'],
            "botones" => '<div class="text-center centro-vertical">' . $editar . $eliminar . '</div>',
        ];
    }
}
$datos = [
    "data" => $data,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecordsFilter
];

echo json_encode($datos);
