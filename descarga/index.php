<?php
require dirname(__DIR__) . "/seguridad/auth.php";
$indice = "descarga";
include dirname(__DIR__) . "/partials/header.php";
?>

<div class="container mt-4">
    <div class="bg-danger text-light p-4 rounded">
        <div class="text-center">
            <h5>Descarga la aplicación para Android</h5>

            <!-- Mensaje para usuarios iOS -->
            <div class="alert alert-info d-inline-block text-start mb-3">
                <i class="bi bi-apple"></i> <strong>¿Usas iPhone?</strong> Esta app solo está disponible para
                Android.<br>
                Puedes usar la <a href="https://boosterapp.site" class="alert-link">versión web</a> en tu dispositivo
                iOS.
            </div>

            <a class="btn btn-outline-light mb-3" href="<?= $baseUrl . "descarga/boosterapp-r.apk" ?>" download>
                Descargar boosterapp-r.apk
            </a>

            <div class="alert alert-warning text-dark mx-auto" style="max-width: 600px;">
                <h6 class="fw-bold">📌 Instrucciones de instalación:</h6>
                <ol class="text-start">
                    <!-- desistala la aplicacion anterior -->
                    <li>Si tienes una versión anterior de la app instalada, desinstálala primero</li>
                    <li>Descarga el archivo <strong>boosterapp-r.apk</strong></li>
                    <li>Al intentar instalar, Android puede mostrar: <br>
                        <em>"Bloqueado por seguridad. No se permiten instalaciones de fuentes desconocidas"</em>
                    </li>
                    <li>Para solucionarlo:
                        <ul>
                            <li>Ve a <strong>Ajustes → Seguridad</strong></li>
                            <li>Activa <strong>"Instalar aplicaciones desconocidas"</strong></li>
                            <li>Selecciona tu navegador o gestor de archivos y activa el permiso</li>
                        </ul>
                    </li>
                    <li>Vuelve a intentar la instalación</li>
                </ol>

                <div class="mt-3 p-2 bg-light rounded">
                    <p class="mb-1 fw-bold">⚠️ ¿No puedes instalarla?</p>
                    <p class="mb-0">Puedes seguir usando la versión web en:<br>
                        <a href="https://boosterapp.site" class="text-primary">boosterapp.site</a> (ingresa con tu
                        usuario y contraseña)
                    </p>
                </div>
            </div>

            <p class="mt-2" style="font-size:0.5rem">Versión 4.3 - 11-07-2023</p>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../partials/boostrap_script.php" ?>
</body>

</html>