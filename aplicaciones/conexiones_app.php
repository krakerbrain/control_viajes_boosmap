<?php
date_default_timezone_set('America/Santiago');
$fechaHoy = date('Y-m-d');
$fechayHoraHoy = date('Y-m-d H:i:s');

require __DIR__ . '/../config.php';
require __DIR__ . '/../seguridad/JWT/jwt.php';

$datosUsuario = validarToken();
$ingresar = $_REQUEST['ingresar'];
if (!$datosUsuario) {
    header($_ENV['URL_LOCAL']);
    exit;
}

$idusuario = $datosUsuario['idusuario'];

switch ($ingresar) {
    case 'agregaApp':
        /**
         * Hay que verificar si la aplicacion ya existe para que no se agregue dos veces
         */
        $nombreApp    = $_POST['nombreApp'];

        // Consulta para verificar si la aplicación ya existe
        $query = $con->prepare("SELECT id FROM aplicaciones WHERE nombre_app = :nombreApp");
        $query->bindParam(':nombreApp', $nombreApp);
        $query->execute();
        $result = $query->fetch();

        // Si el resultado no es nulo, significa que la aplicación ya existe
        if ($result) {
            // Mostrar un mensaje de error
            echo "duplicate";
        } else {
            // La aplicación no existe, se puede agregar
            $sql = $con->prepare("INSERT INTO aplicaciones (idusuario, nombre_app, active, mostrar)VALUES (:idusuario, :nombreApp, 1, 1)");
            $sql->bindParam(':idusuario', $idusuario);
            $sql->bindParam(':nombreApp', $nombreApp);
            $sql->execute();
            echo $nombreApp;
        }
        break;
    case 'appRegistradas':
        $nombreApp = isset($_POST['nombreApp']) ?  $_POST['nombreApp'] : '';
        try {

            $query = $con->prepare("SELECT id,nombre_app FROM aplicaciones WHERE idusuario = :idusuario AND active = 1 ");
            $query->bindParam(':idusuario', $idusuario);

            // Verifica si la variable $nombre no está vacía
            if (!empty($nombreApp)) {
                // Agrega una condición adicional
                $query->queryString .= " AND nombre_app = $nombreApp";
            }

            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $json_result = json_encode($result);
            echo $json_result;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        break;
    case 'registraGanancia':
        $idApp = $_POST['idApp'];
        $monto = $_POST['monto'];
        $fecha = (!empty($_POST['fecha'])) ? $_POST['fecha'] : $fechaHoy;
        $fechaYHora = (!empty($_POST['fechaYHora'])) ? $_POST['fechaYHora'] : $fechayHoraHoy;


        try {
            $query = $con->prepare('CALL RegistraGanancia(?,?,?,?,?)');
            $query->bindParam(1, $idApp, PDO::PARAM_INT);
            $query->bindParam(2, $monto, PDO::PARAM_INT);
            $query->bindParam(3, $idusuario, PDO::PARAM_INT);
            $query->bindParam(4, $fecha, PDO::PARAM_STR);
            $query->bindParam(5, $fechaYHora, PDO::PARAM_STR);
            $query->execute();

            // Obtener los resultados directamente de la rutina
            $datos = $query->fetchAll(PDO::FETCH_ASSOC);
            $json = json_encode($datos);
            echo $json;
        } catch (PDOException $e) {
            die("Error occurred:" . $e->getMessage());
        }

        break;

    case 'ultimasGanancias':
        $query = $con->prepare("SELECT v.id_viajes_app as id, a.nombre_app, v.monto, DATE_FORMAT(v.fecha, '%d-%m-%Y %H:%i:%s') as fecha
                                    FROM aplicaciones a 
                                    JOIN viajes_aplicaciones v 
                                    ON a.id = v.idapp 
                                    WHERE v.idusuario = :idusuario 
                                    AND a.active = 1  
                                    ORDER BY v.fecha DESC
                                    LIMIT 5");
        $query->bindParam(':idusuario', $idusuario);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $json_result = json_encode($result);
        echo $json_result;
        break;
    case 'borrarGanancia':
        $id = $_POST['id'];
        $sql = $con->prepare("DELETE FROM viajes_aplicaciones WHERE id_viajes_app = :id ");
        $sql->bindParam(':id', $id);
        if ($sql->execute()) {
            echo 'ok';
        } else {
            echo 'error';
        }
        break;
    case 'obtenerDataApps':
        // Llamar al procedimiento almacenado para obtener los datos de forma periódica
        $query = $con->prepare('CALL obtenerDatosPeriodicos(?,?)');
        $query->bindParam(1, $idusuario, PDO::PARAM_INT);
        $query->bindParam(2, $fechaHoy, PDO::PARAM_STR_CHAR);
        $query->execute();

        // Obtener los resultados directamente de la rutina
        $datosPeriodicos = $query->fetchAll(PDO::FETCH_ASSOC);

        // Consultar y procesar los datos para las demás aplicaciones
        $query = $con->prepare("SELECT a.id AS idapp, a.nombre_app,
                                       SUM(CASE WHEN DATE(fecha) = :fechaHoy THEN monto ELSE 0 END) AS monto_dia,
                                       SUM(CASE WHEN YEARWEEK(fecha,1) = YEARWEEK(:fechaHoy,1) THEN monto ELSE 0 END) AS monto_semana,
                                       SUM(CASE WHEN YEAR(fecha) = YEAR(:fechaHoy) AND MONTH(fecha) = MONTH(:fechaHoy) THEN monto ELSE 0 END) AS monto_mes
                                FROM aplicaciones a
                                LEFT JOIN viajes_aplicaciones va 
                                ON a.id = va.idapp 
                                AND va.idusuario = :idusuario
                                GROUP BY a.id, a.nombre_app;");
        $query->bindParam(':idusuario', $idusuario);
        $query->bindParam(':fechaHoy', $fechaHoy);
        $query->execute();
        $otrosDatos = $query->fetchAll(PDO::FETCH_ASSOC);

        // Combinar los dos arreglos en uno solo
        $datosCombinados = array_merge($datosPeriodicos, $otrosDatos);
        // Insertar los datos en el archivo JSON
        echo insertarDatosEnArchivoJSON($datosCombinados);
        break;
    default:
        # code...
        break;
}
function insertarDatosEnArchivoJSON($datos)
{
    // Crear un nuevo objeto totalApps
    $totalApps = array();

    foreach ($datos as $row) {
        $nombre_app = $row['nombre_app'];
        $monto_dia = $row['monto_dia'];
        $monto_semana = $row['monto_semana'];
        $monto_mes = $row['monto_mes'];

        $totalApps[$nombre_app] = array(
            'dia' => $monto_dia,
            'semana' => $monto_semana,
            'mes' => $monto_mes
        );
    }
    return json_encode($totalApps);
}
