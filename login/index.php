<?php

use Firebase\JWT\JWT;

require __DIR__ . '/../config.php';
require __DIR__ . '/../seguridad/JWT/jwt.php';

// Verificar si hay un error de token inválido (por actualización de la app)
$tokenError = isset($_GET['error']) && $_GET['error'] === 'invalid_token';

// Si el usuario ya tiene sesión válida, redirigir
$datosUsuario = validarToken();
if ($datosUsuario) {
  header("location:../index.php");
  exit;
}

// Manejo de errores de login
$error = false;
$errorType = '';
$creado = $_REQUEST['creado'] ?? "";
$nombre = $_REQUEST['usuario'] ?? "";
$cambio_clave = $_REQUEST['cambio_clave'] ?? "";

// Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario'], $_POST['contrasenia'])) {
  $usuario = trim($_POST['usuario']);
  $pass = $_POST['contrasenia'];

  if (empty($usuario) || empty($pass)) {
    $error = true;
    $errorType = 'vacio';
  } else {
    $query = $con->prepare("SELECT idusuario, clave, activo, otrasapps, admin FROM usuarios WHERE nombre = :usuario");
    $query->bindParam(':usuario', $usuario);
    $query->execute();
    $datos = $query->fetch(PDO::FETCH_ASSOC);

    if ($datos) {
      if ($datos['activo'] != 1) {
        $error = true;
        $errorType = 'inactivo';
      } elseif (password_verify($pass, $datos['clave'])) {
        generarTokenYConfigurarCookie($datos, $usuario);

        // Verificar si tiene rutas creadas
        $sqlViajes = $con->prepare("SELECT COUNT(*) as count FROM rutas WHERE idusuario = :idusuario");
        $sqlViajes->bindParam(':idusuario', $datos['idusuario']);
        $sqlViajes->execute();
        $resultadoViajes = $sqlViajes->fetch(PDO::FETCH_ASSOC);

        header("location: " . ($resultadoViajes['count'] > 0 ? "../index.php" : "../rutas/index.php?creado=false"));
        exit;
      } else {
        $error = true;
        $errorType = 'true';
      }
    } else {
      $error = true;
      $errorType = 'noexiste';
    }
  }
}

$indice = "login";
include "../partials/header.php";
?>

<body class="bg-danger d-flex justify-content-center align-items-center vh-100">
  <div class="bg-white p-2 p-sm-4 rounded" style="width: 100%; max-width: 400px;">

    <!-- Notificación por actualización de aplicación -->
    <?php if ($tokenError): ?>
      <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i> Hemos actualizado la aplicación. Por favor ingresa nuevamente.
      </div>
    <?php endif; ?>

    <div class="justify-content-center">
      <div class="">
        <form action="" method="post" class="form-group">
          <div class="text-center">
            <h4>CONTROL DE VIAJES</h4>
            <H4>BOOSMAP</H4>
          </div>

          <!-- Mensajes de éxito -->
          <div class="form-group text-center mt-3">
            <?php if ($creado === "true"): ?>
              <div class="alert alert-success py-1">
                <small>¡Registro exitoso! Ingresa usando tu nombre de usuario y contraseña.</small>
              </div>
            <?php elseif ($cambio_clave === "true"): ?>
              <div class="alert alert-success py-1">
                <small>¡Contraseña actualizada! Ingresa con tu nueva clave.</small>
              </div>
            <?php endif; ?>
          </div>

          <!-- Campo usuario -->
          <div class="input-group mb-3">
            <div class="input-group-text bg-danger text-light">
              <i class="fa-solid fa-user"></i>
            </div>
            <input type="text" name="usuario" id="usuario" class="form-control"
              placeholder="Ingrese su usuario" value="<?= isset($nombre) ? $nombre : "" ?>" required
              autofocus>
          </div>

          <!-- Campo contraseña -->
          <div class="input-group mb-3">
            <div class="input-group-text bg-danger text-light">
              <i class="fa-solid fa-key"></i>
            </div>
            <input type="password" name="contrasenia" id="contrasenia" class="form-control"
              placeholder="Ingrese su contraseña" required>
            <button type="button" class="input-group-text bg-light border-start-0" onclick="verpass()">
              <i class="fa-solid fa-eye text-danger"></i>
            </button>
          </div>

          <!-- Botón de submit -->
          <button type="submit" class="btn btn-danger w-100 mb-3">
            <i class="fas fa-sign-in-alt me-2"></i> Ingresar
          </button>

          <!-- Mensajes de error -->
          <?php if ($error): ?>
            <div class="alert alert-info py-2 text-center">
              <i class="fas fa-info-circle me-2"></i>
              <?php switch ($errorType):
                case 'true': ?> Credenciales incorrectas
                  <?php break; ?>
                <?php
                case 'vacio': ?> Complete todos los campos
                  <?php break; ?>
                <?php
                case 'noexiste': ?> Usuario no registrado
                  <?php break; ?>
                <?php
                case 'inactivo': ?> Cuenta inactiva. Contacte al administrador
                  <?php break; ?>
              <?php endswitch; ?>
            </div>
          <?php endif; ?>
        </form>

        <!-- Enlaces adicionales -->
        <?php if ($errorType !== 'inactivo'): ?>
          <div class="d-flex d-md-block flex-column text-center">
            <a href="registro.php" class="text-decoration-none text-danger fw-semibold me-2">
              <i class="fas fa-user-plus me-1"></i> Registrarse
            </a>
            <a href="recupera.php" class="text-decoration-none text-danger fw-semibold">
              <i class="fas fa-key me-1"></i> Recuperar contraseña
            </a>
          </div>
          <div class="text-center mt-3">
            <a href="<?= $baseUrl ?>descarga/app-release.apk" class="btn btn-outline-danger btn-sm">
              <i class="fa-solid fa-download me-1"></i> Descargar APP Android
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script>
    function verpass() {
      const passInput = document.getElementById('contrasenia');
      const icon = passInput.nextElementSibling.querySelector('i');
      if (passInput.type === "password") {
        passInput.type = "text";
        icon.classList.replace('fa-eye', 'fa-eye-slash');
      } else {
        passInput.type = "password";
        icon.classList.replace('fa-eye-slash', 'fa-eye');
      }
    }
  </script>

  <?php include "../partials/footer.php"; ?>
</body>