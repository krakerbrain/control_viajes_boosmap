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

// Verificar si el usuario ha colaborado (admins siempre cuentan como colaboradores)
$colaborador = false;
try {
    $objColaborador = new HaColaborado($con, $datosUsuario['idusuario']);
    $colaborador = $objColaborador->haColaborado() || ($datosUsuario['admin'] == 1);
} catch (PDOException $e) {
    error_log("Error al verificar la colaboración: " . $e->getMessage());
    $colaborador = false;
}