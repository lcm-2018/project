<?php
session_start();
if (!isset($_SESSION['user'])) {
    echo '<script>window.location.replace("../../../index.php");</script>';
    exit();
}
include '../../../conexion.php';
include '../common/cargar_combos.php';
include '../common/funciones_generales.php';

$cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
$cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

$id = isset($_POST['id_hv']) ? $_POST['id_hv'] : -1;
$sql = "SELECT 
            HV.id,
            HV.placa,
            HV.serial,
            HV.id_marca,
            HV.valor,
            HV.tipo_activo,
            HV.id_articulo,
            HV.modelo,
            HV.id_sede,
            HV.id_area,
            HV.id_proveedor,
            HV.lote,
            HV.fecha_fabricacion,
            HV.reg_invima,
            HV.fabricante,
            HV.lugar_origen,
            HV.representante,
            HV.dir_representante,
            HV.tel_representante,
            HV.imagen,
            HV.recom_fabricante,
            HV.tipo_adquisicion,
            HV.fecha_adquisicion,
            HV.fecha_instalacion,
            HV.periodo_garantia,
            HV.vida_util,
            HV.calif_4725,
            HV.calibracion,
            HV.vol_min,
            HV.vol_max,
            HV.frec_min,
            HV.frec_max,
            HV.pot_min,
            HV.pot_max,
            HV.cor_min,
            HV.cor_max,
            HV.temp_min,
            HV.temp_max,
            HV.riesgo,
            HV.uso,
            HV.cb_diagnostico,
            HV.cb_prevencion,
            HV.cb_rehabilitacion,
            HV.cb_analisis_lab,
            HV.cb_trat_mant,
            HV.estado_general,
            HV.causa_est_general,
            HV.fecha_fuera_servicio,
            HV.id_usr_reg,
            HV.fecha_reg,
            HV.id_usr_act,
            HV.fecha_act,
            HV.estado
        FROM acf_hojavida HV
        LEFT JOIN tb_sedes SD ON (SD.id_sede=HV.id_sede)
        WHERE HV.id=" . $id . " LIMIT 1";
$rs = $cmd->query($sql);
$obj = $rs->fetch();

if ($obj === false) {
    $obj = array(); // Inicializa $obj como un array vacío
}

if (empty($obj)) {
    $n = $rs->columnCount();
    for ($i = 0; $i < $n; $i++) :
        $col = $rs->getColumnMeta($i);
        $name = $col['name'];
        $obj[$name] = NULL;
    endfor;
    //Inicializa variable por defecto
    $obj['estado'] = 1;
    $obj['nom_estado'] = 'PENDIENTE';
    $obj['val_total'] = 0;

    $bodega = sede_principal($cmd);
    $obj['id_sede'] = $bodega['id_sede'];
    $obj['nom_sede'] = $bodega['nom_sede'];

    $area = area_principal($cmd);
    $obj['id_area'] = $area['id_area'];
    $obj['nom_area'] = $area['nom_area'];

    $fecha = fecha_hora_servidor();
    $obj['fec_ingreso'] = $fecha['fecha'];
    $obj['hor_ingreso'] = $fecha['hora'];
} else {
    
    $bodega = sede_principal($cmd);
    $obj['id_sede'] = $bodega['id_sede'];
    $obj['nom_sede'] = $bodega['nom_sede'];
    
    if($obj['id_area'] == null) {
        $area = area_principal($cmd);
        $obj['id_area'] = $area['id_area'];
        $obj['nom_area'] = $area['nom_area'];
    }
}
$guardar = in_array($obj['estado'],[1]) ? '' : 'disabled="disabled"';
$cerrar = in_array($obj['estado'],[1]) && $id != -1 ? '' : 'disabled="disabled"';
$anular = in_array($obj['estado'],[2]) ? '' : 'disabled="disabled"';
$imprimir = $id != -1 ? '' : 'disabled="disabled"';

?>

