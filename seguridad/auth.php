<?php
require dirname(__DIR__) . '/config.php';
require dirname(__DIR__) . '/seguridad/JWT/jwt.php';
require dirname(__DIR__) . '/config/HaColaborado.php'; // Incluir la clase mejorada

$datosUsuario = validarToken();
$baseUrl = ConfigUrl::get();

if (!$datosUsuario) {
    $errorType = isset($_COOKIE['jwt']) ? 'invalid_token' : 'no_token';
    header("Location: " . $baseUrl . "login/index.php?error=" . $errorType);
    exit;
}

// Verificar si el usuario ha colaborado
try {
    $haColaborado = new HaColaborado($con, $datosUsuario['idusuario']);
    // si no ha colaborado y no es admin
    // if (!$haColaborado->haColaborado() && !$datosUsuario['admin']) {
    //     // Redirige a la página de colaboración si no ha colaborado
    //     header("Location: " . $baseUrl . "colab/colab.php");
    //     exit;
    // }
} catch (PDOException $e) {
    // Manejar el error (por ejemplo, mostrar un mensaje al usuario o registrar el error)
    error_log("Error al verificar la colaboración: " . $e->getMessage());
    header("Location: " . $baseUrl . "error.php");
    exit;
}
// en descarga agregar instrucciones de descarga
// comporar el nuevo dominio y avisar que va a cambiar el dominio y que tienen que descargar de nuevo la app