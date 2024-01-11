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

  case 'getUltimosViajes':
    $limit   = $_POST['limit'];
    $offset  = $_POST['offset'];

    // Obtener el conteo total de filas
    $totalFilas = obtenerTotalFilas($con, $idusuario);

    // Consulta para obtener los datos paginados
    $datos = obtenerDatosPaginados($con, $idusuario, $limit, $offset);

    $json = json_encode(array("data" => $datos, "cantidadFilas" => $totalFilas));
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
    // Establece la semana para que comience el lunes (1).
    date_default_timezone_set('UTC'); // Ajusta la zona horaria si es necesario.
    setlocale(LC_TIME, 'es_ES'); // Establece la configuración regional si es necesario.

    $today = date('Y-m-d'); // Obtiene la fecha actual.

    // Obtiene la fecha del inicio de la semana (lunes) para la fecha actual.
    $inicioSemana = date('Y-m-d', strtotime('monday this week', strtotime($today)));
    $finSemana = date('Y-m-d', strtotime($inicioSemana . ' +6 days'));

    try {
      // En producción se debe verificar la hora del servidor. Hostinger por ejemplo tiene una diferencia de -03:00. 
      $query = $con->prepare('CALL detalles_viajes(?,?,?,?,@viajes, @total)');
      $query->bindParam(1, $idusuario, PDO::PARAM_INT);
      $query->bindParam(2, $mes, PDO::PARAM_STR_CHAR);
      $query->bindParam(3, $inicioSemana, PDO::PARAM_STR_CHAR);
      $query->bindParam(4, $finSemana, PDO::PARAM_STR_CHAR);
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
  case 'agregaDetalles':
    // Verificar si se ha recibido la clave 'datos' en la solicitud POST
    if (isset($_POST['datos'])) {
      // Decodificar la cadena JSON en un array asociativo
      $datos = json_decode($_POST['datos'], true);

      // Verificar si la decodificación fue exitosa
      if ($datos !== null) {
        foreach ($datos as $data) {
          if (strpos($data['id'], 'fila_') !== false) {
            $tablaDetalle = strtolower($data['descripcion']) . 's';

            $sql = $con->prepare("INSERT INTO $tablaDetalle(idusuario,idviaje,monto) VALUES (:idusuario,:idviaje,:monto)");
            $sql->bindParam(':idusuario', $idusuario);
            $sql->bindParam(':idviaje', $data['idViaje']);
            $sql->bindParam(':monto', $data['monto']);
            $sql->execute();
          }
        }
        echo "true";
      } else {
        // La decodificación JSON falló
        echo "Error al decodificar los datos JSON.";
      }
    } else {
      // La clave 'datos' no está presente en la solicitud POST
      echo "No se recibieron datos.";
    }
    break;

  case 'getDetalles':
    $idviaje = $_POST['idViaje'];
    // Supongamos que tienes una conexión PDO llamada $con y una variable $idusuario definida

    // Array asociativo para almacenar la información de extras y peajes
    $resultado = array();

    // Consulta para obtener datos de la tabla 'extras'
    $queryExtras = $con->prepare("SELECT id, idviaje, monto FROM extras WHERE idusuario = :idusuario AND idviaje = :idviaje");
    $queryExtras->bindParam(':idusuario', $idusuario);
    $queryExtras->bindParam(':idviaje', $idviaje);
    $queryExtras->execute();
    $datosExtras = $queryExtras->fetchAll(PDO::FETCH_ASSOC);

    // Estructurar datos de 'extras' y agregar al resultado
    foreach ($datosExtras as $datoExtra) {
      $resultado[] = array(
        'id' => $datoExtra['id'],
        'idViaje' => $datoExtra['idviaje'],
        'descripcion' => 'Extra',
        'monto' => $datoExtra['monto']
      );
    }

    // Consulta para obtener datos de la tabla 'peajes'
    $queryPeajes = $con->prepare("SELECT id,idviaje, monto FROM peajes WHERE idusuario = :idusuario AND idviaje = :idviaje");
    $queryPeajes->bindParam(':idusuario', $idusuario);
    $queryPeajes->bindParam(':idviaje', $idviaje);
    $queryPeajes->execute();
    $datosPeajes = $queryPeajes->fetchAll(PDO::FETCH_ASSOC);

    // Estructurar datos de 'peajes' y agregar al resultado
    foreach ($datosPeajes as $datoPeaje) {
      $resultado[] = array(
        'id' => $datoPeaje['id'],
        'idViaje' => $datoPeaje['idviaje'],
        'descripcion' => 'Peaje',
        'monto' => $datoPeaje['monto']
      );
    }

    // Imprimir el resultado como JSON
    echo json_encode($resultado);
    break;
  case 'eliminaDataDetalle':
    $id = $_POST['id'];
    $descripcion = $_POST['descripcion'];
    $tablaDetalle = strtolower($descripcion) . 's';

    $query = $con->prepare("DELETE FROM $tablaDetalle WHERE id = :id AND idusuario = :idusuario");
    $query->bindParam(':id', $id);
    $query->bindParam(':idusuario', $idusuario);
    if ($query->execute()) {
      echo "true";
    } else {
      echo "false";
    };

    break;

  default:
    # code...
    break;
}

function obtenerTotalFilas($con, $idusuario)
{
  $queryTotalFilas = $con->prepare("SELECT COUNT(*) as totalFilas FROM viajes WHERE idusuario = :idusuario AND MONTH(fecha) = MONTH(NOW())");
  $queryTotalFilas->bindParam(':idusuario', $idusuario);
  $queryTotalFilas->execute();
  return $queryTotalFilas->fetch(PDO::FETCH_ASSOC)['totalFilas'];
}

function obtenerDatosPaginados($con, $idusuario, $limit, $offset)
{
  $query = $con->prepare("SELECT 
                            viajes.*,
                            DATE_FORMAT(viajes.fecha, '%d-%m-%Y') AS fecha,
                            EXISTS (SELECT 1 FROM peajes WHERE peajes.idviaje = viajes.idviaje 
                                    OR EXISTS (SELECT 1 FROM extras WHERE extras.idviaje = viajes.idviaje)) AS tiene_detalles
                        FROM 
                            viajes
                        WHERE 
                            viajes.idusuario = :idusuario 
                            AND MONTH(viajes.fecha) = MONTH(NOW()) 
                            AND YEAR(viajes.fecha) = YEAR(NOW()) 
                        ORDER BY 
                            CASE WHEN viajes.fecha < CURDATE() THEN viajes.fecha ELSE CURDATE() END DESC, 
                            viajes.idviaje DESC 
                        LIMIT 
                            :limit OFFSET :offset");

  $query->bindParam(':idusuario', $idusuario);
  $query->bindParam(':limit', $limit, PDO::PARAM_INT);
  $query->bindParam(':offset', $offset, PDO::PARAM_INT);
  $query->execute();

  return $query->fetchAll(PDO::FETCH_ASSOC);
}
