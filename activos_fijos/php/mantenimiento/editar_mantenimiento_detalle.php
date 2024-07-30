<?php
session_start();
if (!isset($_SESSION['user'])) {
    echo '<script>window.location.replace("../../../index.php");</script>';
    exit();
}
include '../../../conexion.php';
include '../../../permisos.php';
//Permisos: 1-Consultar,2-Crear,3-Editar,4-Eliminar,5-Anular,6-Imprimir
include '../common/funciones_generales.php';

$oper = isset($_POST['oper']) ? $_POST['oper'] : exit('Acción no permitida');
$fecha_crea = date('Y-m-d H:i:s');
$id_usr_crea = $_SESSION['id_user'];
$res = array();

try {
    $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
    $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

    if ((PermisosUsuario($permisos, 5703, 2) && $oper == 'add' && $_POST['id_detalle_mantenimiento'] == -1) 
        || (PermisosUsuario($permisos, 5703, 3) && $oper == 'add' && $_POST['id_detalle_mantenimiento'] != -1) 
        || ($oper == 'del') 
        || $id_rol == 1
    ) {

        $id = isset($_POST['id_detalle_mantenimiento']) ? $_POST['id_detalle_mantenimiento'] : -1;

        if ($oper == 'add') {
            if ($id == -1) {
                
                $sql = "INSERT INTO acf_mantenimiento_detalle (
                    id_mantenimiento, 
                    id_activo_fijo, 
                    observacion_mantenimiento, 
                    estado_fin_mantenimiento, 
                    observacio_fin_mantenimiento, 
                    estado
                ) VALUES (
                    :id_mantenimiento, 
                    :id_activo_fijo, 
                    :observacion_mantenimiento, 
                    :estado_fin_mantenimiento, 
                    :observacio_fin_mantenimiento, 
                    :estado
                )";
        
                $sql = $cmd->prepare($sql);
                
                $sql->bindParam(':id_mantenimiento', $_POST['id_mantenimiento'], PDO::PARAM_INT);
                $sql->bindParam(':id_activo_fijo', $_POST['id_txt_activo_fijo'], PDO::PARAM_INT);
                $sql->bindParam(':observacion_mantenimiento', $_POST['observacion_mantenimiento'], PDO::PARAM_STR);
                $sql->bindParam(':estado_fin_mantenimiento', $_POST['estado_fin']);
                $sql->bindParam(':observacio_fin_mantenimiento', $_POST['observacio_fin_mantenimiento'], PDO::PARAM_STR);
                $sql->bindParam(':estado', $_POST['estado_detalle']);

                $inserted = $sql->execute();

                if ($inserted) {
                    $id = $cmd->lastInsertId();
                    $res['mensaje'] = 'ok';
                    $res['id_detalle_mantenimiento'] = $id;
                } else {
                    $res['mensaje'] = $sql->errorInfo()[2];
                }   
            } else {
            
                $sql = "UPDATE `acf_mantenimiento_detalle` 
                        SET 
                            `observacion_mantenimiento` = :observacion_mantenimiento, 
                            `estado_fin_mantenimiento` = :estado_fin_mantenimiento, 
                            `observacio_fin_mantenimiento` = :observacio_fin_mantenimiento, 
                            `estado` = :estado
                        WHERE `id_detalle_mantenimiento` = :id_detalle_mantenimiento";

                $sql = $cmd->prepare($sql);

                $sql->bindParam(':id_activo_fijo', $_POST['id_txt_activo_fijo'], PDO::PARAM_INT);
                $sql->bindParam(':observacion_mantenimiento', $_POST['observacion_mantenimiento']);
                $sql->bindParam(':estado_fin_mantenimiento', $_POST['estado_fin_mantenimiento']);
                $sql->bindParam(':observacio_fin_mantenimiento', $_POST['observacio_fin_mantenimiento']);
                $sql->bindParam(':estado', $_POST['estado_detalle']);
                $sql->bindParam(':id_detalle_mantenimiento', $_POST['id_detalle_mantenimiento'], PDO::PARAM_INT);
                
                $updated = $sql->execute();

                if ($updated) {
                    $res['mensaje'] = 'ok';
                    $res['id_detalle_mantenimiento'] = $id;
                } else {
                    $res['mensaje'] = $sql->errorInfo()[2];
                }
            }
        }

        if ($oper == 'del') {
            $sql = "SELECT estado FROM acf_mantenimiento WHERE id_mantenimiento=" . $id;
            $rs = $cmd->query($sql);
            $obj = $rs->fetch();

            if ($obj['estado'] == 1) {
                $sql = "DELETE FROM acf_mantenimiento WHERE id_mantenimiento=" . $id;
                $rs = $cmd->query($sql);
                if ($rs) {
                    $res['mensaje'] = 'ok';
                } else {
                    $res['mensaje'] = $cmd->errorInfo()[2];
                }
            } else {
                $res['mensaje'] = 'Solo puede Borrar Ordenes de Mantenimiento en estado Pendiente' ;
            }
        }

    } else {
        $res['mensaje'] = 'El Usuario del Sistema no tiene Permisos para esta Acción';
    }
    
    $cmd = null;

} catch (PDOException $e) {
    $res['mensaje'] = $e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage();
}
echo json_encode($res);
