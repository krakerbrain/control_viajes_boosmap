<?php

/**Se recibe un parámetro que ejecuta el switch según lo que se recibe */
$ingresar   = $_POST['ingresar'];

require dirname(__DIR__) . '/config.php';
require dirname(__DIR__) . '/seguridad/JWT/jwt.php';


$ingresar  = $_POST['ingresar'];
$datosUsuario = validarToken();

if (!$datosUsuario) {
    // El token no es válido o no existe, manejar según sea necesario
    header("Location: " . $baseUrl . "login/index.php");
    exit;
}

$idusuario = $datosUsuario['idusuario'];

switch ($ingresar) {
    case 'colab_list':
        $sql = $con->prepare("SELECT *, u.nombre as user  FROM colaboraciones c INNER JOIN usuarios u ON c.idusuario = u.idusuario");
        $sql->execute();
        $datos = $sql->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($datos);
        break;
    case 'colaboracion_insert':

        $monto      = $_POST['monto'];

        $sql = $con->prepare("INSERT INTO colaboraciones(idusuario,monto) VALUES (:idusuario,:monto)");
        $sql->bindParam(':idusuario', $idusuario);
        $sql->bindParam(':monto', $monto);
        if ($sql->execute()) {
            echo "true";
        } else {
            echo "false";
        };
        break;
    case 'actualizar_verificado':

        // hacer update de columna verificado en true
        $idcolaboracion = $_POST['id'];
        $verificado = $_POST['verificado'];

        $sql = $con->prepare("UPDATE colaboraciones SET verificado = :verificado WHERE idcolaboracion = :idcolaboracion");
        $sql->bindParam(':idcolaboracion', $idcolaboracion);
        $sql->bindParam(':verificado', $verificado);
        if ($sql->execute()) {
            echo "true";
        } else {
            echo "false";
        }
    default:
        # code...
        break;
}
