<?php
include('../config.php');

if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    // Eliminar la cookie (esto puede variar según cómo esté configurada la cookie)
    setcookie("jwt", "", time() - 1, "/");
}
header("Location: " . $baseUrl . "login/index.php");
exit;
