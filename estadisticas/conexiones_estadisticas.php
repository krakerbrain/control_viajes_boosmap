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
        foreach ($datos as $totalviajes) {
            $montobruto = ($totalviajes['ingresos'] / 0.87);
            echo "<tr>
                  <td nowrap>Viajes completados: " . $totalviajes['totalviajes'] . "</td>
                  </tr>
                  <tr>
                  <td nowrap>Ingresos Líquidos totales: $" . number_format($totalviajes['ingresos'], 0, ',', '.') . "</td>
                  </tr>
                  <tr>
                  <td nowrap>Ingresos Brutos totales: $" . number_format(round($montobruto), 0, ',', '.') . "</td>
                </tr>";
        };

        break;
    case 'viajesxruta':
        $mes = $mes = $_POST['mes'];
        $tipoorden = isset($_POST['tipoorden']) ? $_POST['tipoorden'] : "";
        $order_by = "";
        if (!empty($tipoorden)) {
            $order_by = "ORDER BY " . $tipoorden;
        }
        $query = $con->prepare("SELECT destino, count(destino) as conteo 
                                FROM viajes 
                                WHERE idusuario = :idusuario 
                                AND extract(month FROM fecha) = :mes 
                                AND EXTRACT(YEAR FROM FECHA) = EXTRACT(YEAR FROM NOW()) 
                                GROUP BY destino " . $order_by);
        $query->bindParam(':idusuario', $idusuario);
        $query->bindParam(':mes', $mes);
        $query->execute();
        $datos = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach ($datos as $conteoviajes) {
            echo "<tr>
                          <td class='p1'>" . $conteoviajes['destino'] . "</td>
                          <td class='p1'>" . $conteoviajes['conteo'] . "</td>
                      </tr>";
        };
        break;
    case 'mesesConDatos':
        $mesesConDatos = obtenerMesesEIngresos($con, $idusuario);
        echo json_encode($mesesConDatos);
        break;

    default:
        # code...
        break;
}

function obtenerMesesEIngresos($con, $idusuario)
{
    $mesActual = date('n');
    $annioActual = date('Y');

    $mesesYMonto = [];

    for ($i = 5; $i >= 0; $i--) {
        $mes = ($mesActual - $i + 12) % 12; // Asegura que el mes esté en el rango de 1 a 12
        $mes = $mes == 0 ? 12 : $mes;
        $query = $con->prepare("SELECT extract(month FROM fecha) as mes, SUM(monto) as ingreso, count(destino) as conteo FROM viajes WHERE idusuario = :idusuario AND extract(year FROM fecha) = :anio AND extract(month FROM fecha) = :mes GROUP BY mes");
        $query->bindParam(':idusuario', $idusuario);
        $query->bindParam(':anio', $annioActual);
        $query->bindParam(':mes', $mes);
        $query->execute();
        $resultado = $query->fetch(PDO::FETCH_ASSOC);

        $meses = [
            1 => "Enero",
            2 => "Febrero",
            3 => "Marzo",
            4 => "Abril",
            5 => "Mayo",
            6 => "Junio",
            7 => "Julio",
            8 => "Agosto",
            9 => "Septiembre",
            10 => "Octubre",
            11 => "Noviembre",
            12 => "Diciembre"
        ];

        if ($resultado !== false) {
            // Si se obtuvo un resultado válido, agregarlo al array
            $mesesYMonto[] = [
                'mes' => $meses[(int)$resultado['mes']],
                'viajes' => (int)$resultado['conteo'],
                'monto' => (int)$resultado['ingreso']
            ];
        } else {
            // Si no se obtuvo un resultado válido, agregar un valor predeterminado
            $mesesYMonto[] = [
                'mes' => $meses[$mes],
                'viajes' => 0,
                'monto' => 0 // O cualquier otro valor predeterminado
            ];
        }
    }

    return $mesesYMonto;
}
