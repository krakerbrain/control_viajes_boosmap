<?php
include('../config.php');


$correo =  $_GET['correo'];
$clave =  $_GET['clave'];

$query = $con->prepare("SELECT correo FROM usuarios WHERE correo = :correo AND clave = :clave");
$query->bindParam(':correo', $correo);
$query->bindParam(':clave', $clave);
$query->execute();
$count = $query->rowCount();

if($count > 0){
    header("location:nueva_clave.php?correo=".$correo."");
}else{
    header("location:aviso_correo.php?invalido=true");
}