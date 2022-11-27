<?php

$rutainicio = isset($indice) && $indice != "inicio" ? $_ENV['URL_INICIO'] : "#";  
?>
<nav class="navbar navbar-dark bg-danger lighten-4">
  <a class="navbar-brand" href="<?= $rutainicio ?>">Hola, <?= ucfirst($_SESSION['usuario']) ?></a>
    <button class="navbar-toggler toggler-example" type="button" data-toggle="collapse" data-target="#navbarSupportedContent1"
      aria-controls="navbarSupportedContent1" aria-expanded="false" aria-label="Toggle navigation">
        <span class="dark-blue-text">
            <i class="fas fa-bars fa-1x"></i>
        </span>
    </button>
  <div class="collapse navbar-collapse" id="navbarSupportedContent1">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" style="font-size:12px" href="<?= $rutainicio ?>">Home <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="navbar-brand" style="font-size:12px"href="<?= $_ENV['URL_CONFIG'] ?>">Configurar Rutas</a>
      </li>
      <li class="nav-item">
        <a class="navbar-brand" style="font-size:12px"href="<?= $_ENV['URL_ESTADISTICAS'] ?>">Estadísticas</a>
      </li>
      <li class="nav-item">
        <a class="navbar-brand" style="font-size:12px"href="<?= $_ENV['URL_SESSION'] ?>">Cerrar Sesión</a>
      </li>
    </ul>
  </div>
</nav>