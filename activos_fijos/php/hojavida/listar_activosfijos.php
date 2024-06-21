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

if (isset($_POST['nombre']) && $_POST['nombre']) {
    $where .= " AND FM.nom_medicamento='" . $_POST['nombre'] . "'";
}
if (isset($_POST['placa']) && $_POST['placa']) {
    $where .= " AND HV.placa='" . $_POST['placa'] . "'";
}
if (isset($_POST['serial']) && $_POST['serial']) {
    $where .= " AND HV.serial='" . $_POST['serial'] . "'";
}
if (isset($_POST['marca']) && $_POST['marca']) {
    $where .= " AND M.id=" . $_POST['marca'] . "";
}
if (isset($_POST['tipoactivo']) && strlen($_POST['tipoactivo'])) {
    $where .= " AND tipo_activo=" . $_POST['tipoactivo'];
}

try {
    $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
    $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

    //Consulta el total de registros de la tabla
    $sql = "SELECT COUNT(*) AS total FROM acf_hojavida HV";
    $rs = $cmd->query($sql);
    $total = $rs->fetch();
    $totalRecords = $total['total'];

    //Consulta el total de registros aplicando el filtro
    $sql = "SELECT COUNT(*) AS total, 
                CASE HV.tipo_activo WHEN 1 THEN 'PROPIEDAD, PLANTA Y EQUIPO' WHEN 2 THEN 'PROPIDAD PARA LA VENTA' WHEN 3 THEN 'PROPIEDAD DE INVERSION' END AS tipo_activo 
            FROM acf_hojavida HV
                INNER JOIN far_medicamentos FM On FM.id_med = HV.id_articulo
                INNER JOIN acf_marca M ON M.id = HV.id_marca  $where";
    $rs = $cmd->query($sql);
    $total = $rs->fetch();
    $totalRecordsFilter = $total['total'];

    //Consulta los datos para listarlos en la tabla
    $sql = "SELECT
                HV.id,
                FM.cod_medicamento cod_articulo,
                FM.nom_medicamento nom_articulo,
                HV.placa,
                HV.serial,
                M.descripcion marca,
                HV.valor,
                CASE HV.tipo_activo WHEN 1 THEN 'PROPIEDAD, PLANTA Y EQUIPO' WHEN 2 THEN 'PROPIDAD PARA LA VENTA' WHEN 3 THEN 'PROPIEDAD DE INVERSION' END AS tipo_activo 
            FROM acf_hojavida HV
                INNER JOIN far_medicamentos FM On FM.id_med = HV.id_articulo
                INNER JOIN acf_marca M ON M.id = HV.id_marca 
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
