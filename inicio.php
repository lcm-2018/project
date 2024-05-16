<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
include_once 'conexion.php';
include_once 'permisos.php';
?>
<!DOCTYPE html>
<html lang="es">
<?php include 'head.php' ?>

<body class="sb-nav-fixed <?php if ($_SESSION['navarlat'] == '1') {
                                echo 'sb-sidenav-toggled';
                            } ?>">
    <?php include 'navsuperior.php' ?>
    <div id="layoutSidenav">
        <?php include 'navlateral.php' ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid p-2">
                    <div class="card mb-4">
                        <div class="card-header" id="divTituloPag">
                            <span class="fas fa-house-user fa-lg" style="color: #1D80F7"></span> INICIO
                        </div>
                        <div class="card-body" id="divCuerpoPag">
                            <div class="container">
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <?php include 'footer.php' ?>
        </div>
        <?php include 'modales.php' ?>
    </div>
    <?php include 'scripts.php' ?>
</body>

</html>