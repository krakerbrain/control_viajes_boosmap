<?php
include('../config.php');

$error = "";
$indice = "login";

if (isset($_POST['correo'])) {
    $correo  = $_POST['correo'];
    $query = $con->prepare("SELECT correo FROM usuarios WHERE correo = :correo AND activo = 1");
    $query->bindParam(':correo', $correo);
    $query->execute();
    $count = $query->rowCount();

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = '<p class="alert alert-danger">Formato de correo incorrecto</p>';
    } else {
        if ($count > 0) {
            $clave_recuperacion = bin2hex(random_bytes(16)); // 16 bytes = 32 caracteres en hexadecimal
            $stmt = $con->prepare("UPDATE usuarios SET clave = :clave WHERE correo = :correo");
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':clave', $clave_recuperacion);
            $stmt->execute();
            $correoEnviado = include(__DIR__ . '/../mail/recoveryMail.php');

            if ($correoEnviado) {
                header("Location: aviso_correo.php?correo=" . urlencode($correo));
                exit;
            } else {
                $error = '<p class="alert alert-danger">No se pudo enviar el correo. Inténtalo más tarde.</p>';
            }
        } else {
            $error = '<p class="alert alert-danger">El correo ' . $correo . ' no existe o está inactivo.</p>';
        }
    }
}


include "../partials/header.php";
?>

<body class="bg-danger d-flex justify-content-center align-items-center vh-100">
    <div class="bg-white p-5 rounded">
        <div class="justify-content-center">
            <form action="" method="post" class="form-group">
                <div class="text-center">
                    <h4>RECUPERACION DE CONTRASEÑA</h4>
                </div>
                <div class="input-group mt-2">
                    <div class="input-group-text bg-danger text-light">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <input type="mail" name="correo" id="correo" class="form-control" placeholder="Ingrese su correo">
                </div>
                <div class="form-group mt-3">
                    <input type="submit" value="Enviar" class="btn btn-danger w-100">
                </div>
                <div>
                    <a href="<?= $baseUrl . "index.php" ?>">Ir al inicio</a>
                </div>
                <div class="mt-3 text-center">
                    <?php echo $error ?>
                </div>
            </form>
            <?php
            include "../partials/footer.php";
            ?>