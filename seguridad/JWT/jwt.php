<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function validarToken()
{
    $baseUrl = ConfigUrl::get();

    if (isset($_COOKIE['jwt'])) {
        $key = $_ENV['JWTKEY'];
        try {
            $decoded = JWT::decode($_COOKIE['jwt'], new Key($key, 'HS256'));

            // Borrar la cookie si el token está expirado (opcional)
            if (isset($decoded->exp) && $decoded->exp < time()) {
                setcookie('jwt', '', time() - 3600, '/');
                return null;
            }

            return [
                'idusuario' => $decoded->id,
                'nombre' => $decoded->usuario,
                'admin' => $decoded->admin,
                'otrasapps' => $decoded->otrasapps
            ];
        } catch (Firebase\JWT\SignatureInvalidException $e) {
            // Error específico por cambio de JWTKEY
            setcookie('jwt', '', time() - 3600, '/'); // Borra la cookie inválida
            return null;
        } catch (Exception $e) {
            // Otros errores (token malformado, expirado, etc.)
            return null;
        }
    }
    return null;
}

function generarTokenYConfigurarCookie($data, $usuario)
{
    // Clave secreta para firmar el token (reemplázala con tu propia clave secreta)
    $key = $_ENV['JWTKEY'];

    // Construir el payload del token
    $payload = array(
        "id" => $data['idusuario'],
        "usuario" => $usuario,
        "admin" => $data['admin'] == 1 ? true : false,
        "otrasapps" => $data['otrasapps'] == 1 ? true : false
    );

    // Codificar el token JWT
    $jwt = JWT::encode($payload, $key, 'HS256');

    // Obtener la fecha y hora de medianoche de hoy
    $midnightToday = strtotime('today midnight');

    // Obtener la fecha y hora de medianoche de una semana después
    $midnightNextWeek = $midnightToday + (7 * 24 * 60 * 60);

    // Establecer el token con una duración hasta medianoche de una semana después
    setcookie("jwt", $jwt, $midnightNextWeek, "/", "", false, true);
}

//uso en archivos
// require __DIR__ . '/../config.php';
// require __DIR__ . '/../seguridad/JWT/jwt.php';
// include __DIR__ . '/../partials/header.php';

// $datosUsuario = validarToken();
// $indice = "aplicaciones";

// if (!$datosUsuario) {
//     header($_ENV['URL_LOCAL']);
//     exit;
// }
// return [
//     'idusuario' => $idusuario,
//     'nombre' => $nombre,
//     'admin' => $admin,
//     'otrasapps' => $otrasapps
// ];