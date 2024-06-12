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

        $id_ingreso_detalle = $_POST['id_ingreso_detalle'];

        if ($id_ingreso_detalle > 0) {
            if ($oper == 'add') {

                $placa = $_POST['txt_placa'];
                $serial = $_POST['txt_serial'];
                $marca = $_POST['sl_marca'];
                $valor = $_POST['txt_val_uni'];
                $tipoactivo = $_POST['sl_tipoactivo'];

                if ($placa == -1) {
                    $sql = "SELECT COUNT(*) AS existe FROM acf_orden_ingreso_detalle WHERE id_orden_ingreso=$id_ingreso AND id_medicamento_articulo=" . $id_med;
                    $rs = $cmd->query($sql);
                    $obj = $rs->fetch();

                    if ($obj['existe'] == 0) {
                        $sql = "INSERT INTO acf_orden_ingreso_detalle1(
                                    id_orden_ingreso,
                                    id_medicamento_articulo,
                                    observacion,
                                    cantidad,
                                    valor_sin_iva,
                                    iva,
                                    valor
                                )
                                VALUES($id_ingreso,$id_med,'$observacion',$cantidad,$vr_unidad,$iva,$vr_costo)";
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
                        $res['mensaje'] = 'El activo ya existe en los detalles de la Orden de Ingreso';
                    }
                } else {
                    $sql = "UPDATE acf_activofijo
                            SET serial=?, id_marca=?, valor=?, tipo_activo=?
                            WHERE placa=?" ;

                    $sql = $cmd->prepare($sql);
                    $sql->bindParam(1, $serial, PDO::PARAM_STR);
                    $sql->bindParam(2, $marca, PDO::PARAM_INT);
                    $sql->bindParam(3, $valor, PDO::PARAM_STR);
                    $sql->bindParam(4, $tipoactivo, PDO::PARAM_INT);
                    $sql->bindParam(5, $placa, PDO::PARAM_STR);
                    $updated = $sql->execute();

                    if ($updated) {
                        $res['mensaje'] = 'ok';
                        $res['placa'] = $placa;
                        $res['id_ingreso_detalle'] = $id_ingreso_detalle;
                    } else {
                        $res['mensaje'] = $sql->errorInfo()[2];
                    }
                }
            }

            if ($oper == 'del') {
                $id = $_POST['id'];
                $sql = "DELETE FROM acf_orden_ingreso_detalle WHERE id_ing_detalle=" . $id;
                $rs = $cmd->query($sql);
                if ($rs) {
                    $res['mensaje'] = 'ok';
                } else {
                    $res['mensaje'] = $sql->errorInfo()[2];
                }
            }
        } else {
            $res['mensaje'] = 'Primero debe guardar el detalle de la Orden de Ingreso';
        }
    } else {
        $res['mensaje'] = 'El Usuario del Sistema no tiene Permisos para esta Acción';
    }

    $cmd = null;
} catch (PDOException $e) {
    $res['mensaje'] = $e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage();
}
echo json_encode($res);
