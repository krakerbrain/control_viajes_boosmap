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
    case 'totalesPeriodo';

        try {
            $query = $con->prepare('CALL obtenerDatosPeriodicos(?)');
            $query->bindParam(1, $idusuario, PDO::PARAM_INT);
            $query->execute();

            // Obtener los resultados directamente de la rutina
            $datos = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($datos as &$fila) {
                foreach ($fila as $clave => &$valor) {
                    if ($valor === null) {
                        $valor = 0;
                    }
                }
            }
            $json = json_encode($datos);
            echo $json;
        } catch (PDOException $e) {
            die("Error occurred:" . $e->getMessage());
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
                // var_dump($query->queryString);
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

        // Verificar si ya existe un registro para hoy
        date_default_timezone_set('America/Santiago');
        $fechaHoy = date('Y-m-d');
        $fechayHoraHoy = date('Y-m-d H:i:s');

        try {
            $query = $con->prepare('CALL RegistraGanancia(?,?,?,?,?)');
            $query->bindParam(1, $idApp, PDO::PARAM_INT);
            $query->bindParam(2, $monto, PDO::PARAM_INT);
            $query->bindParam(3, $idusuario, PDO::PARAM_INT);
            $query->bindParam(4, $fechaHoy, PDO::PARAM_STR);
            $query->bindParam(5, $fechayHoraHoy, PDO::PARAM_STR);
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
        $query = $con->prepare("SELECT idapp,
                                    SUM(CASE WHEN DATE(fecha) = CURDATE() THEN monto ELSE 0 END) AS monto_dia,
                                    SUM(CASE WHEN YEARWEEK(fecha,1) = YEARWEEK(CURDATE(),1) THEN monto ELSE 0 END) AS monto_semana,
                                    SUM(CASE WHEN YEAR(fecha) = YEAR(CURDATE()) AND MONTH(fecha) = MONTH(CURDATE()) THEN monto ELSE 0 END) AS monto_mes
                                FROM viajes_aplicaciones
                                WHERE idusuario = :idusuario
                                GROUP BY idapp;");
        $query->bindParam(':idusuario', $idusuario);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $json_result = json_encode($result);
        echo $json_result;
        break;
    case 'totalesMes':
        $query = $con->prepare("SELECT
        SUM(monto_dia) AS total_monto_dia,
        SUM(monto_semana) AS total_monto_semana,
        SUM(monto_mes) AS total_monto_mes
    FROM (
        SELECT
            SUM(CASE WHEN DATE(fecha) = CURDATE() THEN monto ELSE 0 END) AS monto_dia,
            SUM(CASE WHEN YEARWEEK(fecha,1) = YEARWEEK(CURDATE(),1) THEN monto ELSE 0 END) AS monto_semana,
            SUM(CASE WHEN YEAR(fecha) = YEAR(CURDATE()) AND MONTH(fecha) = MONTH(CURDATE()) THEN monto ELSE 0 END) AS monto_mes
        FROM viajes
        WHERE YEAR(fecha) = YEAR(CURDATE())
        AND idusuario = :idusuario
        UNION ALL
        SELECT
            SUM(CASE WHEN DATE(fecha) = CURDATE() THEN monto ELSE 0 END) AS monto_dia,
            SUM(CASE WHEN YEARWEEK(fecha,1) = YEARWEEK(CURDATE(),1) THEN monto ELSE 0 END) AS monto_semana,
            SUM(CASE WHEN YEAR(fecha) = YEAR(CURDATE()) AND MONTH(fecha) = MONTH(CURDATE()) THEN monto ELSE 0 END) AS monto_mes
        FROM viajes_aplicaciones
        WHERE YEAR(fecha) = YEAR(CURDATE())
        AND idusuario = :idusuario
    ) AS subquery_total;
    ");
        $query->bindParam(':idusuario', $idusuario);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $json_result = json_encode($result);
        echo $json_result;

        break;

    default:
        # code...
        break;
}
