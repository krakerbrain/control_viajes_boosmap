<?php
include('../config.php');
$error = "";
$cambio_clave = "false";
$correo = $_GET['correo'];
if(isset($_POST['password']) && isset($_POST['password2'])){
    $pass     = $_POST['password'];
    $pass2    = $_POST['password2'];
    if($pass  == "" || $pass2 == "" ){
        $error = '<p class="alert alert-danger">Registro Incorrecto. Debe llenar todos los campos</p>';
    }else if($pass != $pass2){
        $error = '<p class="alert alert-danger">Las contraseñas deben ser iguales</p>';
    }else{
        $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 7]);
        $query = $con->prepare("UPDATE usuarios SET clave = :clave WHERE correo = :correo");
        $query->bindParam(':correo', $correo);
        $query->bindParam(':clave', $hash);
        $query->execute();
        $cambio_clave = "true";
        header("location:index.php?cambio_clave=".$cambio_clave);
    }
}
include "../partials/header.php";  
?>
<body class="bg-danger d-flex justify-content-center align-items-center vh-100">
    <div class="bg-white p-5 rounded">
        <div class="justify-content-center">
            <form action="" method="post" class="form-group">
                <div class="text-center">
                    <h5>REGISTRO DE NUEVA CLAVE</h5>
                </div>
                <div class="input-group mt-2">
                    <div class="input-group-text bg-danger text-light">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <input type="mail" name="correo" id="correo" class="form-control" placeholder="<?= $correo ?>" disabled>
                </div>
                <div class="input-group mt-2">
                    <div class="input-group-text bg-danger text-light">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Ingrese nueva clave">
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
                    <input type="password" name="password2" id="password2" class="form-control" placeholder="Repita nueva clave">
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
            </script>
<?php
include "../partials/footer.php";  
?> 