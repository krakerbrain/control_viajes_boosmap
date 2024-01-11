<?php
include('../config.php');
$error = "false";
$creado = isset($_REQUEST['creado']) ? $_REQUEST['creado'] : "";
$cambio_clave = isset($_REQUEST['cambio_clave']) ? $_REQUEST['cambio_clave'] : "";

if (isset($_POST['usuario']) && isset($_POST['contrasenia'])) {
  $pass     = $_POST['contrasenia'];
  $usuario  = $_POST['usuario'];
  if ($pass != "" && $usuario != "") {
    $query = $con->prepare("SELECT count(*) as conteo, clave, activo, otrasapps, admin FROM usuarios WHERE nombre = :usuario");
    $query->bindParam(':usuario', $usuario);
    $query->execute();
    $result = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $datos) {
      if ($datos['conteo'] > 0 && $datos['activo'] == 1) {

        if (password_verify($pass, $datos['clave'])) {
          session_start();
          $_SESSION['usuario'] = $usuario;
          $_SESSION['admin'] = $datos['admin'] == 1 ? true : false;
          $_SESSION['otrasapps'] = $datos['otrasapps'] == 1 ? true : false;

          $sqlUsuario = $con->prepare("SELECT idusuario FROM usuarios WHERE nombre = :nombreUsuario");
          $sqlUsuario->bindParam(':nombreUsuario', $_SESSION['usuario']);
          $sqlUsuario->execute();

          $resultadoUsuario = $sqlUsuario->fetch(PDO::FETCH_ASSOC);
          $idusuario = $resultadoUsuario['idusuario'];

          // Realizar la consulta en la tabla viajes utilizando el idusuario
          $sqlViajes = $con->prepare("SELECT COUNT(*) as count FROM rutas WHERE idusuario = :idusuario");
          $sqlViajes->bindParam(':idusuario', $idusuario);
          $sqlViajes->execute();

          $resultadoViajes = $sqlViajes->fetch(PDO::FETCH_ASSOC);
          $count = $resultadoViajes['count'];

          if ($count > 0) {
            header("location:../index.php");
          } else {
            header("location:../rutas/index.php?creado=false");
          }
        } else {
          $error = "true";
          session_abort();
        }
      } else {
        $error =  $datos["activo"] == 0 ? "inactivo" : "noexiste";
        session_abort();
      }
    }
  } else {
    $error = "vacio";
    session_abort();
  }
}
include "../partials/header.php";
?>

<body class="bg-danger d-flex justify-content-center align-items-center vh-100">
  <div class="bg-white p-5 rounded">

    <div class="justify-content-center">
      <div class="">
        <form action="" method="post" class="form-group">
          <div class="text-center">
            <h4>CONTROL DE VIAJES</h4>
            <H4>BOOSMAP</H4>
          </div>
          <div class="form-group text-center mt-3">
            <div class="mb-3">
              <?php if ($creado == "true") { ?>
                <span class="text-danger fw-semibold">¡Se ha registrado correctamente!</span><br>
                <small>Por favor ingrese al sistema.</small>
              <?php } else if ($cambio_clave == "true") { ?>
                <span class="text-danger fw-semibold">¡El cambio de clave ha sido exitoso!</span><br>
                <small>Por favor ingrese al sistema.</small>
              <?php } ?>
            </div>
          </div>
          <div class="input-group">
            <div class="input-group-text bg-danger text-light">
              <i class="fa-solid fa-user"></i>
            </div>
            <input type="text" name="usuario" id="usuario" class="form-control" placeholder="Ingrese su usuario">
          </div>
          <div class="input-group mt-3">
            <div class="input-group-text bg-danger text-light">
              <i class="fa-solid fa-key"></i>
            </div>
            <input type="password" name="contrasenia" id="contrasenia" class="form-control" placeholder="Ingrese su contraseña">
            <div class="input-group-text bg-light">
              <a href="#" class="pe-auto text-danger">
                <i class="fa-solid fa-eye" onclick="verpass()"></i>
              </a>
            </div>
          </div>
          <div class="form-group mt-3">
            <input type="submit" value="Ingresar" class="btn btn-danger w-100">
          </div>
          <?php if ($error == "true") { ?>
            <span class=" d-flex justify-content-center mt-1">Password incorrecto.</span>
          <?php } else if ($error == "vacio") { ?>
            <span class=" d-flex justify-content-center mt-1">Debe llenar todos los campos.</span>
          <?php } else if ($error == "noexiste") { ?>
            <span class=" d-flex justify-content-center mt-1">Usuario No Existe.</span>
          <?php } else if ($error == "inactivo") { ?>
            <span class=" d-flex justify-content-center mt-1">Consulte al Administrador.</span>
          <?php } ?>
        </form>
        <?php if ($error != "inactivo") { ?>
          <div class="d-flex gap-1 justify-content-center mt-1">
            <div style="margin-right:5px">¿No tiene una cuenta?</div>
            <a href="registro.php" class="text-decoration-none text-danger fw-semibold">Registrese</a>
          </div>
          <a href="recupera.php" class="text-decoration-none">
            <p class="text-center text-danger">¿Olvidó su contraseña?</p>
          </a>
          <a href="<?= $_ENV['URL_DESCARGA'] ?>" class="text-decoration-none">
            <p class="text-center text-danger">Descarga la ANDROID APP <i class="fa-solid fa-download"></i></p>
          </a>
        <?php } ?>
        <script>
          function verpass() {
            var pass = document.getElementById('contrasenia');
            pass.type = pass.type == "password" ? "text" : "password"
          }
        </script>
        <?php
        include "../partials/footer.php";
        ?>