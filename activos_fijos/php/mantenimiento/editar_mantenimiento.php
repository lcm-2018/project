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

    if ((PermisosUsuario($permisos, 5006, 2) && $oper == 'add' && $_POST['id_detalle'] == -1) ||
        (PermisosUsuario($permisos, 5006, 3) && $oper == 'add' && $_POST['id_detalle'] != -1) ||
        (PermisosUsuario($permisos, 5006, 4) && $oper == 'del') || $id_rol == 1
    ) {

        $id = isset($_POST['id_mantenimiento']) ? $_POST['id_mantenimiento'] : -1;

        if ($oper == 'add') {
            if ($id == -1) {
                
                $sql = "INSERT INTO acf_mantenimiento (
                    fecha_mantenimiento, hora_mantenimiento, 
                    observaciones, tipo_mantenimiento, id_responsable, 
                    id_tercero, fecha_inicio_mantenimiento, 
                    fecha_fin_mantenimiento, estado, 
                    fecha_creacion, usuaro_creacion, 
                    fecha_aprobacion, usuario_aprobacion, 
                    fecha_ejecucion, usuario_ejecucion
                ) VALUES (
                    :fecha_mantenimiento, :hora_mantenimiento, 
                    :observaciones, :tipo_mantenimiento, :id_responsable, 
                    :id_tercero, :fecha_inicio_mantenimiento, 
                    :fecha_fin_mantenimiento, :estado, 
                    :fecha_creacion, :usuaro_creacion, 
                    :fecha_aprobacion, :usuario_aprobacion, 
                    :fecha_ejecucion, :usuario_ejecucion
                )";
        
                $sql = $cmd->prepare($sql);
                
                $sql->bindParam(':fecha_mantenimiento', $_POST['fecha_mantenimiento']);
                $sql->bindParam(':hora_mantenimiento', $_POST['hora_mantenimiento']);
                $sql->bindParam(':observaciones', $_POST['observaciones']);
                $sql->bindParam(':tipo_mantenimiento', $_POST['tipo_mantenimiento'], PDO::PARAM_INT);
                $sql->bindParam(':id_responsable', $_POST['id_responsable'], PDO::PARAM_INT);
                $sql->bindParam(':id_tercero', $_POST['id_tercero'], PDO::PARAM_INT);
                $sql->bindParam(':fecha_inicio_mantenimiento', $_POST['fecha_inicio_mantenimiento']);
                $sql->bindParam(':fecha_fin_mantenimiento', $_POST['fecha_fin_mantenimiento']);
                $sql->bindParam(':estado', $_POST['estado'], PDO::PARAM_INT);
                $sql->bindParam(':fecha_creacion', $fecha_crea);
                $sql->bindParam(':usuaro_creacion', $id_usr_crea, PDO::PARAM_INT);
                $sql->bindValue(':fecha_aprobacion', null);
                $sql->bindValue(':usuario_aprobacion', null, PDO::PARAM_INT);
                $sql->bindValue(':fecha_ejecucion', null);
                $sql->bindValue(':usuario_ejecucion', null, PDO::PARAM_NULL);

                $inserted = $sql->execute();

                if ($inserted) {
                    $id = $cmd->lastInsertId();
                    $res['mensaje'] = 'ok';
                    $res['id_mantenimiento'] = $id;
                } else {
                    $res['mensaje'] = $sql->errorInfo()[2];
                }   
            } else {
            
                $sql = "UPDATE acf_mantenimiento SET
                            fecha_mantenimiento = :fecha_mantenimiento,
                            hora_mantenimiento = :hora_mantenimiento,
                            observaciones = :observaciones,
                            tipo_mantenimiento = :tipo_mantenimiento,
                            id_responsable = :id_responsable,
                            id_tercero = :id_tercero,
                            fecha_inicio_mantenimiento = :fecha_inicio_mantenimiento,
                            fecha_fin_mantenimiento = :fecha_fin_mantenimiento,
                            fecha_aprobacion = :fecha_aprobacion,
                            usuario_aprobacion = :usuario_aprobacion,
                            fecha_ejecucion = :fecha_ejecucion,
                            usuario_ejecucion = :usuario_ejecucion
                        WHERE id_mantenimiento = :id_mantenimiento";

                $sql = $cmd->prepare($sql);
                $sql->bindParam(':fecha_mantenimiento', $_POST['fecha_mantenimiento']);
                $sql->bindParam(':hora_mantenimiento', $_POST['hora_mantenimiento']);
                $sql->bindParam(':observaciones', $_POST['observaciones']);
                $sql->bindParam(':tipo_mantenimiento', $_POST['tipo_mantenimiento'], PDO::PARAM_INT);
                $sql->bindParam(':id_responsable', $_POST['id_responsable'], PDO::PARAM_INT);
                $sql->bindParam(':id_tercero', $_POST['id_tercero'], PDO::PARAM_INT);
                $sql->bindParam(':fecha_inicio_mantenimiento', $_POST['fecha_inicio_mantenimiento']);
                $sql->bindParam(':fecha_fin_mantenimiento', $_POST['fecha_fin_mantenimiento']);
                $sql->bindValue(':fecha_aprobacion', null);
                $sql->bindValue(':usuario_aprobacion', null, PDO::PARAM_INT);
                $sql->bindValue(':fecha_ejecucion', null);
                $sql->bindValue(':usuario_ejecucion', null, PDO::PARAM_NULL);
                $sql->bindParam(':id_mantenimiento', $id, PDO::PARAM_INT);

                
                $updated = $sql->execute();

                if ($updated) {
                    $res['mensaje'] = 'ok';
                    $res['id_mantenimiento'] = $id;
                } else {
                    $res['mensaje'] = $sql->errorInfo()[2];
                }
            }

            if ($oper == 'del') {
                $id = $_POST['id_mantenimiento'];
                $sql = "DELETE FROM acf_orden_ingrehso_detalle WHERE id_ing_detalle=" . $id;
                $rs = $cmd->query($sql);
                if ($rs) {
                    $res['mensaje'] = 'ok';
                } else {
                    $res['mensaje'] = $cmd->errorInfo()[2];
                }
            }    

        } else {
            $res['mensaje'] = 'El Usuario del Sistema no tiene Permisos para esta Acción';
        }
    }
    $cmd = null;
} catch (PDOException $e) {
    $res['mensaje'] = $e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage();
}
echo json_encode($res);
