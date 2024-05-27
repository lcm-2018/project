<?php
session_start();
if (!isset($_SESSION['user'])) {
    echo '<script>window.location.replace("../../../index.php");</script>';
    exit();
}
include '../../../conexion.php';
include '../../../permisos.php';
//Permite crear botones en la cuadricula si tiene permisos de 1-Consultar,2-Crear,3-Editar,4-Eliminar,5-Anular,6-Imprimir
//include '../common/funciones_kardex.php';
include '../common/funciones_generales.php';

$oper = isset($_POST['oper']) ? $_POST['oper'] : exit('Acción no permitida');
$fecha_ope = date('Y-m-d H:i:s');
$id_usr_ope = $_SESSION['id_user'];
$res = array();

try {
    $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
    $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);

    if ((PermisosUsuario($permisos, 5006, 2) && $oper == 'add' && $_POST['id_ingreso'] == -1) ||
        (PermisosUsuario($permisos, 5006, 3) && $oper == 'add' && $_POST['id_ingreso'] != -1) ||
        (PermisosUsuario($permisos, 5006, 4) && $oper == 'del') ||
        (PermisosUsuario($permisos, 5006, 2) && PermisosUsuario($permisos, 5006, 3) && $oper == 'close') ||
        (PermisosUsuario($permisos, 5006, 5) && $oper == 'annul' || $id_rol == 1)
    ) {

        if ($oper == 'add') {
            $id = $_POST['id_ingreso'];
            $id_sede = $_POST['id_txt_sede'];
            $fec_ing = $_POST['txt_fec_ing'];
            $hor_ing = $_POST['txt_hor_ing'];
            $id_tiping = $_POST['sl_tip_ing'];
            $id_tercero = $_POST['sl_tercero'] ? $_POST['sl_tercero'] : 0;
            $detalle = $_POST['txt_det_ing'];

            if ($id == -1) {
                $sql = "INSERT INTO acf_orden_ingreso(fec_ingreso,hor_ingreso,id_tipo_ingreso,
                        id_provedor,id_centrocosto,detalle,val_total,id_sede,id_sedetraslado,id_usr_crea,fec_creacion,estado)
                    VALUES('$fec_ing','$hor_ing',$id_tiping,
                        $id_tercero,0,'$detalle',0,$id_sede,$id_sede,$id_usr_ope,'$fecha_ope',1)";
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
                $sql = "SELECT estado FROM acf_orden_ingreso WHERE id_ingreso=" . $id;
                $rs = $cmd->query($sql);
                $obj_ingreso = $rs->fetch();

                if ($obj_ingreso['estado'] == 1) {
                    $sql = "UPDATE acf_orden_ingreso 
                        SET id_tipo_ingreso=$id_tiping,id_provedor=$id_tercero,detalle='$detalle'
                        WHERE id_ingreso=" . $id;
                    $rs = $cmd->query($sql);

                    if ($rs) {
                        $res['mensaje'] = 'ok';
                        $res['id'] = $id;
                    } else {
                        $res['mensaje'] = $cmd->errorInfo()[2];
                    }
                } else {
                    $res['mensaje'] = 'Solo puede Modificar Ordenes de Ingreso en estado Pendiente';
                }
            }
        }

        if ($oper == 'del') {
            $id = $_POST['id'];

            $sql = "SELECT estado FROM acf_orden_ingreso WHERE id_ingreso=" . $id;
            $rs = $cmd->query($sql);
            $obj_ingreso = $rs->fetch();

            if ($obj_ingreso['estado'] == 1) {
                $sql = "DELETE FROM acf_orden_ingreso WHERE id_ingreso=" . $id;
                $rs = $cmd->query($sql);
                if ($rs) {
                    $res['mensaje'] = 'ok';
                } else {
                    $res['mensaje'] = $cmd->errorInfo()[2];
                }
            } else {
                $res['mensaje'] = 'Solo puede Borrar Ordenes de Ingreso en estado Pendiente';
            }
        }

        if ($oper == 'close') {
            $id = $_POST['id'];

            $sql = "SELECT estado FROM acf_orden_ingreso WHERE id_ingreso=" . $id;
            $rs = $cmd->query($sql);
            $obj_ingreso = $rs->fetch();
            $estado = isset($obj_ingreso['estado']) ? $obj_ingreso['estado'] : -1;

            $sql = "SELECT COUNT(*) AS total FROM acf_orden_ingreso_detalle WHERE id_ingreso=" . $id;
            $rs = $cmd->query($sql);
            $obj_ingreso = $rs->fetch();
            $num_detalles = $obj_ingreso['total'];

            if ($estado == 1 && $num_detalles > 0) {
                $error = 0;
                $cmd->beginTransaction();

                $sql = 'SELECT acf_orden_ingreso.id_medicamento_articulo
                            acf_orden_ingreso.id_sede,
                            acf_orden_ingreso.detalle,
                            acf_orden_ingreso_detalle.cantidad AS cantidad,
                            acf_orden_ingreso_detalle.valor AS valor
                        FROM acf_orden_ingreso_detalle 
                        INNER JOIN acf_orden_ingreso ON (acf_orden_ingreso.id_ingreso = acf_orden_ingreso_detalle.id_orden_ingreso) 
                        WHERE acf_orden_ingreso_detalle.id_orden_ingreso=' . $id;
                $rs = $cmd->query($sql);
                $objs_detalles = $rs->fetchAll();

                foreach ($objs_detalles as $obj_det) {
                    $id_medicamento = $obj['id_medicamento_articulo'];
                    $id_sede = $obj_det['id_sede'];
                    $detalle = $obj_det['detalle'];
                    $fec_movimiento = date('Y-m-d');
                    $cantidad = $obj_det['cantidad'];
                    $valor = $obj_det['valor'];

                  

                    /* Valores [far_medicamentos] */
                    $sql = 'SELECT existencia,val_promedio FROM far_medicamentos WHERE id_med=' . $id_medicamento . ' LIMIT 1';
                    $rs = $cmd->query($sql);
                    $obj = $rs->fetch();
                    $val_promedio_med = $obj['val_promedio'] ? $obj['val_promedio'] : 0;
                    $existencia_med = $obj['existencia'];

                    $val_promedio_medicamento_kdx = $val_promedio_med;
                    $existencia_medicamento_kdx = $existencia_med + $cantidad;
                    if ($existencia_medicamento_kdx > 0) {
                        $val_promedio_medicamento_kdx = ($val_promedio_med * $existencia_med + $valor * $cantidad) / $existencia_medicamento_kdx;
                    }

                    $sql = "UPDATE far_medicamentos SET existencia=$existencia_medicamento_kdx,val_promedio=$val_promedio_medicamento_kdx WHERE id_med=" . $id_medicamento;
                    $rs3 = $cmd->query($sql);

                    if ($rs1 == false || $rs2 == false || $rs3 == false || error_get_last()) {
                        $error = 1;
                        break;
                    }
                }
                if ($error == 0) {
                    $sql = 'SELECT num_ingresoactual FROM tb_datos_ips LIMIT 1';
                    $rs = $cmd->query($sql);
                    $obj = $rs->fetch();
                    $num_ingreso = $obj['num_ingresoactual'];
                    $res['num_ingreso'] = $num_ingreso;

                    $sql = "UPDATE acf_orden_ingreso SET num_ingreso=$num_ingreso,estado=2,id_usr_cierre=$id_usr_ope,fec_cierre='$fecha_ope' WHERE id_ingreso=$id";
                    $rs1 = $cmd->query($sql);
                    $sql = 'UPDATE tb_datos_ips SET num_ingresoactual=num_ingresoactual+1';
                    $rs2 = $cmd->query($sql);

                    if ($rs1 == false || $rs2 == false || error_get_last()) {
                        $error = 1;
                    }
                }
                if ($error == 0) {
                    $cmd->commit();
                    $res['mensaje'] = 'ok';
                } else {
                    $res['mensaje'] = 'Error de Ejecución de Proceso';
                    $cmd->rollBack();
                }
            } else {
                if ($estado != 1) {
                    $res['mensaje'] = 'Solo puede Cerrar Ordenes de Ingreso en estado Pendiente';
                } else if ($num_detalles == 0) {
                    $res['mensaje'] = 'La Ordenes de Ingreso no tiene detalles';
                } else if ($num_reg_kardex > 0) {
                    $res['mensaje'] = 'La Orden de Ingreso ya tiene registro de movimientos en Kardex';
                }
            }
        }

        if ($oper == 'annul') {
            $id = $_POST['id'];

            $sql = "SELECT estado FROM acf_orden_ingreso WHERE id_ingreso=" . $id;
            $rs = $cmd->query($sql);
            $obj_ingreso = $rs->fetch();
            $estado = $obj_ingreso['estado'];

            if ($estado == 2) {
                $respuesta = verificar_kardex($cmd, $id, "I");

                if ($respuesta == 'ok') {
                    
                    $cmd->beginTransaction();

                    $sql = "UPDATE far_orden_ingreso 
                            INNER JOIN far_kardex ON(far_kardex.id_ingreso = far_orden_ingreso.id_ingreso)
                            SET far_orden_ingreso.id_usr_anula=$id_usr_ope,far_orden_ingreso.fec_anulacion='$fecha_ope',far_orden_ingreso.estado=0,far_kardex.estado=0 
                            WHERE far_orden_ingreso.id_ingreso=$id";
                    $rs = $cmd->query($sql);

                    if ($rs) {
                        $sql = "SELECT GROUP_CONCAT(id_lote) AS lotes
                                FROM far_orden_ingreso_detalle WHERE id_ingreso=" . $id;
                        $rs = $cmd->query($sql);
                        $obj = $rs->fetch();
                        $lotes = $obj['lotes'];

                        recalcular_kardex($cmd,$lotes,'I',$id,'','','','');                        
                    }
                    if ($rs) {
                        $cmd->commit();
                        $res['mensaje'] = 'ok';
                        $accion = 'Anular';
                        $opcion = 'Orden de Ingreso';
                        $detalle = 'Anulo Orden Ingreso Id: ' . $id;
                        bitacora($accion, $opcion, $detalle, $id_usr_ope, $_SESSION['user']);
                    } else {
                        $cmd->rollBack();
                        $res['mensaje'] = $cmd->errorInfo()[2];
                    }
                } else {
                    $res['mensaje'] = $respuesta;
                }
            } else {
                $res['mensaje'] = 'Solo puede Anular Ordenes de Ingreso en estado Cerrado';
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
