<?php
session_start();
if (!isset($_SESSION['user'])) {
    echo '<script>window.location.replace("../../index.php");</script>';
    exit();
}
$data = isset($_POST['xls']) ? $_POST['xls'] : exit('Acción no permitida');
$data = base64_decode($data);
header('Content-type:application/xls');
header('Content-Disposition: attachment; filename=reporte_excel.xls');
echo $data;
