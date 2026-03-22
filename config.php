<?php
require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/ConfigUrl.php';
// Localhost: .env en la raíz del proyecto (__DIR__)
// Producción: código en public_html → subir un nivel y entrar a 'private'
$envPath = file_exists(__DIR__ . '/.env') ? __DIR__ : dirname(__DIR__) . '/private';

$dotenv = Dotenv\Dotenv::createImmutable($envPath);
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
