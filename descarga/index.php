<?php
require dirname(__DIR__) . "/seguridad/auth.php";
$indice = "descarga";
include dirname(__DIR__) . "/partials/header.php";

$apkUrl = $baseUrl . "descarga/app-release.apk";
?>

<style>
    .step-number {
        width: 35px;
        height: 35px;
        background-color: #dc3545;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        flex-shrink: 0;
    }
    .install-card {
        border-radius: 12px;
        border: 1px solid #f0f0f0;
        background-color: #fff;
    }
    .btn-download {
        background-color: #dc3545;
        color: white !important;
        font-weight: bold;
        padding: 15px 20px;
        border-radius: 8px;
        text-decoration: none !important;
        display: inline-block;
        width: 100%;
        max-width: 280px;
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.2);
        font-size: 1.1rem;
        transition: 0.3s;
    }
    .btn-download:hover {
        background: #a71d2a;
        transform: translateY(-2px);
    }
    .support-card {
        background-color: #f8f9fa;
        border-radius: 15px;
        border: 1px dashed #dee2e6;
    }
    .btn-whatsapp {
        background-color: #25D366;
        color: white !important;
        font-weight: bold;
        padding: 15px 20px;
        border-radius: 8px;
        text-decoration: none !important;
        display: inline-block;
        width: 100%;
        max-width: 280px;
        box-shadow: 0 4px 8px rgba(37, 211, 102, 0.2);
        font-size: 1rem;
        transition: 0.3s;
    }
    .btn-whatsapp:hover {
        background-color: #1eb954;
        transform: translateY(-2px);
    }
</style>

<div class="container mt-4 mb-5" style="max-width: 600px;">
    <div class="text-center mb-4">
        <h3 class="font-weight-bold">Instalar Booster App (Android)</h3>
        <p class="text-muted small">Sigue estos 4 pasos rápidos para tu celular.</p>
    </div>

    <!-- PASO 1 -->
    <div class="card shadow-sm border-0 mb-3 install-card">
        <div class="card-body d-flex">
            <div class="step-number mr-3">1</div>
            <div>
                <h6 class="font-weight-bold text-danger mb-1">Borrar app vieja</h6>
                <p class="mb-0 small text-muted">Mantenla presionada y elige <b>Desinstalar</b>. Si no la borras, la nueva no se instalará.</p>
            </div>
        </div>
    </div>

    <!-- PASO 2 -->
    <div class="card shadow-sm border-0 mb-3 install-card text-center">
        <div class="card-body py-4">
            <div class="d-flex align-items-center justify-content-center mb-2">
                <div class="step-number mr-2">2</div>
                <h6 class="font-weight-bold mb-0">Bajar aplicación</h6>
            </div>
            <div class="py-2">
                <a class="btn btn-download" href="<?= $apkUrl ?>" download>
                    <i class="fas fa-download mr-2"></i> DESCARGAR AQUÍ
                </a>
            </div>
            <p class="small text-muted mb-0">Si dice "archivo dañino", dale a <b>"Descargar de todos modos"</b>.</p>
        </div>
    </div>

    <!-- PASO 3 -->
    <div class="card shadow-sm border-0 mb-3 install-card">
        <div class="card-body d-flex">
            <div class="step-number mr-3">3</div>
            <div>
                <h6 class="font-weight-bold mb-1">Autorizar e Instalar</h6>
                <p class="mb-0 small text-muted">Abre el archivo. Si sale <b>"Bloqueado"</b>, ve a <b>Ajustes</b> y activa <b>"Permitir desde esta fuente"</b>.</p>
            </div>
        </div>
    </div>

    <!-- PASO 4 -->
    <div class="card shadow-sm border-0 mb-4 install-card">
        <div class="card-body d-flex">
            <div class="step-number mr-3">4</div>
            <div>
                <h6 class="font-weight-bold mb-1">Abrir e Instalar</h6>
                <p class="mb-0 small text-muted">Abre la app e inicia sesión con el usuario y clave de siempre.</p>
            </div>
        </div>
    </div>

    <!-- IPHONE -->
    <div class="text-center py-2 border rounded bg-light mb-5 px-3">
        <p class="mb-0 small"><i class="fab fa-apple mr-1"></i> <b>¿Tienes iPhone?</b> Entra directo en: <a href="https://boosterapp.de" class="text-danger font-weight-bold">boosterapp.de</a></p>
    </div>

    <!-- SOPORTE -->
    <div class="card border-0 shadow-sm support-card mb-5">
        <div class="card-body text-center p-4">
            <h6 class="font-weight-bold mb-1">¿Necesitas ayuda?</h6>
            <p class="small text-muted mb-3">Si tienes dudas, escríbenos por WhatsApp:</p>
            <a href="https://wa.me/56975325574" class="btn btn-whatsapp shadow-sm">
                <i class="fab fa-whatsapp mr-2"></i> HABLAR CON SOPORTE
            </a>
            <p class="mt-4 mb-0 text-muted" style="font-size: 0.6rem; opacity: 0.7;">Booster App Support &bull; © 2026</p>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../partials/boostrap_script.php" ?>
</body>
</html>