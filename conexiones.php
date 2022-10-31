<?php
include('config.php');
session_start();
if(isset($_SESSION['usuario'])){
    $usuario = $_SESSION['usuario'];
}

/**Se recibe un parámetro que ejecuta el switch según lo que se recibe */
$ingresar   = $_POST['ingresar'];

/**Se obtiene el id del usuario según el nombre que use al iniciar sesión */
$query = $con->prepare("SELECT idusuario FROM usuarios WHERE nombre = :usuario");
$query->bindParam(':usuario', $usuario);
$query->execute();
while ($datos = $query->fetch()){
    $idusuario = $datos[0];
};

switch ($ingresar) {
    case 'insertar':
        $destino    = $_POST['destino'];
        $dia      = date("Y-m-d", strtotime(str_replace('/', '-',$_POST['dia'])));
        $hora      = $_POST['hora'];
        $fecha = $dia." ".$hora;
       $sql = $con->prepare("INSERT INTO viajes(idusuario,destino,fecha,monto) VALUES (:idusuario,:destino,:fecha,(SELECT costoruta from rutas where ruta = :destino limit 1))");
       $sql->bindParam(':idusuario', $idusuario);
       $sql->bindParam(':destino', $destino);
       $sql->bindParam(':fecha', $fecha);
       $sql->execute();
        break;
    
    case 'obtener':
      $query = $con->prepare("SELECT * FROM viajes WHERE idusuario = :idusuario ORDER BY fecha DESC LIMIT 10");
      $query->bindParam(':idusuario', $idusuario);
      $query->execute();
      $datos = $query->fetchAll(PDO::FETCH_ASSOC);
      foreach($datos as $viaje){
        echo "<tr>
                <td nowrap>".$viaje['destino']."</td>".
                "<td nowrap>".date('d-m-Y', strtotime($viaje['fecha']))."</td>".
                "<td class='text-center'>".$viaje['monto']."</td>
                <td style='cursor:pointer' class='text-center' onclick='eliminaviaje(".$viaje['idviaje'].")' >
                    <i class='fa-solid fa-xmark text-danger'></i>
                </td>
                </tr>";
      };
      break;
    case 'cargarutas':
      $query = $con->prepare("SELECT * FROM rutas WHERE idusuario = :idusuario");
      $query->bindParam(':idusuario', $idusuario);
      $query->execute();
      $datos = $query->fetchAll(PDO::FETCH_ASSOC);
      foreach($datos as $ruta){
        echo '<button value="'.$ruta['ruta'].'" class="btn btn-danger mr-1" onclick="insertaRuta(this)">'.$ruta['ruta'].'</button>';
      };
      break;
        case 'conteo';
        $query = $con->prepare("SELECT COUNT(*) as conteo FROM viajes WHERE idusuario = :idusuario");
        $query->bindParam(':idusuario', $idusuario);
        $query->execute();
        $datos = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach($datos as $conteo){
            echo $conteo['conteo'];
        }
        break;
        case 'totalmes';
        $query = $con->prepare("SELECT SUM(monto) as total FROM viajes WHERE idusuario = :idusuario");
        $query->bindParam(':idusuario', $idusuario);
        $query->execute();
        $datos = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach($datos as $total){
            echo $total['total'];
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





?>