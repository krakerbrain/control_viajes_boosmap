<?php
$rutainicio = isset($indice) && $indice != "inicio" ? $baseUrl . "index.php" : "#";
$colaboraciones = new HaColaborado($con, $datosUsuario['idusuario']);
$datos = $colaboraciones->haColaborado();
// $datos = 1 si ha colaborado, 0 si no ha colaborado
// $haColaborado = !empty($datos) ? $datos[0]['verificado'] : null;
$haColaborado = $datos;

?>
<nav class="navbar navbar-dark bg-danger lighten-4">
    <a class="navbar-brand" href="<?= $rutainicio ?>">Hola, <?= ucfirst($datosUsuario['nombre']) ?></a>
    <div>
        <button class="navbar-toggler toggler-example" type="button" data-toggle="collapse"
            data-target="#navbarSupportedContent1" aria-controls="navbarSupportedContent1" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="dark-blue-text">
                <i class="fas fa-bars fa-1x"></i>
            </span>
        </button>
    </div>
    <div class="collapse navbar-collapse" id="navbarSupportedContent1">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" style="font-size:14px" href="<?= $rutainicio ?>">Inicio <span
                        class="sr-only">(current)</span></a>
            </li>
            <?php if ($datosUsuario['otrasapps']) { ?>
            <li class="nav-item">
                <a class="navbar-brand" style="font-size:14px" href="<?= $baseUrl . "aplicaciones/index.php" ?>">Otras
                    Apps</a>
            </li>
            <?php } ?>
            <?php if ($datosUsuario['admin']) { ?>
            <li class="nav-item">
                <a class="navbar-brand" style="font-size:14px" href="<?= $baseUrl . "colab/colab_list.php" ?>">Lista de
                    Colaboraciones</a>
            </li>
            <?php } ?>
            <li class="nav-item">
                <a class="navbar-brand" style="font-size:14px"
                    href="<?= $baseUrl . "actualiza_datos/index.php" ?>">Actualiza Datos</a>
            </li>
            <li class="nav-item">
                <a class="navbar-brand" style="font-size:14px" href="<?= $baseUrl . "rutas/index.php" ?>">Configura
                    Viajes</a>
            </li>
            <li class="nav-item">
                <a class="navbar-brand" style="font-size:14px"
                    href="<?= $baseUrl . "estadisticas/index.php" ?>">Estadísticas</a>
            </li>
            <li class="nav-item">
                <a class="navbar-brand" style="font-size:14px" href="<?= $baseUrl . "descarga/index.php" ?>">Descarga la
                    App</a>
            </li>
            <li class="nav-item pt-2">
                <a class="border navbar-brand px-1" style="font-size:13px" href="#"
                    onclick="confirmarCerrarSesion()">Cerrar Sesión</a>
            </li>
        </ul>
        <!-- Modal de confirmación -->
        <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-light">
                        <h5 class="modal-title" id="confirmModalLabel">Confirmar Cierre de Sesión</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true" class="text-light">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que deseas cerrar la sesión?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <a href="<?= $baseUrl . "login/cerrarsesion.php" ?>?logout=true" class="btn btn-danger">Cerrar
                            Sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Script para mostrar el modal de confirmación -->
    <script>
    function confirmarCerrarSesion() {
        $('#confirmModal').modal('show');
    }
    var phoneNumber = "+56975325574"; // Número de teléfono al que se enviará el mensaje

    // Función para abrir WhatsApp
    function openWhatsApp() {
        window.location.href = "whatsapp://send?phone=" + encodeURIComponent(phoneNumber);
    }
    </script>
</nav>
<?php
if ($indice != 'login' && $indice != 'colaboracion' && !$haColaborado && !$datosUsuario['admin']) {
?>
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    🚗 ¡Apoya el proyecto!
    Si esta aplicación te ha sido útil y quieres colaborar, puedes hacer un aporte para renovar el hosting.
    <a href="<?= $baseUrl . "colab/colab.php" ?>" class="alert-link">Haz clic aquí para ver cómo colaborar.</a>
</div>
<?php } ?>