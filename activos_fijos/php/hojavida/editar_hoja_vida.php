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

        $id_hv = $_POST['id_hv'];

        $rs = $cmd->query($sql);
        $obj_ingreso = $rs->fetch();

        if ($oper == 'add') {
            if ($id_hv == -1) {
                
                $sql = "INSERT INTO acf_hojavida (
                    placa, serial, id_marca, valor, tipo_activo, id_articulo, modelo, id_sede, id_area, 
                    id_proveedor, lote, fecha_fabricacion, reg_invima, fabricante, lugar_origen, representante, 
                    dir_representante, tel_representante, imagen, recom_fabricante, tipo_adquisicion, fecha_adquisicion, 
                    fecha_instalacion, periodo_garantia, vida_util, calif_4725, calibracion, vol_min, vol_max, frec_min, 
                    frec_max, pot_min, pot_max, cor_min, cor_max, temp_min, temp_max, riesgo, uso, cb_diagnostico, 
                    cb_prevencion, cb_rehabilitacion, cb_analisis_lab, cb_trat_mant, estado_general, causa_est_general, 
                    fecha_fuera_servicio, id_usr_reg, fecha_reg, id_usr_act, fecha_act, estado
                ) VALUES (
                    :placa, :serial, :id_marca, :valor, :tipo_activo, :id_articulo, :modelo, :id_sede, :id_area, 
                    :id_proveedor, :lote, :fecha_fabricacion, :reg_invima, :fabricante, :lugar_origen, :representante, 
                    :dir_representante, :tel_representante, :imagen, :recom_fabricante, :tipo_adquisicion, :fecha_adquisicion, 
                    :fecha_instalacion, :periodo_garantia, :vida_util, :calif_4725, :calibracion, :vol_min, :vol_max, :frec_min, 
                    :frec_max, :pot_min, :pot_max, :cor_min, :cor_max, :temp_min, :temp_max, :riesgo, :uso, :cb_diagnostico, 
                    :cb_prevencion, :cb_rehabilitacion, :cb_analisis_lab, :cb_trat_mant, :estado_general, :causa_est_general, 
                    :fecha_fuera_servicio, :id_usr_reg, :fecha_reg, :id_usr_act, :fecha_act, :estado
                )";
        
                // Preparar la consulta
                $sql = $cmd->prepare($sql);
            
                // Datos para insertar (ejemplo)
                $fecha_fabricacion = date('Y-m-d', strtotime($_POST['fecha_fabricacion']));
                
                $data = [
                    ':placa' => $_POST['placa'],
                    ':serial' => $_POST['serial'],
                    ':id_marca' => $_POST['id_marca'],
                    ':valor' => $_POST['valor'],
                    ':tipo_activo' => $_POST['tipo_activo'],
                    ':id_articulo' => $_POST['id_articulo'],
                    ':modelo' => $_POST['modelo'],
                    ':id_sede' => $_POST['id_sede'],
                    ':id_area' => $_POST['id_area'],
                    ':id_proveedor' => $_POST['id_proveedor'],
                    ':lote' => $_POST['lote'],
                    ':fecha_fabricacion' => $fecha_fabricacion,
                    ':reg_invima' => $_POST['reg_invima'],
                    ':fabricante' => $_POST['fabricante'],
                    ':lugar_origen' => $_POST['lugar_origen'],
                    ':representante' => $_POST['representante'],
                    ':dir_representante' => $_POST['dir_representante'],
                    ':tel_representante' => $_POST['tel_representante'],
                    ':imagen' => $_POST['imagen'],
                    ':recom_fabricante' => $_POST['recom_fabricante'],
                    ':tipo_adquisicion' => $_POST['tipo_adquisicion'] ? $_POST['tipo_adquisicion'] : null,
                    ':fecha_adquisicion' => date('Y-m-d', strtotime($_POST['fecha_adquisicion'])),
                    ':fecha_instalacion' => date('Y-m-d', strtotime($_POST['fecha_instalacion'])),
                    ':periodo_garantia' => $_POST['periodo_garantia'],
                    ':vida_util' => $_POST['vida_util'],
                    ':calif_4725' => $_POST['calif_4725'] ? $_POST['calif_4725'] : null,
                    ':calibracion' => $_POST['calibracion'],
                    ':vol_min' => $_POST['vol_min'] ? $_POST['vol_min'] : null,
                    ':vol_max' => $_POST['vol_max'] ? $_POST['vol_max'] : null,
                    ':frec_min' => $_POST['frec_min'] ? $_POST['frec_min'] : null,
                    ':frec_max' => $_POST['frec_max'] ? $_POST['frec_max'] : null,
                    ':pot_min' => $_POST['pot_min'] ? $_POST['pot_min'] : null,
                    ':pot_max' => $_POST['pot_max'] ? $_POST['pot_max'] : null,
                    ':cor_min' => $_POST['cor_min'] ? $_POST['cor_min'] : null,
                    ':cor_max' => $_POST['cor_max'] ? $_POST['cor_max'] : null,
                    ':temp_min' => $_POST['temp_min'] ? $_POST['temp_min'] : null,
                    ':temp_max' => $_POST['temp_max'] ? $_POST['temp_max'] : null,
                    ':riesgo' => $_POST['riesgo'] ? $_POST['riesgo'] : null,
                    ':uso' => $_POST['uso'] ? $_POST['uso'] : null,
                    ':cb_diagnostico' => $_POST['cb_diagnostico'],
                    ':cb_prevencion' => $_POST['cb_prevencion'],
                    ':cb_rehabilitacion' => $_POST['cb_rehabilitacion'],
                    ':cb_analisis_lab' => $_POST['cb_analisis_lab'],
                    ':cb_trat_mant' => $_POST['cb_trat_mant'],
                    ':estado_general' => $_POST['estado_general'] ? $_POST['estado_general'] : null,
                    ':causa_est_general' => $_POST['causa_est_general'],
                    ':fecha_fuera_servicio' => date('Y-m-d', strtotime($_POST['fecha_fuera_servicio'])),
                    ':id_usr_reg' => $id_usr_crea,
                    ':fecha_reg' => $fecha_crea,
                    ':id_usr_act' => $id_usr_crea,
                    ':fecha_act' => $fecha_crea,
                    ':estado' => $_POST['estado'],
                ];
                
                $inserted = $sql->execute($data);

                if ($inserted) {
                    $id_hv = $cmd->lastInsertId();
                    $res['mensaje'] = 'ok';
                    $res['id_hv'] = $id_hv;
                } else {
                    $res['mensaje'] = $sql->errorInfo()[2];
                }   
            } else {
                $sql = "UPDATE acf_orden_ingreso_detalle 
                    SET cantidad=$cantidad,valor_sin_iva=$vr_unidad,iva=$iva,valor=$vr_costo,observacion='$observacion'
                    WHERE id_ing_detalle=" . $id;

                $rs = $cmd->query($sql);
                if ($rs) {
                    $res['mensaje'] = 'ok';
                    $res['id'] = $id;
                } else {
                    $res['mensaje'] = $cmd->errorInfo()[2];
                }
            }

            if ($oper == 'del') {
                $id = $_POST['id'];
                $sql = "DELETE FROM acf_orden_ingreso_detalle WHERE id_ing_detalle=" . $id;
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
