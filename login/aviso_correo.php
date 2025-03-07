<?php
include('../config.php');

$correo =   isset($_GET['correo']) ? $_GET['correo'] : "";
$invalido = isset($_GET['invalido']) ? $_GET['invalido'] : "";
$indice = "login";
include "../partials/header.php";
?>

<body class="bg-danger d-flex justify-content-center align-items-center vh-100">
    <div class="bg-white p-5 rounded">
        <div class="justify-content-center">
            <form action="" method="post" class="form-group">
                <?php if (!$invalido) { ?>
                    <div class="card border-danger">
                        <div class="card-body">
                            <h3 class="card-title text-center text-danger mb-4">¡Correo enviado!</h3>
                            <hr class="mb-4">
                            <p class="card-text">Se ha enviado un correo a:</p>
                            <h5 class="card-subtitle mb-4 text-center text-danger"><?= $correo ?></h5>
                            <p class="card-text">con un enlace para el cambio de clave.</p>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="card border-danger">
                        <div class="card-body">
                            <h4 class="text-danger">El link ya no es válido</h4>
                        </div>
                    </div>
                    <a href="recupera.php" class="text-decoration-none">
                        <p class="text-center text-danger">¿Olvidó su contraseña?</p>
                    </a>
                <?php } ?>
                <div class="text-center">
                    <a href="<?= $baseUrl . "index.php" ?>">Ir al inicio</a>
                </div>
            </form>
            <?php
            include "../partials/footer.php";
            ?>