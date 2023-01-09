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

$url = "comunas-regiones.json";
$json = file_get_contents($url);
$datos = json_decode($json, true)["regiones"];
$totalregiones = count($datos);

switch ($ingresar) {
    case 'cargaregiones':
        foreach ($datos as $key => $value) {
            echo "<option value=".$value["clave"].">".$value["region"]."</option>";
        }
    break;
    case 'cargacomunas':
        $region = $_POST["region"];
        $comunas = $datos[$region]["comunas"];
        foreach ($datos as $key => $value) {
            if($value["clave"] == $region){
                sort($value["comunas"]);
                for ($i=0; $i < count($value["comunas"]); $i++) { 
                    # code...
                    echo "<option value=".$value["comunas"][$i].">".$value["comunas"][$i]."</option>";
                }
            }
        }
    break;
    case 'agregaviaje':
        $comuna = $_POST['comuna'];
        $costoruta = $_POST['costoruta'];
        var_dump($idusuario);
       $sql = $con->prepare("INSERT INTO rutas(idusuario,ruta,costoruta) VALUES (:idusuario,:ruta,:costoruta)");
       $sql->bindParam(':idusuario', $idusuario);
       $sql->bindParam(':ruta', $comuna);
       $sql->bindParam(':costoruta', $costoruta);
       $sql->execute();
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
    case 'actualizaPrecios';
        $nuevosprecios = json_decode($_POST['nuevosPrecios']);
        $actualizaMes = $_POST['actualizaMes'];
        $actualizaActual = $_POST['actualizaActual'];
        var_dump($actualizaActual, $actualizaMes);
        
        for ($i=0; $i < count($nuevosprecios); $i++) { 
            if($actualizaMes == "true"){
                $query = $con->prepare("UPDATE viajes SET monto = :precio WHERE destino = (SELECT ruta from rutas WHERE idruta = :idruta) and date_format(fecha,'%m') = month(now())");
                $query->bindParam(':precio', $nuevosprecios[$i]->precio);
                $query->bindParam(':idruta', $nuevosprecios[$i]->id);
                $query->execute();
            }
            if($actualizaActual == "true"){
                $query = $con->prepare("UPDATE rutas SET costoruta = :precio WHERE idruta = :idruta");
                $query->bindParam(':precio', $nuevosprecios[$i]->precio);
                $query->bindParam(':idruta', $nuevosprecios[$i]->id);
                $query->execute();
            }
        }
    default:
        # code...
    break;
}