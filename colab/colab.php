<?php
require dirname(__DIR__) . '/config.php';
require dirname(__DIR__) . '/seguridad/JWT/jwt.php';


$datosUsuario = validarToken();
$indice = "colaboracion";
$baseUrl = ConfigUrl::get();

if (!$datosUsuario) {
    // Redirige a la página de login usando la URL base
    header("Location: " . $baseUrl . "login/index.php");
    exit;
}

include dirname(__DIR__) . "/partials/header.php";
?>

<body class="bg-light">

    <div class="container py-5">

        <div class="text-center mb-3">
            <h1 class="mb-3">🙏 ¡Gracias por querer colaborar!</h1>
            <p class="lead">
                Hace ya <strong>tres años</strong> inicié este proyecto como una herramienta personal para llevar el
                control de mis viajes y ganancias,
                ya que la aplicación que usamos los conductores no nos ofrecía el detalle completo de cada trayecto.
            </p>
            <p class="lead">
                Con el tiempo, esta plataforma se hizo conocida y comenzó a ser utilizada por más colegas que, al igual
                que yo, necesitaban tener <strong>orden y resumen diario, semanal y mensual</strong> de su trabajo.
            </p>
            <p class="lead">
                Hasta hoy, <strong>he asumido personalmente todos los costos anuales</strong> para mantenerla online,
                pero los gastos han aumentado considerablemente.
                Si esta herramienta te ha sido útil y quieres que siga activa, <strong>te pido una colaboración</strong>
                para ayudarme a cubrir los costos de hosting y dominio.
            </p>
            <p class="lead">
                La renovación vence el <strong>1 de abril de 2025</strong>, y si no logro reunir el monto necesario,
                <strong>lamentablemente el proyecto dejará de estar disponible a partir de esa fecha</strong>.
            </p>
            <p class="lead">
                Desde ya, <strong>¡muchas gracias por tu apoyo!</strong>
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
                    Por favor, completa el siguiente formulario para verificar tu aporte y así quitar el mensaje de
                    aviso de tu cuenta.
                </p>
                <form>
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
            monto: $("#monto").val()
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