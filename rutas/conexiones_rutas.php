<?php
require dirname(__DIR__) . '/seguridad/auth.php';

$ingresar = $_REQUEST['ingresar'];
$idusuario = $datosUsuario['idusuario'];

switch ($ingresar) {
    case 'agregaruta':
        try {
            $comuna = $_POST['comuna'];
            $costoruta = $_POST['costoruta'];

            // Validación en el backend
            if (empty($comuna) || empty($costoruta)) {
                throw new Exception("La comuna o el costo de ruta no pueden estar vacíos.");
            }

            // Inserción de la nueva ruta en la base de datos
            $sql = $con->prepare("INSERT INTO rutas(idusuario,ruta,costoruta) VALUES (:idusuario,:ruta,:costoruta)");
            $sql->bindParam(':idusuario', $idusuario);
            $sql->bindParam(':ruta', $comuna);
            $sql->bindParam(':costoruta', $costoruta);
            $sql->execute();

            // Respuesta al frontend con un mensaje de éxito
            echo "Ruta agregada correctamente.";
        } catch (Exception $e) {
            // Respuesta al frontend con un mensaje de error
            echo "Error al agregar la ruta: " . $e->getMessage();
        }
        break;

    case 'obtenerRutas':
        $query = $con->prepare("SELECT * FROM rutas WHERE idusuario = :idusuario");
        $query->bindParam(':idusuario', $idusuario);
        $query->execute();
        $datos = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
        echo $datos;

        break;
    case 'eliminaRuta';
        $idruta = $_POST['idruta'];
        $query = $con->prepare("DELETE FROM rutas WHERE idruta = :idruta AND idusuario = :idusuario");
        $query->bindParam(':idruta', $idruta);
        $query->bindParam(':idusuario', $idusuario);
        $query->execute();
        break;
    case 'actualizaPrecios':
        try {
            $nuevosprecios = json_decode($_POST['nuevosPrecios']);
            $actualizaMes = $_POST['actualizaMes'] === "true";

            // 1. Actualizar precios en 'rutas'
            foreach ($nuevosprecios as $precioData) {
                $query = $con->prepare("UPDATE rutas SET costoruta = :precio WHERE idruta = :idruta");
                $query->bindParam(':precio', $precioData->precio);
                $query->bindParam(':idruta', $precioData->id);
                $query->execute();
            }

            // 2. Actualizar viajes del mes (si está marcado)
            if ($actualizaMes) {
                $query = $con->prepare("
                        UPDATE viajes v
                        JOIN rutas r ON v.destino = r.ruta
                        SET v.monto = r.costoruta
                        WHERE MONTH(v.fecha) = MONTH(CURRENT_DATE())
                        AND YEAR(v.fecha) = YEAR(CURRENT_DATE())
                        AND v.idusuario = :idusuario
                        AND r.idruta IN (" . implode(',', array_map(fn($p) => $p->id, $nuevosprecios)) . ")
                    ");
                $query->bindParam(':idusuario', $idusuario);
                $query->execute();
            }

            // Respuesta clara y simple
            echo $actualizaMes
                ? "¡Se actualizaron los montos de las rutas y los viajes de este mes!"
                : "¡Se actualizaron los montos de las rutas!";
        } catch (Exception $e) {
            echo "Error: No se pudieron guardar los cambios. Por favor, inténtalo de nuevo.";
        }
        break;
    case 'verificarComunas':
        $comuna = $_POST['comuna'];
        $query = $con->prepare("SELECT count(*) FROM rutas WHERE ruta = :comuna AND idusuario = :idusuario");
        $query->bindParam(':comuna', $comuna);
        $query->bindParam(':idusuario', $idusuario);
        $query->execute();
        $count = $query->fetchColumn();

        if ($count > 0) {
            echo "true";
        } else {
            echo "false";
        }
        break;
    default:
        # code...
        break;
}
