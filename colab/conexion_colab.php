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
        // Recibir los datos del frontend
        $idcolaboracion = $_POST['id'];
        $verificado = $_POST['verificado'];
        $numeroTransaccion = $_POST['numero_transaccion'];

        try {
            // Verificar si el número de transacción ya existe
            $checkNumTransQuery = $con->prepare("SELECT COUNT(*) FROM colaboraciones WHERE numero_transaccion = :numero_transaccion AND idcolaboracion != :idcolaboracion");
            $checkNumTransQuery->bindParam(':numero_transaccion', $numeroTransaccion);
            $checkNumTransQuery->bindParam(':idcolaboracion', $idcolaboracion);
            $checkNumTransQuery->execute();
            $numTransExistente = $checkNumTransQuery->fetchColumn();

            if ($numTransExistente > 0) {
                // Si el número de transacción ya existe, devolver un mensaje al frontend
                echo json_encode(["success" => false, "message" => "El número de transacción ya existe en otro registro."]);
            } else {
                // Si el número de transacción no existe, actualizar el registro
                $sql = $con->prepare("UPDATE colaboraciones SET verificado = :verificado, numero_transaccion = :numero_transaccion WHERE idcolaboracion = :idcolaboracion");
                $sql->bindParam(':idcolaboracion', $idcolaboracion);
                $sql->bindParam(':verificado', $verificado);
                $sql->bindParam(':numero_transaccion', $numeroTransaccion);

                if ($sql->execute()) {
                    echo json_encode(["success" => true, "message" => "Actualización exitosa."]);
                } else {
                    throw new Exception("Error al actualizar la colaboración.");
                }
            }
        } catch (Exception $e) {
            // Captura cualquier error y envíalo al frontend
            echo json_encode(["success" => false, "message" => "Error en la operación: " . $e->getMessage()]);
        }
        break;

    default:
        # code...
        break;
}
