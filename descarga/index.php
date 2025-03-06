<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../seguridad/JWT/jwt.php';

include __DIR__ . "/../partials/header.php";
$datosUsuario = validarToken();
$indice = "descarga";


if (!$datosUsuario) {
    header("Location: " . $baseUrl . "login/index.php");
    exit;
}
?>

<body>
    <div class="container px-0" style="max-width:850px">
        <?php include __DIR__ . "/../partials/navbar.php"; ?>
        <div class="bg-danger text-light d-flex align-items-center justify-content-center mt-4"
            style="height:300px; width:100%">
            <div class="text-center">
                <h5>Puedes descargar la aplicación solo para Android</h5>
                <a class="btn btn-outline-light" href="<?= $baseUrl . "descarga/app-debug.apk" ?>" download>Descargar</a>
                <p class="mt-1" style="font-size:0.5rem">V. 4.3 11-07-2023</p>
            </div>
        </div>
    </div>
</body>
<?php include __DIR__ . "/../partials/boostrap_script.php" ?>