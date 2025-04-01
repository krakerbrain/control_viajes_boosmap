<?php
require dirname(__DIR__) . '/config.php';
require dirname(__DIR__) . '/seguridad/JWT/jwt.php';
require dirname(__DIR__) . '/config/HaColaborado.php';


$datosUsuario = validarToken();
$baseUrl = ConfigUrl::get();

if (!$datosUsuario) {
    // Redirige a la página de login usando la URL base
    header("Location: " . $baseUrl . "login/index.php");
    exit;
}

$indice = "colaboracion";
include dirname(__DIR__) . "/partials/header.php";
?>
<style>
h1 {
    font-size: 1.8em;
}

.lead {
    font-size: 0.9em;
}
</style>

<body class="bg-light">

    <div class="container py-5">

        <div class="text-center mb-3">
            <h1 class="mb-3">🙏 ¡Gracias por tu apoyo!</h1>
            <p class="lead">
                Gracias a <strong>los compañeros que han colaborado</strong>, el proyecto podrá continuar,
                aunque con algunas limitaciones. A partir del <strong>10 de abril de 2025</strong>, ciertas
                funcionalidades estarán disponibles solo para usuarios que hayan realizado un aporte.
            </p>

            <p class="lead">
                <strong>No es mi intención lucrarme</strong> con esta herramienta que nació para ayudarnos
                a todos. Los aportes recaudados se destinarán exclusivamente al mantenimiento básico del sitio.
            </p>

            <p class="lead">
                Si esta aplicación te es útil y puedes colaborar, te lo agradeceré enormemente.
                Cada aporte, por pequeño que sea, ayuda a mantener el proyecto vivo.
            </p>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h4 class="card-title mb-3">💳 Datos para colaborar</h4>
                <p>
                    Si deseas apoyar el proyecto, puedes realizar una transferencia a la siguiente cuenta:
                </p>
                <div class="bg-light p-3 rounded mb-3">
                    <p class="mb-1"><strong>Banco:</strong> Banco Estado</p>
                    <p class="mb-1"><strong>Titular:</strong> Mario Montenegro Videla</p>
                    <p class="mb-1"><strong>Cuenta:</strong> 10770017</p>
                    <p class="mb-1"><strong>Tipo:</strong> Cuenta Rut</p>
                    <p class="mb-1"><strong>RUT:</strong> 10770017-K</p>
                    <p class="mb-1"><strong>Email:</strong> marioplantabaja@gmail.com</p>
                </div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <h4 class="card-title mb-3">📩 Confirma tu colaboración</h4>
                <p>
                    Por favor, completa el siguiente formulario para verificar tu aporte y continuar usando la
                    aplicación.
                </p>
                <form>
                    <input type="hidden" name="user_id" value="<?= $datosUsuario['idusuario'] ?>">
                    <div class="mb-3">
                        <label for="monto" class="form-label">Yo colaboré con:</label>
                        <input type="text" class="form-control" id="monto" name="monto" placeholder="Ejemplo: 5.000">
                    </div>
                    <button type="submit" class="btn btn-success" onclick="insertaColaboracion(event)">Enviar
                        confirmación</button>
                </form>
            </div>
        </div>

        <div class="text-center">
            <a href="<?= $baseUrl . "index.php" ?>" class="btn btn-danger mt-3">Ir a la app</a>
        </div>
    </div>
    <!-- modal agradecimiento -->
    <div class="modal fade" id="modalAgradecimiento" tabindex="-1" role="dialog"
        aria-labelledby="modalAgradecimientoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-light">
                    <h5 class="modal-title" id="modalAgradecimientoLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true" class="text-light">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>
    <!-- fin modal agradecimiento -->
</body>
<script>
function insertaColaboracion() {
    event.preventDefault();

    let monto = document.getElementById("monto").value;
    if (monto == 0 || monto == "" || isNaN(monto)) {
        modificaModal("Error", "El monto ingresado es incorrecto");
        // agregar footer boton cancelar
        return;
    }

    $.post("conexion_colab.php", {
        ingresar: "colaboracion_insert",
        monto: $("#monto").val(),
        idusuario: document.querySelector("input[name='user_id']").value
    }).done(function(data, error) {
        if (data == "true") {
            modificaModal("Gracias por tu aporte 👍",
                "Pronto quitaré los mensajes y podrás continuar usando la app con normalidad", false);
        } else {
            modificaModal("Gracias 👍", "Tu aporte ya ha sido registrado, pronto quitaré los mensajes", false);
        }
    }).fail(function() {
        alert("error");
    });
}

function modificaModal(title, body, footer = true) {
    document.getElementById("modalAgradecimientoLabel").innerHTML = title;
    document.querySelector("#modalAgradecimiento .modal-body").innerHTML = body;
    if (footer) {
        document.querySelector("#modalAgradecimiento .modal-footer").innerHTML =
            `<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>`;
    } else {
        document.querySelector("#modalAgradecimiento .modal-footer").innerHTML =
            `<a href="<?= $baseUrl . "index.php" ?>" class="btn btn-danger mt-3">Ir a la app</a>`;
    }
    $('#modalAgradecimiento').modal('show');
}
</script>
<?php include dirname(__DIR__) . "/partials/boostrap_script.php" ?>