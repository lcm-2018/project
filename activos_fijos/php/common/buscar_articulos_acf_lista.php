<?php
session_start();
if (!isset($_SESSION['user'])) {
    echo '<script>window.location.replace("../../../index.php");</script>';
    exit();
}
include '../../../conexion.php';
include '../common/funciones_generales.php';

$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$limit = "";
if ($length != -1) {
    $limit = "LIMIT $start, $length";
}
$col = $_POST['order'][0]['column'] + 1;
$dir = $_POST['order'][0]['dir'];

$where_gen = ' WHERE FM.estado=1 AND G.id_grupo IN (3 , 4, 5)';

$where = $where_gen;
if (isset($_POST['codigo']) && $_POST['codigo']) {
    $where .= " AND FM.cod_medicamento LIKE '" . $_POST['codigo'] . "%'";
}
if (isset($_POST['nombre']) && $_POST['nombre']) {
    $where .= " AND FM.nom_medicamento LIKE '" . $_POST['nombre'] . "%'";
} 
if (isset($_POST['con_existencia']) && $_POST['con_existencia']) {
    $where .= " AND FM.existencia>0";
}

try {
    $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
    $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

    //Consulta el total de registros de la tabla
    $sql = "SELECT COUNT(*) AS total FROM far_medicamentos FM
        INNER JOIN far_subgrupos SG ON SG.id_subgrupo = FM.id_subgrupo
        INNER JOIN far_grupos G ON G.id_grupo = SG.id_grupo" . $where_gen;

    $rs = $cmd->query($sql);
    $total = $rs->fetch();
    $totalRecords = $total['total'];

    //Consulta el total de registros aplicando el filtro
    $sql = "SELECT COUNT(*) AS total FROM far_medicamentos FM
                INNER JOIN far_subgrupos SG ON SG.id_subgrupo = FM.id_subgrupo 
                INNER JOIN far_grupos G ON G.id_grupo = SG.id_grupo" . $where;
    $rs = $cmd->query($sql);
    $total = $rs->fetch();
    $totalRecordsFilter = $total['total'];

    //Consulta los datos para listarlos en la tabla
    $sql = "SELECT FM.id_med,
                FM.cod_medicamento,
                FM.nom_medicamento,
                FM.existencia,
                FM.val_promedio
            FROM far_medicamentos FM 
            INNER JOIN far_subgrupos SG ON SG.id_subgrupo = FM.id_subgrupo 
            INNER JOIN far_grupos G ON G.id_grupo = SG.id_grupo" . $where . " ORDER BY $col $dir $limit";


    $rs = $cmd->query($sql);
    $objs = $rs->fetchAll();
    $cmd = null;
} catch (PDOException $e) {
    echo $e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage();
}

$data = [];
if (!empty($objs)) {
    foreach ($objs as $obj) {
        $data[] = [
            "id_med" => $obj['id_med'],
            "cod_medicamento" => $obj['cod_medicamento'],
            "nom_medicamento" => $obj['nom_medicamento'],            
            "existencia" => $obj['existencia'],
            "val_promedio" => formato_valor($obj['val_promedio']),
        ];
    }
}
$datos = [
    "data" => $data,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecordsFilter
];

echo json_encode($datos);

   