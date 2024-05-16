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

    if ((PermisosUsuario($permisos, 5008, 2) && $oper == 'add' && $_POST['id_detalle'] == -1) ||
        (PermisosUsuario($permisos, 5008, 3) && $oper == 'add' && $_POST['id_detalle'] != -1) ||
        (PermisosUsuario($permisos, 5008, 4) && $oper == 'del') || $id_rol == 1
    ) {

        $id_traslado = $_POST['id_traslado'];

        if ($id_traslado > 0) {

            $sql = "SELECT estado FROM far_traslado WHERE id_traslado=" . $id_traslado;
            $rs = $cmd->query($sql);
            $obj_traslado = $rs->fetch();

            if ($obj_traslado['estado'] == 1) {
                if ($oper == 'add') {
                    $id = $_POST['id_detalle'];
                    $id_lote = $_POST['id_txt_nom_lot'];
                    $cantidad = $_POST['txt_can_tra'] ? $_POST['txt_can_tra'] : 1;
                    $valor = $_POST['txt_val_pro'] ? $_POST['txt_val_pro'] : 0;

                    $sql = "SELECT existencia FROM far_medicamento_lote WHERE id_lote=" . $id_lote;
                    $rs = $cmd->query($sql);
                    $obj_det = $rs->fetch();

                    if ($obj_det['existencia'] >= $cantidad) {
                        if ($id == -1) {
                            $sql = "SELECT COUNT(*) AS existe FROM far_traslado_detalle WHERE id_traslado=$id_traslado AND id_lote_origen=" . $id_lote;
                            $rs = $cmd->query($sql);
                            $obj = $rs->fetch();
                            if ($obj['existe'] == 0) {

                                $sql = "INSERT INTO far_traslado_detalle(id_traslado,id_lote_origen,cantidad,valor)
                                VALUES($id_traslado,$id_lote,$cantidad,$valor)";
                                $rs = $cmd->query($sql);

                                if ($rs) {
                                    $res['mensaje'] = 'ok';
                                    $sql_i = 'SELECT LAST_INSERT_ID() AS id';
                                    $rs = $cmd->query($sql_i);
                                    $obj = $rs->fetch();
                                    $res['id'] = $obj['id'];
                                } else {
                                    $res['mensaje'] = $cmd->errorInfo()[2];
                                }
                            } else {
                                $res['mensaje'] = 'El Lote ya existe en los detalles del Traslado';
                            }
                        } else {
                            $sql = "UPDATE far_traslado_detalle 
                                SET cantidad=$cantidad
                                WHERE id_tra_detalle=" . $id;

                            $rs = $cmd->query($sql);
                            if ($rs) {
                                $res['mensaje'] = 'ok';
                                $res['id'] = $id;
                            } else {
                                $res['mensaje'] = $cmd->errorInfo()[2];
                            }
                        }
                    } else {
                        $res['mensaje'] = 'La Cantidad a Egresar es mayor a la Existencia';
                    }
                }

                if ($oper == 'del') {
                    $id = $_POST['id'];
                    $sql = "DELETE FROM far_traslado_detalle WHERE id_tra_detalle=" . $id;
                    $rs = $cmd->query($sql);
                    if ($rs) {
                        $res['mensaje'] = 'ok';
                    } else {
                        $res['mensaje'] = $cmd->errorInfo()[2];
                    }
                }

                if ($res['mensaje'] == 'ok') {
                    $sql = "UPDATE far_traslado SET val_total=(SELECT SUM(valor*cantidad) FROM far_traslado_detalle WHERE id_traslado=$id_traslado) WHERE id_traslado=$id_traslado";
                    $rs = $cmd->query($sql);

                    $sql = "SELECT val_total FROM far_traslado WHERE id_traslado=" . $id_traslado;
                    $rs = $cmd->query($sql);
                    $obj_traslado = $rs->fetch();
                    $res['val_total'] = formato_valor($obj_traslado['val_total']);
                }
            } else {
                $res['mensaje'] = 'Solo puede Modificar Traslados en estado Pendiente';
            }
        } else {
            $res['mensaje'] = 'Primero debe guardar el Traslado';
        }
    } else {
        $res['mensaje'] = 'El Usuario del Sistema no tiene Permisos para esta Acción';
    }

    $cmd = null;
} catch (PDOException $e) {
    $res['mensaje'] = $e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage();
}
echo json_encode($res);
