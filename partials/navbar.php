<?php

$rutainicio = isset($indice) && $indice != "inicio" ? $_ENV['URL_INICIO'] : "#";  
?>
<nav class="navbar navbar-dark bg-danger lighten-4">
  <a class="navbar-brand" href="<?= $rutainicio ?>">Hola, <?= ucfirst($_SESSION['usuario']) ?></a>
  <div>
    <button class="navbar-toggler toggler-example" type="button" data-toggle="collapse" data-target="#navbarSupportedContent1"
    aria-controls="navbarSupportedContent1" aria-expanded="false" aria-label="Toggle navigation">
      <span class="dark-blue-text">
        <i class="fas fa-bars fa-1x"></i>
      </span>
    </button>
    <!-- <a href="#" data-toggle="modal" data-target="#whatsappModal" title="Dudas o cunsultas">
      <i class="fa-regular fa-circle-question text-light p-1" style="font-size:20px"></i>
    </a> -->
    <!-- Modal de WhatsApp
    <div class="modal fade" id="whatsappModal" tabindex="-1" role="dialog" aria-labelledby="whatsappModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header bg-danger text-light">
            <h5 class="modal-title" id="whatsappModalLabel">Contacto por WhatsApp</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true" class="text-light">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Si tienes dudas o consultas, puedes contactarme por WhatsApp haciendo clic en el siguiente enlace:</p>
            <a href="#" onclick="openWhatsApp()" >Enviar mensaje por WhatsApp</a>
            
          </div>
        </div>
      </div>
    </div> -->
  </div>
  <div class="collapse navbar-collapse" id="navbarSupportedContent1">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" style="font-size:14px" href="<?= $rutainicio ?>">Home <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="navbar-brand" style="font-size:14px"href="<?= $_ENV['URL_CONFIG'] ?>">Configuraciones</a>
      </li>
      <li class="nav-item">
        <a class="navbar-brand" style="font-size:14px"href="<?= $_ENV['URL_ESTADISTICAS'] ?>">Estadísticas</a>
      </li>
      <li class="nav-item">
        <a class="navbar-brand" style="font-size:14px"href="<?= $_ENV['URL_DOWNLOAD'] ?>">Descarga la App</a>
      </li>
      <li class="nav-item pt-2">
        <a class="border navbar-brand px-1" style="font-size:13px" href="#" onclick="confirmarCerrarSesion()">Cerrar Sesión</a>
      </li>
    </ul>
    <!-- Modal de confirmación -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header bg-danger text-light">
            <h5 class="modal-title" id="confirmModalLabel">Confirmar Cierre de Sesión</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true" class="text-light">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>¿Estás seguro de que deseas cerrar la sesión?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <a href="<?= $_ENV['URL_SESSION'] ?>" class="btn btn-danger">Cerrar Sesión</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Script para mostrar el modal de confirmación -->
  <script>
    function confirmarCerrarSesion() {
      $('#confirmModal').modal('show');
    }
    var phoneNumber = "+56975325574"; // Número de teléfono al que se enviará el mensaje

    // Función para abrir WhatsApp
    function openWhatsApp() {
      window.location.href = "whatsapp://send?phone=" + encodeURIComponent(phoneNumber);
    }
  </script>
</nav>