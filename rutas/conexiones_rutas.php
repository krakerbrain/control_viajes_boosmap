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
    /*case 'cargaregiones':
        $token = "rvoQ1IRbmKC3d6jyrTRTmK3BZjiXMhbYPc9BZcre";
        $url = "https://api.datos.observatoriologistico.cl/api/v2/datastreams/COMUN-POR-REGIO/data.pjson/?auth_key=".$token;
        $json = file_get_contents($url);
        $datos = json_decode($json, true)['result'];
        $totalregiones = count($datos)-1;
   
       for ($i=0; $i < $totalregiones; $i++) {
        $regiones =  explode(" ",$datos[$i]['NOMBRE_REGION']);
        $nombre_region = end($regiones);
        echo "<option value=".$i.">".$nombre_region."</option>";
       }

        break;*/
    case 'cargaregiones':
        foreach ($datos as $key => $value) {
            echo "<option value=".$value["clave"].">".$value["region"]."</option>";
        }
    break;
    case 'cargacomunas':
        $region= $_POST["region"];
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
        $datos = $query->fetchAll(PDO::FETCH_ASSOC);
        foreach($datos as $ruta){
          echo "<tr>
                  <td nowrap>".$ruta['ruta']."</td>".
                  "<td class='text-center'>".$ruta['costoruta']."</td>
                  <td style='cursor:pointer' class='text-center' onclick='eliminaRuta(\"".$ruta['ruta']."\",".$ruta['idruta'].")' >
                      <i class='fa-solid fa-xmark text-danger'></i>
                  </td>
                  </tr>";
        };
    break;
    case 'eliminaRuta';
        $idruta = $_POST['idruta'];
        $query = $con->prepare("DELETE FROM rutas WHERE idruta = :idruta AND idusuario = :idusuario");
        $query->bindParam(':idruta', $idruta);
        $query->bindParam(':idusuario', $idusuario);
        $query->execute();
    break;
    default:
        # code...
    break;
}