<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../seguridad/JWT/jwt.php';
include __DIR__ . "/../partials/header.php";

$datosUsuario = validarToken();
$idusuario = $datosUsuario['idusuario'];
$nombreUsuario = $datosUsuario['nombre'];
$indice = "actualiza_datos";

$sqlUsuario = $con->prepare("SELECT correo FROM usuarios WHERE idusuario = :idUsuario");
$sqlUsuario->bindParam(':idUsuario', $idusuario);
$sqlUsuario->execute();

$resultadoUsuario = $sqlUsuario->fetch(PDO::FETCH_ASSOC);
$correoUsuario = $resultadoUsuario['correo'];

?>

<body class="container px-0" style="max-width:850px">
    <div>
        <?php include __DIR__ . "/../partials/navbar.php"; ?>
    </div>
    <div class="text-right">
        <i class="text-danger  mr-4 mt-2 far fa-question-circle" style="font-size:1.5rem" data-toggle="popover" data-placement="bottom" data-content="Aquí podrás modificar el correo, el password o ambos. Es importante tener el correo actualizado para enviar notificaciones de algunos cambios críticos que se pueden presentar"></i>
    </div>

    <div class="row-cols-lg-2">
        <form action="" method="post" class="form-group mt-3 mx-auto">
            <div class="input-group">
                <div class="input-group-text bg-danger text-light">
                    <i class="fa-solid fa-user"></i>
                </div>
                <input type="hidden" name="idusuario" id="idusuario" class="form-control" value="<?= $idusuario ?>">
                <input type="text" name="usuario" id="usuario" class="form-control" value="<?= $nombreUsuario ?>" disabled>
            </div>
            <div class=" input-group mt-2">
                <div class="input-group-text bg-danger text-light">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <input type="mail" name="correo" id="correo" class="form-control" placeholder="Ingrese su correo" value="<?= $correoUsuario ?>">
            </div>
            <div class="input-group mt-2">
                <div class="input-group-text bg-danger text-light">
                    <i class="fa-solid fa-key"></i>
                </div>
                <input type="password" name="password" id="password" class="form-control" placeholder="Ingrese su nueva clave">
                <div class="input-group-text bg-light">
                    <a href="#" class="pe-auto text-danger">
                        <i class="fa-solid fa-eye" onclick="verpass(1)"></i>
                    </a>
                </div>
            </div>
            <div class="input-group mt-2">
                <div class="input-group-text bg-danger text-light">
                    <i class="fa-solid fa-key"></i>
                </div>
                <input type="password" name="password2" id="password2" class="form-control" placeholder="Repita la clave" autocomplete="off">
                <div class="input-group-text bg-light">
                    <a href="#" class="pe-auto text-danger">
                        <i class="fa-solid fa-eye" onclick="verpass(2)"></i>
                    </a>
                </div>
            </div>
            <div class="form-group mt-3">
                <input type="button" value="Actualizar" class="btn btn-danger w-100" onclick="actualizaDatos(event)">
            </div>
            <div class="mt-3 text-center" id="respuesta">
            </div>
            <div>
                <a href="<?= $_ENV['URL_INICIO'] ?>">Ir al inicio</a>
            </div>
        </form>
    </div>
</body>
<?php include __DIR__ . "/../partials/boostrap_script.php" ?>

</html>
<script>
    function verpass(param) {
        var pass1 = document.getElementById('password');
        var pass2 = document.getElementById('password2');
        if (param == 1) {
            pass1.type = pass1.type == "password" ? "text" : "password"
        } else {
            pass2.type = pass2.type == "password" ? "text" : "password"
        }
    }

    function actualizaDatos(event) {
        event.preventDefault();
        const idusuario = document.getElementById("idusuario").value;
        const correo = document.getElementById("correo").value;
        const password = document.getElementById("password").value;
        const password2 = document.getElementById("password2").value

        $.post("conexiones_actualiza.php", {
                ingresar: 'actualizar',
                idusuario: idusuario,
                correo: correo,
                password: password,
                password2: password2
            })
            .done(function(resp) {
                console.log(resp)
                try {
                    // Parsear el objeto JSON recibido
                    let data = JSON.parse(resp);

                    // Obtener los valores individuales del objeto
                    let respuesta = "";
                    let correo_actualizado = data.correo;
                    let clave_actualizada = data.clave;
                    let correoActual = data.correo_actual;

                    // Mostrar la respuesta en el elemento correspondiente
                    if (correo_actualizado == 'true' && clave_actualizada == 'false') {
                        respuesta = 'El correo se ha actualizado correctamente'
                    } else if (clave_actualizada == 'true' && correo_actualizado == 'false') {
                        respuesta =
                            'El password ha sido actualizado correctamente. <br> Se recomienda <a href="<?= $_ENV['URL_SESSION'] ?>?logout=true ">CERRAR SESIÓN</a> y acceder con la nueva clave'
                    } else if (correo_actualizado == 'true' && clave_actualizada == 'true') {
                        respuesta =
                            `${data.respuesta} <br> Se recomienda <a href="<?= $_ENV['URL_SESSION'] ?>?logout=true ">CERRAR SESIÓN</a> y acceder con la nueva clave`
                    } else {
                        respuesta = data.respuesta;
                    }
                    document.getElementById("respuesta").innerHTML = `<p class="text-danger">${respuesta}</p>`;

                    // Mostrar el correo actual en el campo correspondiente
                    let correoMostrar = correo_actualizado == "true" ? correo : correoActual;
                    document.getElementById("correo").value = correoMostrar;

                } catch (error) {
                    // Mostrar la respuesta en el elemento correspondiente
                    document.getElementById("respuesta").innerHTML = `<p class="text-danger">${resp}</p>`;
                }

            })
            .fail(function() {
                alert("Ha ocurrido un error en la actualización de datos.");
            });
    }
    $(function() {
        $('[data-toggle="popover"]').popover()
    })
</script>