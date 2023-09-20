<?php
include('config.php');
session_start();
if (isset($_SESSION['usuario'])) {
  $usuario = $_SESSION['usuario'];
}

/**Se recibe un parámetro que ejecuta el switch según lo que se recibe */
$ingresar   = $_POST['ingresar'];

/**Se obtiene el id del usuario según el nombre que use al iniciar sesión */
$query = $con->prepare("SELECT idusuario FROM usuarios WHERE nombre = :usuario");
$query->bindParam(':usuario', $usuario);
$query->execute();
while ($datos = $query->fetch()) {
  $idusuario = $datos[0];
};


switch ($ingresar) {
  case 'insertar':
    $destino    = $_POST['destino'];
    $dia      = date("Y-m-d", strtotime(str_replace('/', '-', $_POST['dia'])));
    $hora      = $_POST['hora'];
    $fecha = $dia . " " . $hora;
    $sql = $con->prepare("INSERT INTO viajes(idusuario,destino,fecha,monto) VALUES (:idusuario,:destino,:fecha,(SELECT costoruta from rutas where idusuario = :idusuario and ruta = :destino limit 1))");
    $sql->bindParam(':idusuario', $idusuario);
    $sql->bindParam(':destino', $destino);
    $sql->bindParam(':fecha', $fecha);
    $sql->execute();
    break;

  case 'obtener':
    $limit   = $_POST['limit'];
    $offset  = $_POST['offset'];

    // Obtener el conteo total de filas
    $queryTotalFilas = $con->prepare("SELECT COUNT(*) as totalFilas FROM viajes WHERE idusuario = :idusuario AND MONTH(fecha) = MONTH(NOW())");
    $queryTotalFilas->bindParam(':idusuario', $idusuario);
    $queryTotalFilas->execute();
    $totalFilas = $queryTotalFilas->fetch(PDO::FETCH_ASSOC)['totalFilas'];

    // Consulta para obtener los datos paginados
    $query = $con->prepare("SELECT *, DATE_FORMAT(fecha, '%d-%m-%Y') AS fecha FROM viajes WHERE idusuario = :idusuario AND MONTH(fecha) = MONTH(NOW()) AND YEAR(fecha) = YEAR(NOW()) ORDER BY CASE WHEN fecha < CURDATE() THEN fecha ELSE CURDATE() END DESC, idviaje DESC LIMIT :limit OFFSET :offset");
    $query->bindParam(':idusuario', $idusuario);
    $query->bindParam(':limit', $limit, PDO::PARAM_INT); // Parámetro de límite de filas por página
    $query->bindParam(':offset', $offset, PDO::PARAM_INT); // Parámetro de inicio de filas
    $query->execute();
    $datos = $query->fetchAll(PDO::FETCH_ASSOC);
    $json = json_encode(array("data" => $datos, "cantidadFilas" => $totalFilas)); // Agregar el conteo total de filas al JSON
    echo $json;
    break;


  case 'cargarutas':
    $query = $con->prepare("SELECT * FROM rutas WHERE idusuario = :idusuario");
    $query->bindParam(':idusuario', $idusuario);
    $query->execute();
    $datos = $query->fetchAll(PDO::FETCH_ASSOC);
    foreach ($datos as $ruta) {
      echo '<button value="' . $ruta['ruta'] . '" class="btn btn-danger mr-1" onclick="agregaRuta(this)" style="font-size:0.6em">' . $ruta['ruta'] . '</button>';
    };
    break;
  case 'totalmes';
    $mes = $_POST['periodo'];
    try {
      // En producción se debe verificar la hora del servidor. Hostinger por ejemplo tiene una diferencia de -03:00. 
      $query = $con->prepare('CALL detalles_viajes(?,?, @viajes, @total)');
      $query->bindParam(1, $idusuario, PDO::PARAM_INT);
      $query->bindParam(2, $mes, PDO::PARAM_STR_CHAR);
      $query->execute();
      $query->closeCursor();

      $select = $con->query('SELECT @viajes as viajes, @total as monto_total');
      $datos = $select->fetchAll(PDO::FETCH_ASSOC);
      $json = json_encode($datos);
      echo $json;
    } catch (PDOException $e) {
      die("Error occurred:" . $e->getMessage());
    }
    break;
  case 'eliminar';
    $idviaje = $_POST['id_viaje'];
    $query = $con->prepare("DELETE FROM viajes WHERE idviaje = :idviaje AND idusuario = :idusuario");
    $query->bindParam(':idviaje', $idviaje);
    $query->bindParam(':idusuario', $idusuario);
    $query->execute();
    break;

  default:
    # code...
    break;
}