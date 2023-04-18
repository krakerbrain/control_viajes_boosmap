<?php
include __DIR__.'/../config.php';
session_start();

if(isset($_SESSION['usuario'])){
    $usuario = $_SESSION['usuario'];
}
$ingresar = $_REQUEST['ingresar'];
/**Se obtiene el id del usuario según el nombre que use al iniciar sesión */
$query = $con->prepare("SELECT idusuario FROM usuarios WHERE nombre = :usuario");
$query->bindParam(':usuario', $usuario);
$query->execute();
while ($datos = $query->fetch()){
    $idusuario = $datos[0];
};

switch ($ingresar) {
    case 'select_mes':
        $query = $con->prepare("SELECT DISTINCT MONTH(fecha) AS mes, YEAR(fecha) AS anio FROM viajes ORDER BY fecha ASC");
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $json_result = json_encode($result);
        echo $json_result;
        break;
    case 'pedidostotales':
        $mes = $_POST['mes'];
        $mesAnio = explode('-', $mes); // Divide el valor del mes y año en dos partes separadas
        $mes = $mesAnio[0]; // Obtiene el mes de la primera parte del valor
        $anio = $mesAnio[1]; // Obtiene el año de la segunda parte del valor
        
        $query = $con->prepare("SELECT count(*) AS totalviajes, sum(monto) as ingresos  FROM viajes WHERE idusuario = :idusuario AND MONTH(fecha) = :mes AND YEAR(fecha) = :anio");
        $query->bindParam(':idusuario', $idusuario);
        $query->bindParam(':mes', $mes);
        $query->bindParam(':anio', $anio);
        $query->execute();
        $datos = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach($datos as $totalviajes){
            $montobruto = ($totalviajes['ingresos'] / 0.87);
          echo "<tr>
                  <td nowrap>Viajes completados: ".$totalviajes['totalviajes']."</td>
                  </tr>
                  <tr>
                  <td nowrap>Ingresos Líquidos totales: ".$totalviajes['ingresos']."</td>
                  </tr>
                  <tr>
                  <td nowrap>Ingresos Brutos totales: ".round($montobruto)."</td>
                </tr>";
        };

        break;
        case 'viajesxruta':
            $mes = filter_input(INPUT_POST, 'mes', FILTER_SANITIZE_STRING);
            $tipoorden = isset($_POST['tipoorden']) ? $_POST['tipoorden'] : "";
            $order_by = "";
            if (!empty($tipoorden)) {
                $order_by = "ORDER BY " . $tipoorden;
            }
            $query = $con->prepare("SELECT destino, count(destino) as conteo FROM viajes WHERE idusuario = :idusuario AND extract(month FROM fecha) = :mes GROUP BY destino " . $order_by);
            $query->bindParam(':idusuario', $idusuario);
            $query->bindParam(':mes', $mes);
            $query->execute();
            $datos = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($datos as $conteoviajes){
                echo "<tr>
                          <td class='p1'>".$conteoviajes['destino']."</td>
                          <td class='p1'>".$conteoviajes['conteo']."</td>
                      </tr>";
            };
            break;
        
    default:
        # code...
        break;
}


?>