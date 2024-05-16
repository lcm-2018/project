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
$idusr = $_SESSION['id_user'];
$idrol = $_SESSION['rol'];

$where_usr = " WHERE 1";
if($idrol !=1){
    $where_usr .= " AND far_kardex.id_bodega IN (SELECT id_bodega FROM seg_bodegas_usuario WHERE id_usuario=$idusr)";
}

$fecha = $_POST['fecha'] ? $_POST['fecha'] : date('Y-m-d');

$where_kar = " AND far_kardex.estado=1";
if (isset($_POST['id_sede']) && $_POST['id_sede']) {
    $where_kar .= " AND far_kardex.id_sede='" . $_POST['id_sede'] . "'";
}
if (isset($_POST['id_bodega']) && $_POST['id_bodega']) {
    $where_kar .= " AND far_kardex.id_bodega='" . $_POST['id_bodega'] . "'";
}
if (isset($_POST['fecha']) && $_POST['fecha']) {
    $where_kar .= " AND far_kardex.fec_movimiento<='" . $_POST['fecha'] . "'";
}

$where_art = " WHERE 1";
if (isset($_POST['codigo']) && $_POST['codigo']) {
    $where_art .= " AND far_medicamentos.cod_medicamento LIKE '" . $_POST['codigo'] . "%'";
}
if (isset($_POST['nombre']) && $_POST['nombre']) {
    $where_art .= " AND far_medicamentos.nom_medicamento LIKE '" . $_POST['nombre'] . "%'";
}
if (isset($_POST['id_subgrupo']) && $_POST['id_subgrupo']) {
    $where_art .= " AND far_medicamentos.id_subgrupo=" . $_POST['id_subgrupo'];
}
if (isset($_POST['artactivo']) && $_POST['artactivo']) {
    $where_art .= " AND far_medicamentos.estado=1";
}
if (isset($_POST['conexistencia']) && $_POST['conexistencia']) {
    $where_art .= " AND e.existencia_fecha>=1";
}


try {
    $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
    $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

    //Consulta el total de registros de la tabla
    $sql = "SELECT COUNT(*) AS total
            FROM far_medicamentos
            INNER JOIN (SELECT id_med FROM far_kardex
                        WHERE id_kardex IN (SELECT MAX(id_kardex) FROM far_kardex $where_usr GROUP BY id_lote)                        
                        GROUP BY id_med	
                        ) AS e ON (e.id_med = far_medicamentos.id_med)";        
    $rs = $cmd->query($sql);
    $total = $rs->fetch();
    $totalRecords = $total['total'];

    //Consulta el total de registros aplicando el filtro
    $sql = "SELECT COUNT(*) AS total
            FROM far_medicamentos
            INNER JOIN far_subgrupos ON (far_subgrupos.id_subgrupo=far_medicamentos.id_subgrupo)
            INNER JOIN (SELECT id_med,SUM(existencia_lote) AS existencia_fecha FROM far_kardex
                        WHERE id_kardex IN (SELECT MAX(id_kardex) FROM far_kardex $where_usr $where_kar GROUP BY id_lote)                        
                        GROUP BY id_med	
                        ) AS e ON (e.id_med = far_medicamentos.id_med)	
            $where_art";
    $rs = $cmd->query($sql);
    $total = $rs->fetch();
    $totalRecordsFilter = $total['total'];

    //Consulta los datos para listarlos en la tabla
    $sql = "SELECT far_medicamentos.id_med,far_medicamentos.cod_medicamento,far_medicamentos.nom_medicamento,
                far_subgrupos.nom_subgrupo,e.existencia_fecha,v.val_promedio_fecha,
                (e.existencia_fecha*v.val_promedio_fecha) AS val_total
            FROM far_medicamentos
            INNER JOIN far_subgrupos ON (far_subgrupos.id_subgrupo=far_medicamentos.id_subgrupo)
            INNER JOIN (SELECT id_med,SUM(existencia_lote) AS existencia_fecha FROM far_kardex
                        WHERE id_kardex IN (SELECT MAX(id_kardex) FROM far_kardex $where_usr $where_kar GROUP BY id_lote)                        
                        GROUP BY id_med	
                        ) AS e ON (e.id_med = far_medicamentos.id_med)	
            INNER JOIN (SELECT id_med,val_promedio AS val_promedio_fecha FROM far_kardex
                        WHERE id_kardex IN (SELECT MAX(id_kardex) FROM far_kardex				
                                            WHERE fec_movimiento<='$fecha' AND estado=1 
                                            GROUP BY id_med)
                        ) AS v ON (v.id_med = far_medicamentos.id_med) 
            $where_art ORDER BY $col $dir $limit";
    $rs = $cmd->query($sql);
    $objs = $rs->fetchAll();
    $cmd = null;
} catch (PDOException $e) {
    echo $e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage();
}

$data = [];
if (!empty($objs)) {
    foreach ($objs as $obj) {
        $id = $obj['id_med'];
        $data[] = [
            "id_med" => $id,
            "cod_medicamento" => $obj['cod_medicamento'],
            "nom_medicamento" => mb_strtoupper($obj['nom_medicamento']),
            "nom_subgrupo" => mb_strtoupper($obj['nom_subgrupo']),
            "existencia_fecha" => $obj['existencia_fecha'],
            "val_promedio_fecha" => formato_valor($obj['val_promedio_fecha']),
            "val_total" => formato_valor($obj['val_total']),
        ];
    }
}
$datos = [
    "data" => $data,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecordsFilter
];

echo json_encode($datos);
