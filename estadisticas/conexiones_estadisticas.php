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
    case 'pedidostotales':
        $mes = $_POST['mes'];
        $query = $con->prepare("SELECT count(*) AS totalviajes, sum(monto) as ingresos  FROM viajes WHERE idusuario = :idusuario AND extract(month FROM fecha) = :mes");
        $query->bindParam(':idusuario', $idusuario);
        $query->bindParam(':mes', $mes);
        $query->execute();
        $datos = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach($datos as $totalviajes){
            $montobruto = ($totalviajes['ingresos'] * 13.975 /100) + $totalviajes['ingresos'];
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
            $mes = $_POST['mes'];
            $tipoorden = $_POST['tipoorden'];
            $query = $con->prepare("SELECT destino, count(destino) as conteo FROM viajes WHERE idusuario = :idusuario AND extract(month FROM fecha) = :mes group by destino order by ".$tipoorden."");
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