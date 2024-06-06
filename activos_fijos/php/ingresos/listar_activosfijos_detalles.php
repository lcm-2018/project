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

$where = "";
if (isset($_POST['search']['value']) && $_POST['search']['value']){
    $search = $_POST['search']['value'];
    $where .= " AND (AF.placa LIKE '%$search%' OR AF.serial LIKE '%$search%')";
}

try {
    $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
    $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

    //Consulta el total de registros de la tabla
    $sql = "SELECT
                COUNT(*) AS total
            FROM acf_activofijo_ordeningresodetalle AFOD
                INNER JOIN acf_orden_ingreso_detalle OID ON OID.id_ing_detalle = AFOD.id_ordeningresodetalle
                INNER JOIN acf_activofijo AF ON AF.placa = AFOD.placa_activofijo
            WHERE OID.id_ing_detalle=" . $_POST['id_ingreso_detalle'];
    $rs = $cmd->query($sql);
    $total = $rs->fetch();
    $totalRecords = $total['total'];

    //Consulta el total de registros aplicando el filtro
    $sql = "SELECT
                COUNT(*) AS total
            FROM acf_activofijo_ordeningresodetalle AFOD
                INNER JOIN acf_orden_ingreso_detalle OID ON OID.id_ing_detalle = AFOD.id_ordeningresodetalle
                INNER JOIN acf_activofijo AF ON AF.placa = AFOD.placa_activofijo
            WHERE OID.id_ing_detalle=" . $_POST['id_ingreso_detalle'] . $where; 
    $rs = $cmd->query($sql);
    $total = $rs->fetch();
    $totalRecordsFilter = $total['total'];

    //Consulta los datos para listarlos en la tabla
    $sql = "SELECT
                OID.id_orden_ingreso,
                OID.id_ing_detalle,
                AF.placa,
                AF.serial,
                m.descripcion marca,
                AF.valor,
                CASE AF.tipo_activo WHEN 1 THEN 'PROPIEDAD, PLANTA Y EQUIPO' WHEN 2 THEN 'PROPIDAD PARA LA VENTA' WHEN 3 THEN 'PROPIEDAD DE INVERSION' END AS tipo_activo 
            FROM acf_activofijo_ordeningresodetalle AFOD
                INNER JOIN acf_orden_ingreso_detalle OID ON OID.id_ing_detalle = AFOD.id_ordeningresodetalle
                INNER JOIN acf_activofijo AF ON AF.placa = AFOD.placa_activofijo
                INNER JOIN acf_marca M ON M.id = AF.id_marca
            WHERE OID.id_ing_detalle=" . $_POST['id_ingreso_detalle'] . $where . " ORDER BY $col $dir $limit";

    $rs = $cmd->query($sql);
    $objs = $rs->fetchAll();
    $cmd = null;
} catch (PDOException $e) {
    echo $e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage();
}

$editar = NULL;
$eliminar = NULL;
$editaractivofijo = NULL;
$data = [];
if (!empty($objs)) {
    foreach ($objs as $obj) {
        $id_ingreso_detalle = $obj['id_ing_detalle'];
        //Permite crear botones en la cuadricula si tiene permisos de 1-Consultar,2-Crear,3-Editar,4-Eliminar,5-Anular,6-Imprimir
        if (PermisosUsuario($permisos, 5006, 3) || $id_rol == 1) {
            $editar = '<a value="' . $id_ingreso_detalle . '" class="btn btn-outline-primary btn-sm btn-circle shadow-gb btn_editar" title="Editar"><span class="fas fa-pencil-alt fa-lg"></span></a>';
        }
        if (PermisosUsuario($permisos, 5006, 4) || $id_rol == 1) {
            $eliminar =  '<a value="' . $id_ingreso_detalle . '" class="btn btn-outline-danger btn-sm btn-circle shadow-gb btn_eliminar" title="Eliminar"><span class="fas fa-trash-alt fa-lg"></span></a>';
        }
        $data[] = [
            //"id_ing_detalle" => $id_ing_detalle,
            "placa" => $obj['placa'],
            "serial" => $obj['serial'],
            "marca" => $obj['marca'],
            "valor" => formato_valor($obj['valor']),
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

   