<div class="px-0">
    <div class="shadow">
        <div class="card-header mb-3" style="background-color: #16a085 !important;">
            <h5 style="color: white;">REGISTRAR HOJA DE VIDA ACTIVO FIJO</h5>
        </div>
        <div class="px-2">
            <form id="acf_reg_hoja_vida">
                <input type="hidden" id="id_hv" name="id_hv" value="<?php echo $id ?>">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="id_sede" class="small">Sede</label>
                        <input type="text" class="form-control form-control-sm" id="nom_sede" class="small" value="<?php echo $obj['nom_sede'] ?>" readonly="readonly">
                        <input type="hidden" id="id_sede" name="id_sede" value="<?php echo $obj['id_sede'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="id_area" class="small">Área</label>
                        <input type="text" class="form-control form-control-sm" id="nom_area" class="small" value="<?php echo $obj['nom_area'] ?>" readonly="readonly">
                        <input type="hidden" id="id_area" name="id_area" value="<?php echo $obj['id_area'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="id_proveedor" class="small">Proveedor</label>
                        <select class="form-control form-control-sm" id="id_proveedor" name="id_proveedor">
                            <?php terceros($cmd, '', $obj['id_proveedor']) ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="id_articulo" class="small">Artículo</label>
                        <select class="form-control form-control-sm" id="id_articulo" name="id_articulo">
                        <?php articulosActivosFijos($cmd, '', $obj['id_articulo']) ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="placa" class="small">Placa</label>
                        <input type="text" class="form-control form-control-sm" id="placa" name="placa" value="<?php echo $obj['placa'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="serial" class="small">Serial</label>
                        <input type="text" class="form-control form-control-sm" id="serial" name="serial" value="<?php echo $obj['serial'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="id_marca" class="small">Marca</label>
                        <select class="form-control form-control-sm" id="id_marca" name="id_marca">
                            <?php marcas($cmd, '', $obj['id_marca']) ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="valor" class="small">Valor</label>
                        <input type="number" step="0.0001" class="form-control form-control-sm" id="valor" name="valor" value="<?php echo $obj['valor'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="tipo_activo" class="small">Tipo de Activo</label>
                        <select class="form-control form-control-sm" id="tipo_activo" name="tipo_activo">
                            <?php tipos_activo($cmd, $obj['tipo_activo']) ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="modelo" class="small">Modelo</label>
                        <input type="text" class="form-control form-control-sm" id="modelo" name="modelo" value="<?php echo $obj['modelo'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="lote" class="small">Lote</label>
                        <input type="text" class="form-control form-control-sm" id="lote" name="lote" value="<?php echo $obj['lote'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="fecha_fabricacion" class="small">Fecha de Fabricación</label>
                        <input type="date" class="form-control form-control-sm" id="fecha_fabricacion" name="fecha_fabricacion" class="small" value="<?php echo $obj['fecha_fabricacion'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="reg_invima" class="small">Registro INVIMA</label>
                        <input type="text" class="form-control form-control-sm" id="reg_invima" name="reg_invima" value="<?php echo $obj['reg_invima'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="fabricante" class="small">Fabricante</label>
                        <input type="text" class="form-control form-control-sm" id="fabricante" name="fabricante" value="<?php echo $obj['fabricante'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="lugar_origen" class="small">Lugar de Origen</label>
                        <input type="text" class="form-control form-control-sm" id="lugar_origen" name="lugar_origen" value="<?php echo $obj['lugar_origen'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="representante" class="small">Representante</label>
                        <input type="text" class="form-control form-control-sm" id="representante" name="representante" value="<?php echo $obj['representante'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="dir_representante" class="small">Dirección del Representante</label>
                        <input type="text" class="form-control form-control-sm" id="dir_representante" name="dir_representante" value="<?php echo $obj['dir_representante'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="tel_representante" class="small">Teléfono del Representante</label>
                        <input type="text" class="form-control form-control-sm" id="tel_representante" name="tel_representante" value="<?php echo $obj['tel_representante'] ?>">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="recom_fabricante" class="small">Recomendaciones del Fabricante</label>
                        <textarea class="form-control form-control-sm" id="recom_fabricante" name="recom_fabricante" rows="3"><?php echo $obj['recom_fabricante'] ?></textarea>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="tipo_adquisicion" class="small">Tipo de Adquisición</label>
                        <select class="form-control form-control-sm" id="tipo_adquisicion" name="tipo_adquisicion">
                            <?php tipo_ingreso($cmd, '', $obj['tipo_adquisicion']) ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="fecha_adquisicion" class="small">Fecha de Adquisición</label>
                        <input type="date" class="form-control form-control-sm" id="fecha_adquisicion" name="fecha_adquisicion" value="<?php echo $obj['fecha_adquisicion'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="fecha_instalacion" class="small">Fecha de Instalación</label>
                        <input type="date" class="form-control form-control-sm" id="fecha_instalacion" name="fecha_instalacion" value="<?php echo $obj['fecha_instalacion'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="periodo_garantia" class="small">Período de Garantía</label>
                        <input type="text" class="form-control form-control-sm" id="periodo_garantia" name="periodo_garantia" value="<?php echo $obj['periodo_garantia'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="vida_util" class="small">Vida Útil</label>
                        <input type="text" class="form-control form-control-sm" id="vida_util" name="vida_util" value="<?php echo $obj['vida_util'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="calif_4725" class="small">Calificación 4725</label>
                        <input type="number" class="form-control form-control-sm" id="calif_4725" name="calif_4725" value="<?php echo $obj['calif_4725'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="calibracion" class="small">Calibración</label>
                        <input type="text" class="form-control form-control-sm" id="calibracion" name="calibracion" value="<?php echo $obj['calibracion'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="vol_min" class="small">Voltaje Mínimo</label>
                        <input type="number" class="form-control form-control-sm" id="vol_min" name="vol_min" value="<?php echo $obj['vol_min'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="vol_max" class="small">Voltaje Máximo</label>
                        <input type="number" class="form-control form-control-sm" id="vol_max" name="vol_max" value="<?php echo $obj['vol_max'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="frec_min" class="small">Frecuencia Mínima</label>
                        <input type="number" class="form-control form-control-sm" id="frec_min" name="frec_min" value="<?php echo $obj['frec_min'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="frec_max" class="small">Frecuencia Máxima</label>
                        <input type="number" class="form-control form-control-sm" id="frec_max" name="frec_max" value="<?php echo $obj['frec_max'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="pot_min" class="small">Potencia Mínima</label>
                        <input type="number" class="form-control form-control-sm" id="pot_min" name="pot_min" value="<?php echo $obj['pot_min'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="pot_max" class="small">Potencia Máxima</label>
                        <input type="number" class="form-control form-control-sm" id="pot_max" name="pot_max" value="<?php echo $obj['pot_max'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="cor_min" class="small">Corriente Mínima</label>
                        <input type="number" class="form-control form-control-sm" id="cor_min" name="cor_min" value="<?php echo $obj['cor_min'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="cor_max" class="small">Corriente Máxima</label>
                        <input type="number" class="form-control form-control-sm" id="cor_max" name="cor_max" value="<?php echo $obj['cor_max'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="temp_min" class="small">Temperatura Mínima</label>
                        <input type="number" class="form-control form-control-sm" id="temp_min" name="temp_min" value="<?php echo $obj['temp_min'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="temp_max" class="small">Temperatura Máxima</label>
                        <input type="number" class="form-control form-control-sm" id="temp_max" name="temp_max" value="<?php echo $obj['temp_max'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="riesgo" class="small">Riesgo</label>
                        <input type="number" class="form-control form-control-sm" id="riesgo" name="riesgo" value="<?php echo $obj['riesgo'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="uso" class="small">Uso</label>
                        <input type="number" class="form-control form-control-sm" id="uso" name="uso" value="<?php echo $obj['uso'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="cb_diagnostico" class="small">CB Diagnóstico</label>
                        <input type="text" class="form-control form-control-sm" id="cb_diagnostico" name="cb_diagnostico" value="<?php echo $obj['cb_diagnostico'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="cb_prevencion" class="small">CB Prevención</label>
                        <input type="text" class="form-control form-control-sm" id="cb_prevencion" name="cb_prevencion" value="<?php echo $obj['cb_prevencion'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="cb_rehabilitacion" class="small">CB Rehabilitación</label>
                        <input type="text" class="form-control form-control-sm" id="cb_rehabilitacion" name="cb_rehabilitacion" value="<?php echo $obj['cb_rehabilitacion'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="cb_analisis_lab" class="small">CB Análisis de Laboratorio</label>
                        <input type="text" class="form-control form-control-sm" id="cb_analisis_lab" name="cb_analisis_lab" value="<?php echo $obj['cb_analisis_lab'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="cb_trat_mant" class="small">CB Tratamiento y Mantenimiento</label>
                        <input type="text" class="form-control form-control-sm" id="cb_trat_mant" name="cb_trat_mant" value="<?php echo $obj['cb_trat_mant'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="estado_general" class="small">Estado General</label>
                        <select class="form-control form-control-sm" id="estado_general" name="estado_general">
                            <?php estado_general_activo('--Estado--', $obj['estado_general']) ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="fecha_fuera_servicio" class="small">Fecha Fuera de Servicio</label>
                        <input type="date" class="form-control form-control-sm" id="fecha_fuera_servicio" name="fecha_fuera_servicio" value="<?php echo $obj['fecha_fuera_servicio'] ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="estado" class="small">Estado</label>
                        <select class="form-control form-control-sm" id="estado" name="estado">
                            <?php estado_activo('--Estado--', $obj['estado']) ?>
                        </select>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="causa_est_general" class="small">Causa del Estado General</label>
                        <textarea class="form-control form-control-sm" id="causa_est_general" name="causa_est_general" rows="3"><?php echo $obj['causa_est_general'] ?></textarea>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="imagen" class="small">Imagen</label>
                        <input type="text" class="form-control form-control-sm" id="imagen" name="imagen" value="<?php echo $obj['imagen'] ?>">
                    </div>
                </div>
                <div class="form-group mt-3">
                    <button type="button" class="btn btn-primary btn-sm" id="btn_guardar" <?php echo $guardar ?>>Guardar</button>
                    <button type="button" class="btn btn-primary btn-sm" id="btn_cerrar" <?php echo $cerrar ?>>Cerrar</button>
                    <button type="button" class="btn btn-primary btn-sm" id="btn_anular" <?php echo $anular ?>>Anular</button>
                    <button type="button" class="btn btn-primary btn-sm" id="btn_imprimir" <?php echo $imprimir ?>>Imprimir</button>
                    <a type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Aquí puedes agregar cualquier script adicional necesario para el funcionamiento del formulario
</script>


