<?php
session_start();
if (!isset($_SESSION['user'])) {
    echo '<script>window.location.replace("../../index.php");</script>';
    exit();
}

include '../../../conexion.php';
include '../common/funciones_generales.php';

$cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
$cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

$where = "WHERE far_orden_egreso.id_egreso<>0";
if (isset($_POST['id_sede']) && $_POST['id_sede']) {
    $where .= " AND far_orden_egreso.id_sede='" . $_POST['id_sede'] . "'";
}
if (isset($_POST['id_bodega']) && $_POST['id_bodega']) {
    $where .= " AND far_orden_egreso.id_bodega='" . $_POST['id_bodega'] . "'";
}
if (isset($_POST['id_egr']) && $_POST['id_egr']) {
    $where .= " AND far_orden_egreso.id_egreso='" . $_POST['id_egr'] . "'";
}
if (isset($_POST['num_egr']) && $_POST['num_egr']) {
    $where .= " AND far_orden_egreso.num_egreso='" . $_POST['num_egr'] . "'";
}
if (isset($_POST['fec_ini']) && $_POST['fec_ini'] && isset($_POST['fec_fin']) && $_POST['fec_fin']) {
    $where .= " AND far_orden_egreso.fec_egreso BETWEEN '" . $_POST['fec_ini'] . "' AND '" . $_POST['fec_fin'] . "'";
}
if (isset($_POST['id_tercero']) && $_POST['id_tercero']) {
    $where .= " AND far_orden_egreso.id_cliente=" . $_POST['id_tercero'] . "";
}
if (isset($_POST['id_depende']) && $_POST['id_depende']) {
    $where .= " AND far_orden_egreso.id_dependencia=" . $_POST['id_depende'] . "";
}
if (isset($_POST['id_tipegr']) && $_POST['id_tipegr']) {
    $where .= " AND far_orden_egreso.id_tipo_egreso=" . $_POST['id_tipegr'] . "";
}
if (isset($_POST['estado']) && strlen($_POST['estado'])) {
    $where .= " AND far_orden_egreso.estado=" . $_POST['estado'];
}

try {
    $sql = "SELECT far_orden_egreso.id_egreso,far_orden_egreso.num_egreso,far_orden_egreso.fec_egreso,far_orden_egreso.hor_egreso,
                    far_orden_egreso.detalle,tb_terceros.nom_tercero,tb_centrocostos.nom_centro,
                    far_orden_egreso_tipo.nom_tipo_egreso,far_orden_egreso.val_total,tb_sedes.nom_sede,far_bodegas.nombre AS nom_bodega,
                    CASE far_orden_egreso.estado WHEN 1 THEN 'PENDIENTE' WHEN 2 THEN 'CERRADO' WHEN 0 THEN 'ANULADO' END AS nom_estado
                    FROM far_orden_egreso
                    INNER JOIN far_orden_egreso_tipo ON (far_orden_egreso_tipo.id_tipo_egreso=far_orden_egreso.id_tipo_egreso)
                    INNER JOIN tb_terceros ON (tb_terceros.id_tercero=far_orden_egreso.id_cliente)
                    INNER JOIN tb_centrocostos ON (tb_centrocostos.id_centro=far_orden_egreso.id_centrocosto)
                    INNER JOIN tb_sedes ON (tb_sedes.id_sede=far_orden_egreso.id_sede)
                    INNER JOIN far_bodegas ON (far_bodegas.id_bodega=far_orden_egreso.id_bodega)$where ORDER BY far_orden_egreso.id_egreso DESC";
    $res = $cmd->query($sql);
    $objs = $res->fetchAll();
} catch (PDOException $e) {
    echo $e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode();
}
?>
<div class="text-right py-3">
    <a type="button" id="btnExcelEntrada" class="btn btn-outline-success btn-sm" value="01" title="Exprotar a Excel">
        <span class="fas fa-file-excel fa-lg" aria-hidden="true"></span>
    </a>
    <a type="button" class="btn btn-primary btn-sm" id="btnImprimir">Imprimir</a>
    <a type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"> Cerrar</a>
</div>
<div class="content bg-light" id="areaImprimir">
    <style>
        @media print {
            body {
                font-family: Arial, sans-serif;
            }
        }
        .resaltar:nth-child(even) {
            background-color: #F8F9F9;
        }
        .resaltar:nth-child(odd) {
            background-color: #ffffff;
        }
    </style>

    <?php include('../common/reporte_header.php'); ?>

    <table style="width:100%; font-size:80%">
        <tr style="text-align:center">
            <th>REPORTE DE ORDENES DE EGRESO ENTRE: <?php echo $_POST['fec_ini'].' y '. $_POST['fec_fin'] ?></th>
        </tr>     
    </table>

    <table style="width:100% !important">
        <thead style="font-size:80%">                
            <tr style="background-color:#CED3D3; color:#000000; text-align:center">
                <th>Id</th>
                <th>No. Egreso</th>
                <th>Fecha Egreso</th>
                <th>Hora Egreso</th>
                <th>Detalle</th>
                <th>Tercero</th>
                <th>Centro Costo</th>
                <th>Tipo Egreso</th>
                <th>Vr. Total</th>
                <th>Sede</th>
                <th>Bodega</th>
                <th>Estado</th>
            </tr>    
        </thead>
        <tbody style="font-size: 60%;">
            <?php
            $tabla = '';
            foreach ($objs as $obj) {
                $tabla .=  '<tr class="resaltar" style="text-align:center"> 
                        <td>' . $obj['id_egreso'] . '</td>  
                        <td>' . $obj['num_egreso'] . '</td>
                        <td>' . $obj['fec_egreso'] . '</td>
                        <td>' . $obj['hor_egreso'] . '</td>                  
                        <td style="text-align:left">' . $obj['detalle'] . '</td>                      
                        <td style="text-align:left">' . mb_strtoupper($obj['nom_tercero']) . '</td>   
                        <td style="text-align:left">' . mb_strtoupper($obj['nom_centro']) . '</td> 
                        <td>' . mb_strtoupper($obj['nom_tipo_egreso']) . '</td>   
                        <td>' . formato_valor($obj['val_total']). '</td>                             
                        <td>' . mb_strtoupper($obj['nom_sede']) . '</td>   
                        <td>' . mb_strtoupper($obj['nom_bodega']) . '</td>   
                        <td>' . $obj['nom_estado']. '</td></tr>';
            }
            echo $tabla;
            ?>            
        </tbody>
        <tfoot style="font-size:60%"> 
            <tr style="background-color:#CED3D3; color:#000000">
                <td colspan="12" style="text-align:left">
                    No. de Registros: <?php echo count($objs); ?> 
                </td>
            </tr>
        </tfoot>
    </table>
</div>