<?php
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
include_once 'conexion.php';
include_once 'permisos.php';
$rol = $_SESSION['rol'];
?>
<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu ">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">MÓDULOS</div>
                <?php

                /* MODULO DE ALMACEN */

                $key = array_search('50', array_column($perm_modulos, 'id_modulo'));
                if (false !== $key) {
                    ?>
                    <a class="nav-link collapsed sombra" href="#" data-toggle="collapse" data-target="#collapseAlmacen" aria-expanded="false" aria-controls="collapseAlmacen">
                        <div class="form-row">
                            <div class="div-icono">
                                <span class="fas fa-store fa-lg" style="color: #82E0AA"></span>
                            </div>
                            <div>
                                Almacén
                            </div>
                        </div>
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-caret-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseAlmacen" aria-labelledby="headingTwo" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav accordion shadow-nav-lat" id="sidenavAccordionPages">
                            <a class="nav-link collapsed sombra" href="#" data-toggle="collapse" data-target="#pagesCollapseArticulos" aria-expanded="false" aria-controls="pagesCollapseArticulos">
                                <div class="form-row">
                                    <div class="div-icono">
                                        <i class="fa fa-tags fa-sm" style="color: #FFC300CC;"></i>
                                    </div>
                                    <div>
                                        General
                                    </div>
                                </div>
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-caret-down"></i></div>
                            </a>
                            <div class="collapse" id="pagesCollapseArticulos" aria-labelledby="headingOne">
                                <nav class="sb-sidenav-menu-nested nav shadow-nav-lat">
                                    <?php if (PermisosUsuario($permisos, 5010, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/almacen/php/centros_costo/index.php?var=3">
                                            <div class="div-icono">
                                                <i class="fas fa-file-invoice-dollar fa-sm" style="color: #E74C3C;"></i>
                                            </div>
                                            <div>
                                                Centros Costo
                                            </div>
                                        </a>
                                    <?php } ?>
                                    <?php if (PermisosUsuario($permisos, 5015, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/almacen/php/centrocosto_areas/index.php?var=3">
                                            <div class="div-icono">
                                                <i class="fa fa-sitemap fa-sm" style="color: #E74C3C;"></i>
                                            </div>
                                            <div>
                                                Areas
                                            </div>
                                        </a>
                                    <?php } ?>
                                    <?php if (PermisosUsuario($permisos, 5016, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/almacen/php/pres_comercial/index.php?var=3">
                                            <div class="div-icono">
                                                <i class="fas fa-ticket-alt fa-sm" style="color: #E74C3C;"></i>
                                            </div>
                                            <div>
                                                Presentación Comercial
                                            </div>
                                        </a>
                                    <?php } ?>
                                    <?php if (PermisosUsuario($permisos, 5001, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/almacen/php/subgrupos/index.php?var=3">
                                            <div class="div-icono">
                                                <i class="fas fa-layer-group fa-sm" style="color: #E74C3C;"></i>
                                            </div>
                                            <div>
                                                Subgrupos
                                            </div>
                                        </a>
                                    <?php } ?>
                                    <?php if (PermisosUsuario($permisos, 5002, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/almacen/php/articulos/index.php?var=3">
                                            <div class="div-icono">
                                                <i class="fa fa-barcode fa-sm" style="color: #E74C3C;"></i>
                                            </div>
                                            <div>
                                                Articulos
                                            </div>
                                        </a>
                                    <?php } ?>
                                </nav>
                            </div>
                            <a class="nav-link collapsed sombra" href="#" data-toggle="collapse" data-target="#pagesCollapsePedidos" aria-expanded="false" aria-controls="pagesCollapsePedidos">
                                <div class="form-row">
                                    <div class="div-icono">
                                        <i class="fa fa-pencil-square-o fa-sm" style="color: #FFC300CC;"></i>
                                    </div>
                                    <div>
                                        Pedidos
                                    </div>
                                </div>
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-caret-down"></i></div>
                            </a>
                            <div class="collapse" id="pagesCollapsePedidos" aria-labelledby="headingOne">
                                <nav class="sb-sidenav-menu-nested nav shadow-nav-lat">
                                    <?php if (PermisosUsuario($permisos, 5005, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/almacen/php/pedidos_alm/index.php">
                                            <div class="form-row">
                                                <div class="div-icono">
                                                    <i class="fa fa-database fa-sm" style="color: #E74C3C;"></i>
                                                </div>
                                                <div>
                                                    Almacen
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>
                                    <?php if (PermisosUsuario($permisos, 5003, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/almacen/php/pedidos_bod/index.php">
                                            <div class="form-row">
                                                <div class="div-icono">
                                                    <i class="fa fa-th-large fa-sm" style="color: #E74C3C;"></i>
                                                </div>
                                                <div>
                                                    Bodega
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>                                    
                                </nav>
                            </div>
                            <a class="nav-link collapsed sombra" href="#" data-toggle="collapse" data-target="#pagesCollapseMovimientos" aria-expanded="false" aria-controls="pagesCollapseMovimientos">
                                <div class="form-row">
                                    <div class="div-icono">
                                        <i class="fas fa-sliders fa-sm" style="color: #FFC300CC;"></i>
                                    </div>
                                    <div>
                                        Movimientos
                                    </div>
                                </div>
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-caret-down"></i></div>
                            </a>
                            <div class="collapse" id="pagesCollapseMovimientos" aria-labelledby="headingOne">
                                <nav class="sb-sidenav-menu-nested nav shadow-nav-lat">
                                    <?php if (PermisosUsuario($permisos, 5006, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/almacen/php/ingresos/index.php">
                                            <div class="form-row">
                                                <div class="div-icono">
                                                    <i class="fas fa-door-open" style="color: #E74C3C;"></i>
                                                </div>
                                                <div>
                                                    Ingresos
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>
                                    <?php if (PermisosUsuario($permisos, 5007, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/almacen/php/egresos/index.php">
                                            <div class="form-row">
                                                <div class="div-icono">
                                                    <i class="fas fa-sign-out-alt" style="color: #E74C3C;"></i>
                                                </div>
                                                <div>
                                                    Egresos
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>
                                    <?php if (PermisosUsuario($permisos, 5008, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/almacen/php/traslados/index.php">
                                            <div class="form-row">
                                                <div class="div-icono">
                                                    <i class="fas fa-exchange-alt" style="color: #E74C3C;"></i>
                                                </div>
                                                <div>
                                                    Traslados
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>
                                    <?php if (PermisosUsuario($permisos, 5009, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/almacen/php/recalcular_kardex/index.php">
                                            <div class="form-row">
                                                <div class="div-icono">
                                                    <i class="fa fa-cogs" style="color: #E74C3C;"></i>
                                                </div>
                                                <div>
                                                    Recalcula Mtos.
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>
                                </nav>
                            </div>
                            <a class="nav-link collapsed sombra" href="#" data-toggle="collapse" data-target="#pagesCollapseReportes" aria-expanded="false" aria-controls="pagesCollapseMovimientos">
                                <div class="form-row">
                                    <div class="div-icono">
                                        <i class="fa fa-map-o fa-sm" style="color: #FFC300CC;"></i>
                                    </div>
                                    <div>
                                        Reportes
                                    </div>
                                </div>
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-caret-down"></i></div>
                            </a>
                            <div class="collapse" id="pagesCollapseReportes" aria-labelledby="headingOne">
                                <nav class="sb-sidenav-menu-nested nav shadow-nav-lat">
                                    <?php if (PermisosUsuario($permisos, 5011, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/almacen/php/existencia_articulo/index.php">
                                            <div class="form-row">
                                                <div class="div-icono">
                                                    <i class="fas fa-chart-bar" style="color: #E74C3C;"></i>
                                                </div>
                                                <div>
                                                    Ex. General
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>
                                    <?php if (PermisosUsuario($permisos, 5012, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/almacen/php/existencia_lote/index.php">
                                            <div class="form-row">
                                                <div class="div-icono">
                                                    <i class="fas fa-chart-bar" style="color: #E74C3C;"></i>
                                                </div>
                                                <div>
                                                    Ex. Detallada
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>
                                    <?php if (PermisosUsuario($permisos, 5013, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/almacen/php/existencia_fecha/index.php">
                                            <div class="form-row">
                                                <div class="div-icono">
                                                    <i class="fas fa-chart-bar" style="color: #E74C3C;"></i>
                                                </div>
                                                <div>
                                                    Ex. a una Fecha
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>
                                    <?php if (PermisosUsuario($permisos, 5014, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/almacen/php/movimiento_periodo/index.php">
                                            <div class="form-row">
                                                <div class="div-icono">
                                                    <i class="fas fa-chart-bar" style="color: #E74C3C;"></i>
                                                </div>
                                                <div>
                                                    Mov. por Periodo
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>
                                    <?php if (PermisosUsuario($permisos, 5099, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/almacen/php/inf_personalizados/index.php">
                                            <div class="form-row">
                                                <div class="div-icono">
                                                    <i class="fas fa-chart-bar" style="color: #E74C3C;"></i>
                                                </div>
                                                <div>
                                                    Inf. Personalizados
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>
                                </nav>
                            </div>
                        </nav>
                    </div>
                <?php
                }

                /* MODULO DE ACTIVOS FIJOS */

                $key = array_search('57', array_column($perm_modulos, 'id_modulo'));
                if (false !== $key) {
                ?>
                    <a class="nav-link collapsed sombra" href="#" data-toggle="collapse" data-target="#collapseActivosFijos" aria-expanded="false" aria-controls="collapsePages2">
                        <div class="form-row">
                            <div class="div-icono">
                                <span class="fas fa-laptop-house fa-lg" style="color: #D2B4DE"></span>
                            </div>
                            <div>
                                Activos Fijos
                            </div>
                        </div>
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-caret-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseActivosFijos" aria-labelledby="headingTwo" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav accordion shadow-nav-lat" id="sidenavAccordionPages">
                            <a class="nav-link collapsed sombra" href="#" data-toggle="collapse" data-target="#pagesCollapseAcfGeneral" aria-expanded="false" aria-controls="pagesCollapseAcfGeneral">
                                <div class="form-row">
                                    <div class="div-icono">
                                    <i class="fa fa-tags fa-sm" style="color: #FFC300CC;"></i>
                                    </div>
                                    <div>
                                        General
                                    </div>
                                </div>
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-caret-down"></i></div>
                            </a>
                            <div class="collapse" id="pagesCollapseAcfGeneral" aria-labelledby="headingOne">
                                <nav class="sb-sidenav-menu-nested nav shadow-nav-lat">
                                    <?php if (PermisosUsuario($permisos, 5707, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/activos_fijos/php/marcas/index.php?var=3">
                                            <div class="div-icono">
                                                <i class="fas fa-border-none" style="color: #E74C3C;"></i>
                                            </div>
                                            <div>
                                                Marcas
                                            </div>
                                        </a>
                                    <?php } ?>
                                </nav>
                                <nav class="sb-sidenav-menu-nested nav shadow-nav-lat">
                                    <?php if (PermisosUsuario($permisos, 5701, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/activos_fijos/php/articulos/index.php?var=3">
                                            <div class="div-icono">
                                                <i class="fa fa-tags fa-sm" style="color: #E74C3C;"></i>
                                            </div>
                                            <div>
                                                Articulos
                                            </div>
                                        </a>
                                    <?php } ?>
                                </nav>
                            </div>
                            <a class="nav-link collapsed sombra" href="#" data-toggle="collapse" data-target="#pagesCollapseMovimientos" aria-expanded="false" aria-controls="pagesCollapseMovimientos">
                                <div class="form-row">
                                    <div class="div-icono">
                                    <i class="fas fa-sliders fa-sm" style="color: #FFC300CC;"></i>
                                    </div>
                                    <div>
                                        Movimientos
                                    </div>
                                </div>
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-caret-down"></i></div>
                            </a>
                            <div class="collapse" id="pagesCollapseMovimientos" aria-labelledby="headingOne">
                                <nav class="sb-sidenav-menu-nested nav shadow-nav-lat">
                                    <?php if (PermisosUsuario($permisos, 5702, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/activos_fijos/php/pedidos/index.php?var=3">
                                            <div class="form-row">
                                                <div class="div-icono">
                                                    <i class="fa fa-pencil-square-o fa-sm" style="color: #E74C3C;"></i>
                                                </div>
                                                <div>
                                                    Pedidos
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>
                                    <?php if (PermisosUsuario($permisos, 5703, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/activos_fijos/php/ingresos/index.php?var=3">
                                            <div class="form-row">
                                                <div class="div-icono">
                                                    <i class="fas fa-door-open" style="color: #E74C3C;"></i>
                                                </div>
                                                <div>
                                                    Ingresos
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>                                    
                                </nav>
                            </div>
                            <a class="nav-link collapsed sombra" href="#" data-toggle="collapse" data-target="#pagesCollapseMantenimiento" aria-expanded="false" aria-controls="pagesCollapseMantenimiento">
                                <div class="form-row">
                                    <div class="div-icono">
                                    <i class="fas fa-cogs" style="color: #FFC300CC;"></i>
                                    </div>
                                    <div>
                                        Mantenimiento
                                    </div>
                                </div>
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-caret-down"></i></div>
                            </a>
                            <div class="collapse" id="pagesCollapseMantenimiento" aria-labelledby="headingOne">
                                <nav class="sb-sidenav-menu-nested nav shadow-nav-lat">
                                    <?php if (PermisosUsuario($permisos, 5704, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/activos_fijos/php/hojavida/index.php?var=3">
                                            <div class="form-row">
                                                <div class="div-icono">
                                                    <i class="fa fa-pencil-square-o fa-sm" style="color: #E74C3C;"></i>
                                                </div>
                                                <div>
                                                    Hoja de Vida
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>
                                    <?php if (PermisosUsuario($permisos, 5704, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/activos_fijos/php/ingresos/index.php?var=3">
                                            <div class="form-row">
                                                <div class="div-icono">
                                                    <i class="far fa-clipboard" style="color: #E74C3C;"></i>
                                                </div>
                                                <div>
                                                    Registros
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>  
                                    <?php if (PermisosUsuario($permisos, 5703, 1) || $id_rol == 1) { ?>
                                        <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/activos_fijos/php/ingresos/index.php?var=3">
                                            <div class="form-row">
                                                <div class="div-icono">
                                                    <i class="fas fa-sort-amount-down-alt fa-sm" style="color: #E74C3C;"></i>
                                                </div>
                                                <div>
                                                    Progreso
                                                </div>
                                            </div>
                                        </a>
                                    <?php } ?>                                   
                                </nav>
                            </div>
                        </nav>
                    </div>
                <?php
                }

                /* MODULO DE ACTIVOS FIJOS ANTERIOR */

                $key = array_search('57', array_column($perm_modulos, 'id_modulo'));
                if (false !== $key) {
                ?>
                    <a class="nav-link collapsed sombra" href="#" data-toggle="collapse" data-target="#collapseActFijos" aria-expanded="false" aria-controls="collapsePages2">
                        <div class="form-row">
                            <div class="div-icono">
                                <span class="fas fa-laptop-house fa-lg" style="color: #D2B4DE"></span>
                            </div>
                            <div>
                                Activos Fijos
                            </div>
                        </div>
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-caret-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseActFijos" aria-labelledby="headingTwo" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav accordion shadow-nav-lat" id="sidenavAccordionPages">
                            <?php if (PermisosUsuario($permisos, 5701, 0) || $id_rol == 1) { ?>
                                <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/activos_fijos/entradas_activos_fijos.php">
                                    <div class="form-row">
                                        <div class="div-icono">
                                            <i class="fas fa-people-carry fa-sm" style="color: #85C1E9;"></i>
                                        </div>
                                        <div>
                                            Entradas
                                        </div>
                                    </div>
                                </a>
                            <?php } ?>
                            <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/activos_fijos/componentes_acfijos.php">
                                <div class="form-row">
                                    <div class="div-icono">
                                        <span class="fas fa-pencil-ruler fa-sm" style="color: #F1C40F;"></span>
                                    </div>
                                    <div>
                                        Gestión
                                    </div>
                                </div>
                            </a>
                            <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/activos_fijos/mantenimiento_acfijos.php">
                                <div class="form-row">
                                    <div class="div-icono">
                                        <span class="fas fa-tools fa-sm" style="color: #EB984E;"></span>
                                    </div>
                                    <div>
                                        Mantenimiento
                                    </div>
                                </div>
                            </a>
                        </nav>
                    </div>
                <?php
                }
                //$key = array_search('9', array_column($perm_modulos, 'id_modulo'));
                if (false) {
                ?>
                    <a class="nav-link collapsed sombra" href="#" data-toggle="collapse" data-target="#collapseCostos" aria-expanded="false" aria-controls="collapsePages2">
                        <div class="form-row">
                            <div class="div-icono">
                                <span class="fas fa-laptop-house fa-lg" style="color: #D2B4DE"></span>
                            </div>
                            <div>
                                Costos
                            </div>
                        </div>
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-caret-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseCostos" aria-labelledby="headingTwo" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav accordion shadow-nav-lat" id="sidenavAccordionPages">
                            <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/activos_fijos/entradas_activos_fijos.php">
                                <div class="form-row">
                                    <div class="div-icono">
                                        <i class="fas fa-people-carry fa-sm" style="color: #85C1E9;"></i>
                                    </div>
                                    <div>
                                        Entradas
                                    </div>
                                </div>
                            </a>
                            <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/activos_fijos/componentes_acfijos.php">
                                <div class="form-row">
                                    <div class="div-icono">
                                        <span class="fas fa-pencil-ruler fa-sm" style="color: #F1C40F;"></span>
                                    </div>
                                    <div>
                                        Gestión
                                    </div>
                                </div>
                            </a>
                        </nav>
                    </div>
                <?php
                }
                //$key = array_search('10', array_column($perm_modulos, 'id_modulo'));
                if (false) {
                ?>
                    <a class="nav-link sombra" href="<?php echo $_SESSION['urlin'] ?>/consultas/listado.php">
                        <div class="form-row">
                            <div class="div-icono">
                                <i class="fas fa-user-secret fa-lg" style="color: #1ABC9C;"></i>
                            </div>
                            <div>
                                Consultas
                            </div>
                        </div>
                    </a>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="sb-sidenav-footer py-0">
            <style>
                #btnRegVigencia,
                #btnRegVigencia:hover {
                    color: whitesmoke;
                    text-decoration: none;
                }
            </style>
            <div class="small">Actualmente:</div>
            <?php
            if ($id_rol == 1) {
                $valida = '<div><a type="button" id="btnRegVigencia" href="javascript:void(0)" title="Agregar Vigencia">Vigencia:</a> ' . $_SESSION['vigencia'] . '</div>';
            } else {
                $valida = '<div>Vigencia: ' . $_SESSION['vigencia'] . '</div>';
            }
            ?>
            <div class="small">
                <?php echo $valida ?>
                <div>Usuario: <?php echo mb_strtoupper($_SESSION['user']) ?></div>
            </div>
        </div>
    </nav>
</div>