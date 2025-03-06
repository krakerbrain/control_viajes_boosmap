<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../seguridad/JWT/jwt.php';


$datosUsuario = validarToken();
$ingresar = $_REQUEST['ingresar'];

if (!$datosUsuario) {
    header("Location: " . $baseUrl . "login/index.php");
    exit;
}

$idusuario = $datosUsuario['idusuario'];



switch ($ingresar) {
    case 'select_mes':
        $query = $con->prepare("SELECT DISTINCT MONTH(fecha) AS mes, YEAR(fecha) AS anio FROM viajes WHERE idusuario = :idusuario ORDER BY fecha ASC");
        $query->bindParam(':idusuario', $idusuario);
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

        // Cargar el contenido del archivo JSON
        // $jsonFilePath = '../islr.json';
        // $jsonContent = file_get_contents($jsonFilePath);
        // $islrData = json_decode($jsonContent, true);

        // // Obtener el factor correspondiente al año
        // foreach ($islrData as $islr) {
        //     //Como el pago de diciembre es en enero, hay que sumar 1 al anio para que coincida con el impuesto aumentado de enero
        //     $anioFactor = $mes == 12 ? $anio + 1 : $anio;
        //     if ($islr['anio'] == $anioFactor) {
        //         $factor = $islr['factor'];
        //         break;
        //     }
        // }
        $query = $con->prepare("SELECT 
                                    COUNT(*) AS totalviajes,
                                    COALESCE(SUM(viajes.monto),0) AS montoLiquido,
                                    COUNT(DISTINCT peajes.idviaje) AS conteoPeajes,
                                    COUNT(DISTINCT extras.idviaje) AS conteoExtras,
                                    COALESCE(SUM(peajes.monto),0) AS totalPeajes,
                                    COALESCE(SUM(extras.monto),0) AS totalExtras
                                FROM 
                                    viajes
                                LEFT JOIN peajes ON peajes.idviaje = viajes.idviaje
                                LEFT JOIN extras ON extras.idviaje = viajes.idviaje
                                WHERE 
                                    viajes.idusuario = :idusuario 
                                    AND MONTH(viajes.fecha) = :mes 
                                    AND YEAR(viajes.fecha) = :anio");
        $query->bindParam(':idusuario', $idusuario);
        $query->bindParam(':mes', $mes);
        $query->bindParam(':anio', $anio);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $json_result = json_encode($result);
        echo $json_result;
        break;
    case 'viajesxruta':
        $mes = $_POST['mes'];
        $annio = $_POST['annio'];

        $tipoorden = isset($_POST['tipoorden']) ? $_POST['tipoorden'] : "";
        $order_by = "";
        if (!empty($tipoorden)) {
            $order_by = "ORDER BY " . $tipoorden;
        }
        $query = $con->prepare("SELECT destino, count(destino) as conteo 
                                FROM viajes 
                                WHERE idusuario = :idusuario 
                                AND extract(month FROM fecha) = :mes 
                                AND EXTRACT(YEAR FROM fecha) = :annio 
                                GROUP BY destino " . $order_by);
        $query->bindParam(':idusuario', $idusuario);
        $query->bindParam(':mes', $mes);
        $query->bindParam(':annio', $annio);
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
        $annio = $mes > $mesActual ? $annioActual - 1 : $annioActual;
        $query = $con->prepare("SELECT extract(month FROM fecha) as mes, SUM(monto) as ingreso, count(destino) as conteo FROM viajes WHERE idusuario = :idusuario AND extract(year FROM fecha) = :anio AND extract(month FROM fecha) = :mes GROUP BY mes");
        $query->bindParam(':idusuario', $idusuario);
        $query->bindParam(':anio', $annio);
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
