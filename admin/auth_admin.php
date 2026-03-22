<?php
require dirname(__DIR__) . '/seguridad/auth.php';

// Solo si es administrador
if (!$datosUsuario['admin']) {
    header("Location: " . $baseUrl . "index.php");
    exit;
}
?>
