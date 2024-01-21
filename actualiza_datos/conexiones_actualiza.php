<?php
include __DIR__ . '/../config.php';
/**Se recibe un parámetro que ejecuta el switch según lo que se recibe */
$ingresar   = $_POST['ingresar'];
$idusuario = $_POST['idusuario'];


switch ($ingresar) {
    case 'actualizar':
        // Aquí puedes escribir la lógica para la acción "actualizar"
        // Puedes acceder a los datos enviados desde el formulario a través de $_POST
        // Ejemplo:
        $correo = isset($_POST['correo']) ? $_POST['correo'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $password2 = isset($_POST['password2']) ? $_POST['password2'] : '';
        $pattern = '/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/';

        // Verificar igualdad de contraseñas
        if ($correo !== "" && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $error = "Formato de correo incorrecto.";
            echo $error;
        } else if ($password !== $password2) {
            $error = "Las contraseñas no coinciden.";
            echo $error;
        } else {
            $query = $con->prepare("SELECT clave FROM usuarios WHERE idusuario = :idusuario");
            $query->bindParam(':idusuario', $idusuario);
            $query->execute();
            $result = $query->fetch();
            if ($result !== null) {
                if (password_verify($password, $result['clave'])) {
                    $respuesta = 'No han habido cambios';
                    echo $respuesta;
                } else {
                    $hash = $password != "" ? password_hash($password, PASSWORD_BCRYPT, ['cost' => 7]) : $password;
                    // Llamar al procedimiento almacenado utilizando PDO
                    $sqlCallSP = "CALL ActualizarUsuario(:idusuario, :correo, :password, @respuesta, @v_respuesta)";
                    $stmt = $con->prepare($sqlCallSP);
                    $stmt->bindParam(':idusuario', $idusuario);
                    $stmt->bindParam(':correo', $correo);
                    $stmt->bindParam(':password', $hash);
                    $stmt->execute();

                    // Obtener la respuesta del procedimiento almacenado
                    $stmt->closeCursor();
                    $stmt = $con->query("SELECT @respuesta AS respuesta, @v_respuesta AS v_respuesta");
                    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

                    $respuesta = $resultado['respuesta'];
                    $v_respuesta = $resultado['v_respuesta'];

                    // Decodificar el objeto JSON en un arreglo asociativo
                    $v_respuestaArray = json_decode($v_respuesta, true);
                    // Acceder a los valores individuales del arreglo asociativo
                    $correo = $v_respuestaArray['correo'];
                    $clave = $v_respuestaArray['clave'];
                    $correoActual = $v_respuestaArray['correo_actual'];

                    // Mostrar la respuesta del procedimiento almacenado
                    $respuestaJson = json_encode([
                        'respuesta' => $respuesta,
                        'correo' => $correo,
                        'clave' => $clave,
                        'correo_actual' => $correoActual
                    ]);

                    echo $respuestaJson;
                }
            }
        }
        break;


    default:
        # code...
        break;
}
