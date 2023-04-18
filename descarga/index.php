<?php
session_start();
$sesion = isset($_SESSION['usuario']);

require __DIR__ . '/../config.php';
include __DIR__."/../partials/header.php"; 
$indice = "descarga";

?>

<body>
    <div class="container px-0" style="max-width:850px">
        <?php include __DIR__."/../partials/navbar.php"; ?>
        <div class="bg-danger text-light d-flex align-items-center justify-content-center mt-4" style="height:300px; width:100%">
            <div class="text-center">
                <h5>Puedes descargar la aplicación solo para Android</h5>
                <a class="btn btn-outline-light" href="<?= $_ENV['URL_DESCARGA'] ?>" download>Descargar</a>
                <p class="mt-1" style="font-size:0.5rem">V. 4.2 15-04-2023</p>
            </div>
        </div>
    </div>
</body>
        <?php include __DIR__."/../partials/boostrap_script.php" ?>