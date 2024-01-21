<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function validarToken()
{

    if (isset($_COOKIE['jwt'])) {
        $key = $_ENV['JWTKEY'];
        try {
            $decoded = JWT::decode($_COOKIE['jwt'], new Key($key, 'HS256'));
            if (is_object($decoded)) {
                // Acceder a la información del payload
                $idusuario = $decoded->id;
                $nombre = $decoded->usuario;
                $admin = $decoded->admin;
                $otrasapps = $decoded->otrasapps;

                return [
                    'idusuario' => $idusuario,
                    'nombre' => $nombre,
                    'admin' => $admin,
                    'otrasapps' => $otrasapps
                ];
            } else {
                // Manejar el caso en el que el token no decodificado correctamente
                header($_ENV['URL_LOCAL']);
                return null;
            }
        } catch (Exception $e) {
            echo "Error JWT: " . $e->getMessage();
            return null;
        }
    }

    return null;
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