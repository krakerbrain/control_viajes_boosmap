<?php
include('../config.php');
$creado = "false";
$error = "";

if(isset($_POST['usuario']) && isset($_POST['correo']) &&isset($_POST['password']) && isset($_POST['password2'])){
    $pass     = $_POST['password'];
    $pass2    = $_POST['password2'];
    $usuario  = $_POST['usuario'];
    $correo  = $_POST['correo'];
    $query = $con->prepare("SELECT correo FROM usuarios WHERE correo = :correo");
    $query->bindParam(':correo', $correo);
    $query->execute();
    $count = $query->rowCount();
    if($_POST['usuario'] == "" || $_POST['correo']  == "" || $_POST['password']  == "" || $_POST['password2'] == "" ){
     
        $error = '<p class="alert alert-danger">Registro Incorrecto. Debe llenar todos los campos</p>';
    }else if($count > 0){
        $error = '<p class="alert alert-danger">Este correo ya ha sido registrado. Intente de nuevo</p>';
    }else if(!preg_match(" /^(([^<>()\[\]\.,;:\s@\”]+(\.[^<>()\[\]\.,;:\s@\”]+)*)|(\”.+\”))@(([^<>()[\]\.,;:\s@\”]+\.)+[^<>()[\]\.,;:\s@\”]{2,})$/", $correo)){
        $error = '<p class="alert alert-danger">Formato de correo incorrecto</p>';
    }else if($pass != $pass2){
        $error = '<p class="alert alert-danger">Las contraseñas deben ser iguales</p>';
      
    }else{
        $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 7]);
        $query = $con->prepare("INSERT INTO usuarios(nombre,correo,clave) VALUES (:nombre,:correo,:clave)");
        $query->bindParam(':nombre', $usuario);
        $query->bindParam(':correo', $correo);
        $query->bindParam(':clave', $hash);
        $query->execute();
        $count2 = $query->rowCount();

        if($count2){
          $creado = "true";
          header("location:index.php?creado=".$creado);
        }else{
         $error = "conex";
        }
    }


 
}
  
include "../partials/header.php";  
?>
    <div class="form-group text-center mt-3">
        <h5>Registro</h5>
    </div>
    <div class="input-group">
        <div class="input-group-text bg-danger text-light">
            <i class="fa-solid fa-user"></i>
        </div>
        <input type="text" name="usuario" id="usuario" class="form-control" placeholder="Ingrese un nombre de usuario" ">
    
    </div>
    <div class="input-group mt-2">
        <div class="input-group-text bg-danger text-light">
            <i class="fa-solid fa-envelope"></i>
        </div>
        <input type="mail" name="correo" id="correo" class="form-control" placeholder="Ingrese un correo" >
    </div>
    <div class="input-group mt-2">
          <div class="input-group-text bg-danger text-light">
            <i class="fa-solid fa-key"></i>
          </div>
          <input type="password" name="password" id="password" class="form-control" placeholder="Ingrese una clave">
          <div class="input-group-text bg-light">
            <a href="#" class="pe-auto text-danger">
                <i class="fa-solid fa-eye" onclick="verpass(1)"></i>
            </a>  
          </div>
    </div>
    <div class="input-group mt-2">
          <div class="input-group-text bg-danger text-light">
            <i class="fa-solid fa-key"></i>
          </div>
          <input type="password" name="password2" id="password2" class="form-control" placeholder="Ingrese otra vez">
          <div class="input-group-text bg-light">
            <a href="#" class="pe-auto text-danger">
                <i class="fa-solid fa-eye" onclick="verpass(2)"></i>
            </a>  
          </div>
    </div>
        <div class="form-group mt-3">
            <input type="submit" value="Enviar" class="btn btn-danger w-100">
        </div>
        <div class="mt-3 text-center">
            <?php echo $error ?>
        </div>
<div>
    <a href="index.php">Ir al inicio</a>
    <!-- <a href="http://biowork.tech/login/inicio.php">Ir al inicio</a> -->
</div>

    </form>
    <script>
        function verpass(param){
            var pass1 = document.getElementById('password');
            var pass2 = document.getElementById('password2');
            if(param == 1){ 
                pass1.type = pass1.type == "password" ? "text" : "password"
            }else{
                pass2.type = pass2.type == "password" ? "text" : "password"
            }
        }
 
       <?php if($error == "correo"){ ?>
            document.getElementById('correo').focus();
            <?php }else if($error == "vacio"){ ?>
                document.getElementById('usuario').focus();
        <?php } ?>
    </script>
<?php
include "../partials/footer.php";  
?>