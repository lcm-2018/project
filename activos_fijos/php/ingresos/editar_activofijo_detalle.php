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

                $idArticulo = $_POST['id_articulo'];
                $placa = $_POST['placa'];
                $txtPlaca = $_POST['txt_placa'];
                $serial = $_POST['txt_serial'];
                $marca = $_POST['sl_marca'];
                $valor = $_POST['txt_val_uni'];
                $tipoactivo = $_POST['sl_tipoactivo'];

                if ($placa == -1) {
                    $sql = "SELECT COUNT(*) AS existe FROM acf_activofijo_ordeningresodetalle WHERE id_ordeningresodetalle=$id_ingreso_detalle AND placa_activofijo=" . $txtPlaca;
                    $rs = $cmd->query($sql);
                    $obj = $rs->fetch();

                    if ($obj['existe'] == 0) {

                        $cmd->beginTransaction();

                        $sql = "INSERT INTO acf_activofijo(placa, serial, id_marca, valor,tipo_activo, id_articulo) VALUES(?,?,?,?,?,?)";
                        $sql = $cmd->prepare($sql);
                        $sql->bindParam(1, $txtPlaca, PDO::PARAM_STR);
                        $sql->bindParam(2, $serial, PDO::PARAM_STR);
                        $sql->bindParam(3, $marca, PDO::PARAM_INT);
                        $sql->bindParam(4, $valor, PDO::PARAM_STR);
                        $sql->bindParam(5, $tipoactivo, PDO::PARAM_INT);
                        $sql->bindParam(6, $idArticulo, PDO::PARAM_INT);
                        $inserted = $sql->execute();

                        $sql = "INSERT INTO acf_activofijo_ordeningresodetalle(id_ordeningresodetalle, placa_activofijo) VALUES(?,?)";
                        $sql = $cmd->prepare($sql);
                        $sql->bindParam(1, $id_ingreso_detalle, PDO::PARAM_INT);
                        $sql->bindParam(2, $txtPlaca, PDO::PARAM_STR);
                        $inserted = $sql->execute();

                        if ($inserted) {
                            $cmd->commit();
                            $res['mensaje'] = 'ok';
                            $res['placa'] = $txtPlaca;
                            $res['id_ingreso_detalle'] = $id_ingreso_detalle;
                        } else {
                            $res['mensaje'] = $sql->errorInfo()[2];
                            $cmd->rollBack();
                        }

                    } else {
                        $res['mensaje'] = 'El activo ya existe en los detalles de la Orden de Ingreso';
                    }
                } else {
                    $sql = "UPDATE acf_activofijo
                            SET serial=?, id_marca=?, valor=?, tipo_activo=?, id_articulo=?
                            WHERE placa=?" ;

                    $sql = $cmd->prepare($sql);
                    $sql->bindParam(1, $serial, PDO::PARAM_STR);
                    $sql->bindParam(2, $marca, PDO::PARAM_INT);
                    $sql->bindParam(3, $valor, PDO::PARAM_STR);
                    $sql->bindParam(4, $tipoactivo, PDO::PARAM_INT);
                    $sql->bindParam(5, $idArticulo, PDO::PARAM_INT);
                    $sql->bindParam(6, $txtPlaca, PDO::PARAM_STR);
                    $updated = $sql->execute();

                    if ($updated) {
                        $res['mensaje'] = 'ok';
                        $res['placa'] = $txtPlaca;
                        $res['id_ingreso_detalle'] = $id_ingreso_detalle;
                    } else {
                        $res['mensaje'] = $sql->errorInfo()[2];
                    }
                }
            }

            if ($oper == 'del') {
                $placa = $_POST['placa'];

                $cmd->beginTransaction();

                $sql = "DELETE FROM acf_activofijo WHERE placa=?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $placa, PDO::PARAM_STR);
                $deleted = $sql->execute();
                

                $sql = "DELETE FROM acf_activofijo_ordeningresodetalle WHERE id_ordeningresodetalle=? AND placa_activofijo=?" ;
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $id_ingreso_detalle, PDO::PARAM_INT);
                $sql->bindParam(2, $placa, PDO::PARAM_STR);
                $deleted = $sql->execute();
                
                
                if ($deleted) {
                    $cmd->commit();
                    $res['mensaje'] = 'ok';
                } else {
                    $cmd->rollBack();
                    $res['mensaje'] = $cmd->errorInfo()[2];
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
