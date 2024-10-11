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

    if ((PermisosUsuario($permisos, 5703, 2) && $oper == 'add' && $_POST['id_mantenimiento'] == -1) 
        || (PermisosUsuario($permisos, 5703, 3) && $oper == 'add' && $_POST['id_mantenimiento'] != -1) 
        || ($oper == 'del') 
        || $id_rol == 1
    ) {

        $id = isset($_POST['id_mantenimiento']) ? $_POST['id_mantenimiento'] : -1;

        if ($oper == 'add') {
            if ($id == -1) {
                
                $sql = "INSERT INTO acf_baja 
                    (fecha_orden, hora_orden, observaciones, estado, fecha_baja, usuario_crea, usuario_cierra) 
                    VALUES (:fecha_orden, :hora_orden, :observaciones, :estado, :fecha_baja, :usuario_crea, :usuario_cierra)";

                $stmt = $conn->prepare($sql);

                $stmt->bindParam(':fecha_orden', $_POST['fecha_orden']);
                $stmt->bindParam(':hora_orden', $_POST['hora_orden']);
                $stmt->bindParam(':observaciones', $_POST['observaciones']);
                $stmt->bindParam(':estado', 1, PDO::PARAM_INT);
                $stmt->bindParam(':fecha_baja', $fecha_crea);
                $stmt->bindParam(':usuario_crea', $id_usr_crea, PDO::PARAM_INT);
                $stmt->bindParam(':usuario_cierra', null, PDO::PARAM_INT);

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

        if ($oper == 'aprobar') {

            $sql = "UPDATE acf_mantenimiento SET estado = :estado WHERE id_mantenimiento = :id_mantenimiento";

            $APROBADO = 2;
            $sql = $cmd->prepare($sql);
            $sql->bindParam(':estado', $APROBADO, PDO::PARAM_INT);
            $sql->bindParam(':id_mantenimiento', $id, PDO::PARAM_INT);
   
            $updated = $sql->execute();

            $sql = "UPDATE acf_hojavida HV INNER JOIN acf_mantenimiento_detalle AFMD ON HV.id_activo_fijo = AFMD.id_activo_fijo
                    SET HV.estado = :estado
                    WHERE AFMD.id_mantenimiento = :id_mantenimiento;";

            $PARA_MANTENIMIENTO = 2;
            $sql = $cmd->prepare($sql);
            $sql->bindParam(':estado', $PARA_MANTENIMIENTO, PDO::PARAM_INT);
            $sql->bindParam(':id_mantenimiento', $id, PDO::PARAM_INT);

            $updated = $sql->execute();

            if ($updated) {
                $res['mensaje'] = 'ok';
                $res['id_mantenimiento'] = $id;
            } else {
                $res['mensaje'] = $sql->errorInfo()[2];
            }

        }

        if ($oper == 'ejecutar') {

            $sql = "UPDATE acf_mantenimiento SET estado = :estado WHERE id_mantenimiento = :id_mantenimiento";

            $EN_EJECUCON = 3;
            $sql = $cmd->prepare($sql);
            $sql->bindParam(':estado', $EN_EJECUCON, PDO::PARAM_INT);
            $sql->bindParam(':id_mantenimiento', $id, PDO::PARAM_INT);
   
            $updated = $sql->execute();

            $sql = "UPDATE acf_hojavida HV INNER JOIN acf_mantenimiento_detalle AFMD ON HV.id_activo_fijo = AFMD.id_activo_fijo
                    SET HV.estado = :estado
                    WHERE AFMD.id_mantenimiento = :id_mantenimiento;";

            $EN_MANTENIMIENTO = 3;
            $sql = $cmd->prepare($sql);
            $sql->bindParam(':estado', $EN_MANTENIMIENTO, PDO::PARAM_INT);
            $sql->bindParam(':id_mantenimiento', $id, PDO::PARAM_INT);

            $updated = $sql->execute();

            if ($updated) {
                $res['mensaje'] = 'ok';
                $res['id_mantenimiento'] = $id;
            } else {
                $res['mensaje'] = $sql->errorInfo()[2];
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
