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
$fecha_hora_servidor = fecha_hora_servidor();

try {
    $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
    $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

    if ((PermisosUsuario($permisos, 5006, 2) && $oper == 'add' ||
        (PermisosUsuario($permisos, 5006, 4) && $oper == 'del') || $id_rol == 1)
    ) {

        $id_detalle_mantenimiento = $_POST['id_detalle_mantenimiento'];
        $id_nota = isset($_POST['id_nota_mantenimiento']) ? $_POST['id_nota_mantenimiento'] : -1;

        if ($oper == 'add') {

            $archivo_actual = $_POST['archivo'];
            $observaciones = isset($_POST['observaciones']) ? $_POST['observaciones'] : null;

            if ($id_nota == -1) {

                $sql = "INSERT INTO acf_detalle_mantenimiento_nota 
                    (id_detalle_mantenimiento, fecha, hora, observaciones, fecha_creacion, id_usuario_crea, archivo) 
                    VALUES 
                    (:id_detalle_mantenimiento, :fecha, :hora, :observaciones,:fecha_creacion, :id_usuario_crea, :archivo)";

                $sql = $cmd->prepare($sql);

                $nombre = 'temp_file';
                $sql->bindParam(':id_detalle_mantenimiento', $id_detalle_mantenimiento, PDO::PARAM_INT);
                $sql->bindParam(':fecha', $fecha_hora_servidor['fecha'], PDO::PARAM_STR);
                $sql->bindParam(':hora', $fecha_hora_servidor['hora'], PDO::PARAM_STR);
                $sql->bindParam(':observaciones', $observaciones, PDO::PARAM_STR);
                $sql->bindParam(':fecha_creacion', $fecha_crea, PDO::PARAM_STR);
                $sql->bindParam(':id_usuario_crea', $id_usr_crea, PDO::PARAM_INT);
                $sql->bindParam(':archivo', $nombre, PDO::PARAM_STR);

                $inserted = $sql->execute();

                if ($inserted) {
                    $id_nota = $cmd->lastInsertId();

                    $nombreImagenLocal =  $_FILES["uploadDocNota"]['name'];
                    $fileExtension = '.' . strtolower( pathinfo($nombreImagenLocal)['extension']);
                    $nombre = $id_nota . '_' .  date('Ymd_His') . $fileExtension;
                    $temporal = $_FILES['uploadDocNota']['tmp_name'];
                    $ruta = '../../imagenes/activos_fijos/';
                    if (!file_exists($ruta)) {
                        $ruta = mkdir($ruta, 0777, true);
                        $ruta = '../../imagenes/activos_fijos/';
                    }
                    if ((move_uploaded_file($temporal, $ruta . $nombre))) {
                        $sql = "UPDATE acf_detalle_mantenimiento_nota SET archivo = :archivo WHERE id = :id";
                        $sql = $cmd->prepare($sql);
                        $sql->bindValue(':archivo', $nombre);
                        $sql->bindValue(':id', $id_nota, PDO::PARAM_INT);
                        $updated = $sql->execute();
                        if ($updated) {
                            $res['mensaje'] = 'ok';
                            $res['id_detalle_mantenimiento'] = $id_detalle_mantenimiento;
                            $res['id_nota_mantenimiento'] = $id_nota;
                            $res['nombre_archivo'] = $nombre;
                        } else {
                            $res['mensaje'] = $sql->errorInfo()[2];
                        }
                    } 
                } else {
                    $res['mensaje'] = $sql->errorInfo()[2];
                }
                if ($updated) {
                    $res['mensaje'] = 'ok';
                    $res['id_nota_mantenimiento'] = $id_nota;
                    $res['nombre_archivo'] = $nombre;
                } else {
                    $res['mensaje'] = $sql->errorInfo()[2];
                }
            }  else {
                $nombre = $archivo_actual;

                if(isset($_FILES["uploadDocNota"])) {
                    
                    $nombreImagenLocal =  $_FILES["uploadDocNota"]['name'];
                    $fileExtension = '.' . strtolower( pathinfo($nombreImagenLocal)['extension']);
                    $nombre = $id_nota . '_' .  date('Ymd_His') . $fileExtension;
                    $temporal = $_FILES['uploadDocNota']['tmp_name'];
                    $ruta = '../../imagenes/activos_fijos/';

                    if (!file_exists($ruta)) {
                        $ruta = mkdir($ruta, 0777, true);
                        $ruta = '../../imagenes/activos_fijos/';
                    }
                    move_uploaded_file($temporal, $ruta . $nombre);
                }

                $sql = "UPDATE `acf_detalle_mantenimiento_nota` 
                        SET `fecha` = :fecha, `hora` = :hora, `observaciones` = :observaciones, `archivo` = :archivo
                        WHERE `id` = :id";

                $sql = $cmd->prepare($sql);

                $sql->bindParam(':id', $id_nota, PDO::PARAM_INT);
                $sql->bindParam(':fecha', $fecha_hora_servidor['fecha'], PDO::PARAM_STR);
                $sql->bindParam(':hora', $fecha_hora_servidor['hora'], PDO::PARAM_STR);
                $sql->bindParam(':observaciones', $observaciones, PDO::PARAM_STR);
                $sql->bindParam(':archivo', $nombre, PDO::PARAM_STR);

                $updated = $sql->execute();

                if ($updated) {
                    $res['mensaje'] = 'ok';
                    $res['id_nota_mantenimiento'] = $id_nota;
                    $res['nombre_archivo'] = $nombre;
                } else {
                    $res['mensaje'] = $sql->errorInfo()[2];
                }
                
            }

        } 

        if ($oper == 'del') {
            $sql = "DELETE FROM acf_detalle_mantenimiento_nota WHERE id=" . $id_nota;
            $rs = $cmd->query($sql);
            if ($rs) {
                $res['mensaje'] = 'ok';
            } else {
                $res['mensaje'] = $cmd->errorInfo()[2];
            }
        }
    }
    $cmd = null;
} catch (PDOException $e) {
    $res['mensaje'] = $e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage();
}
echo json_encode($res);
