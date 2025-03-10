<?php
require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/ConfigUrl.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$baseUrl = ConfigUrl::get();

$host = $_ENV['HOST'];
$bd = $_ENV['BD'];
$usuario = $_ENV['USUARIO'];
$contrasenia = $_ENV['PASS'];

try {
    $con = new PDO("mysql:host=$host;dbname=$bd", $usuario, $contrasenia);
    // echo "Conectado";
} catch (PDOException $ex) {
    echo $ex->getMessage();
}
