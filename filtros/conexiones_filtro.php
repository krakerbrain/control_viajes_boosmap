<?php
include __DIR__ . '/../config.php';
session_start();

if (isset($_SESSION['usuario'])) {
    $usuario = $_SESSION['usuario'];
}
$ingresar = $_REQUEST['ingresar'];
/**Se obtiene el id del usuario según el nombre que use al iniciar sesión */
$query = $con->prepare("SELECT idusuario FROM usuarios WHERE nombre = :usuario");
$query->bindParam(':usuario', $usuario);
$query->execute();
while ($datos = $query->fetch()) {
    $idusuario = $datos[0];
};

switch ($ingresar) {
    case 'getRutas':
        $query = $con->prepare("SELECT idruta, ruta FROM rutas WHERE idusuario = :idusuario");
        $query->bindParam(':idusuario', $idusuario);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $json_result = json_encode($result);
        echo $json_result;
        break;
    case 'getViajes':
        $ruta   = isset($_POST['ruta']) ? $_POST['ruta'] : "";
        $desde  = isset($_POST['desde']) ? $_POST['desde'] : "";
        $hasta  = isset($_POST['hasta']) ? $_POST['hasta'] : "";
        $orden = isset($_POST['orden']) ? $_POST['orden'] : "";

        $sql = "SELECT destino, count(destino) as conteo, SUM(monto) as ingreso
            FROM viajes
            WHERE 1 = 1 AND idusuario = :idusuario";

        // AND fecha BETWEEN '2023-12-01 00:00:00' AND '2023-12-30 23:59:59'

        // GROUP by destino asc

        // $sql = "SELECT *, DATE_FORMAT(fecha, '%d-%m-%Y') AS fecha FROM viajes WHERE 1=1 AND idusuario = :idusuario";

        if (!empty($ruta)) {
            $sql .= " AND destino = :ruta";
        }

        if (!empty($desde) && !empty($hasta)) {
            $sql .= " AND fecha BETWEEN CONCAT(:desde, ' 00:00:00') AND CONCAT(:hasta, ' 23:59:00')";
        } else {
            if (empty($desde) && !empty($hasta)) {
                $sql .= " AND fecha <= CONCAT(:hasta, ' 23:59:00')";
            } elseif (!empty($desde) && empty($hasta)) {
                $sql .= " AND fecha >= CONCAT(:desde, ' 00:00:00')";
            }
        }

        if (!empty($orden)) {
            $sql .= " " . $orden;
        }

        $query = $con->prepare($sql);
        $query->bindParam(':idusuario', $idusuario);

        if (!empty($ruta)) {
            $query->bindParam(':ruta', $ruta);
        }

        if (!empty($desde) && !empty($hasta)) {
            $query->bindParam(':desde', $desde);
            $query->bindParam(':hasta', $hasta);
        } else {
            if (empty($desde) && !empty($hasta)) {
                $query->bindParam(':hasta', $hasta);
            } elseif (!empty($desde) && empty($hasta)) {
                $query->bindParam(':desde', $desde);
            }
        }

        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        // Inicializa un nuevo array para almacenar los resultados
        $json_array = array();

        // Itera sobre cada fila en $result
        foreach ($result as $row) {
            // Agrega cada fila al nuevo array
            $json_array[] = $row;
        }

        // Convierte el nuevo array a formato JSON
        $json_result = json_encode($json_array);

        // Imprime o devuelve el resultado JSON
        echo $json_result;
        break;

    default:
        # code...
        break;
}
