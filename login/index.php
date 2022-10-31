<?php
include('../config.php');
$error = "false";
$creado = isset($_REQUEST['creado']);
if(isset($_POST['usuario']) && isset($_POST['contrasenia'])){
  $pass     = $_POST['contrasenia'];
  $usuario  = $_POST['usuario'];
  if($pass != "" && $usuario != ""){
    $query = $con->prepare("SELECT count(*) as conteo, clave FROM usuarios WHERE nombre = :usuario");
    $query->bindParam(':usuario', $usuario);
    $query->execute();
    $result = $query->fetchAll(PDO::FETCH_ASSOC);

  foreach($result as $datos){
    if($datos['conteo'] > 0){
      
      if(password_verify($pass,$datos['clave'])){
        header("location:../index.php");
        session_start();
        $_SESSION['usuario'] = $usuario;
      }else{
        $error = "true";
        session_abort();
      }
    }else{
      $error = "true";
      session_abort();
    }
  }
}else{
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
            <h5 class="mb-3">Ingresar</h5>
            <?php if($creado == "true"){ ?>
              <div class="mb-3">
                <span class="text-danger fw-semibold">¡Se ha registrado correctamente!</span><br>
                <small>Por favor ingrese al sistema.</small>
              </div>
            <?php } ?>
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
        </div>
        <div class="form-group mt-3">
          <input type="submit" value="Ingresar" class="btn btn-danger w-100">
        </div>
        <?php if($error == "true"){ ?>
          <span class=" d-flex justify-content-center mt-1">Datos incorrectos.</span>
        <?php }else if($error == "vacio") { ?>
            <span class=" d-flex justify-content-center mt-1">Debe llenar todos los campos</span>
        <?php } ?>
    </form>
    <div class="d-flex gap-1 justify-content-center mt-1">
      <div>¿No tiene una cuenta?</div>
      <a href="registro.php" class="text-decoration-none text-danger fw-semibold">Registrese</a>
    </div>
<?php
include "../partials/footer.php";  
?>