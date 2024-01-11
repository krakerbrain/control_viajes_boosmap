<?php
session_start();
$sesion = isset($_SESSION['usuario']);

require __DIR__ . '/../config.php';
include __DIR__ . "/../partials/header.php";
$indice = "aplicaciones";

// Verificar si el usuario está autenticado y es "admin2"
if (!$sesion || !$_SESSION['otrasapps']) {
    // Si el usuario no está autenticado o no tiene activo el modulo, redirigir o mostrar un mensaje de error.
    header($_ENV['URL_LOCAL']);
    exit();
}
?>

<body>
    <div class="container px-0" style="max-width:850px">
        <?php include __DIR__ . "/../partials/navbar.php"; ?>
        <div class="mx-3">
            <div class="row-cols-lg-2 m-2">
                <h6 class="mx-auto my-2">REGISTRO OTRAS APPS</h6>
                <form action="conexiones_app.php" id="registraApp" method="post" class="mx-auto">
                    <input type="checkbox" name="checkboxRegistroApp" id="checkboxRegistroApp"> Registrar Nueva
                    Aplicación
                    <div class="form-group d-flex">
                        <input class="form-control" type="text" id="nombreApp" placeholder="Nombre de App" disabled>
                        <input class="btn btn-danger mx-2" type="submit" value="Agregar" id="agregaApp" disabled>
                    </div>
                </form>

                <div class="mx-auto">

                    <input type="date" name="fecha" id="fecha" class="form-control mb-2">
                    <select class="form-control" name="appNombre" id="appNombre">
                        <option value="">Aplicaciones registradas</option>
                    </select>

                    <form id="registraGanancias" action="conexiones_app.php" method="post" class="mx-auto">
                        <div class="form-group d-flex mt-2">
                            <input class="form-control" type="number" id="inputRegistraGanancia" placeholder="Último registro App">
                            <input class="btn btn-danger mx-2" type="submit" value="Agregar" id="agregar">
                        </div>
                    </form>
                    <div id="campoVacioAlerta" class="alert alert-danger" role="alert" style="display: none;">
                        Los campos Aplicación y Monto son obligatorios
                    </div>
                </div>
                <div class="mx-auto">
                    <table class="table table-striped table-sm" style="font-size: 0.9em;">
                        <thead class="table-danger text-center">
                            <tr>
                                <td style="width: 20%;">App</td>
                                <td style="width: 30%;">Monto</td>
                                <td style="width: 40%;">Fecha</td>
                                <td style="width: 10%;">Acción</td>
                            </tr>
                        </thead>
                        <tbody class="text-center" id="tablaRegistro"></tbody>
                    </table>
                </div>
                <div id="carouselExampleControls" class="carousel slide mx-auto" data-ride="carousel" data-interval="false">
                    <div id="carruselGanancias" class="carousel-inner">
                        <div class="carousel-item active">
                            <div class="text-center">
                                <table class="table table-striped table-sm">
                                    <thead class="table-danger">
                                        <tr>
                                            <td colspan="3">App: BOOSMAP</td>
                                        </tr>
                                    </thead>
                                    <thead>
                                        <tr>
                                            <td style="width: 30%;">SEMANA</td>
                                            <td style="width: 30%;">HOY</td>
                                            <td style="width: 30%;">MES</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="dataBoosmap">

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <style>
                        .carousel-control-prev {
                            left: -20px;
                        }

                        .carousel-control-next {
                            right: -20px;
                        }
                    </style>
                    <button class="carousel-control-prev" type="button" data-target="#carouselExampleControls" data-slide="prev">
                        <span class="bg-secondary carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-target="#carouselExampleControls" data-slide="next">
                        <span class="bg-secondary carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </button>
                </div>
                <div id="tablaTotales" class="mx-auto text-center">
                    <table class="table table-striped table-sm">
                        <thead class="table-danger">
                            <tr>
                                <td colspan="3">Totales Apps</td>
                            </tr>
                        </thead>
                        <thead>
                            <tr>
                                <td style="width: 30%;">SEMANA</td>
                                <td style="width: 30%;">HOY</td>
                                <td style="width: 30%;">MES</td>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <tr id="dataTotales">
                                <td>$0</td>
                                <td>$0</td>
                                <td>$0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal App-->
    <div class="modal fade" id="modalApp" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAppLabel">APP AGREGADA</h5>
                </div>
                <div class="modal-body" id="modalAppMensaje">
                    La aplicación ha sido agregada con éxito
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- fin modal -->
</body>

<?php include __DIR__ . "/../partials/boostrap_script.php" ?>
<script>
    window.onload = function() {
        detallesBoosmap();
        opcionesRegistradorGanancias();
        obtenerUltimasGanancias(false);
        totalesMes();
    };
    // Obtener referencias a los elementos del DOM
    let checkbox = document.getElementById('checkboxRegistroApp');
    let nombreAppInput = document.getElementById('nombreApp');
    let agregaAppBtn = document.getElementById('agregaApp');

    // Escuchar el evento "change" del checkbox
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            nombreAppInput.disabled = false;
            agregaAppBtn.disabled = false;

        } else {
            nombreAppInput.disabled = true;
            agregaAppBtn.disabled = true;
        }
    });

    document.getElementById("registraApp").addEventListener("submit", function(event) {
        event.preventDefault();

        $.post("../aplicaciones/conexiones_app.php", {
            ingresar: "agregaApp",
            nombreApp: nombreAppInput.value.trim().toUpperCase(),
        }).done(function(datos) {

            nombreAppInput.value = "";
            nombreAppInput.disabled = true;
            agregaAppBtn.disabled = true;
            checkbox.checked = false;
            tipoDatoParaTablaAplicacion(datos);

        }).fail(function(error) {
            console.log(error)
        });
    })

    function detallesBoosmap() {

        $.post("conexiones_app.php", {
            ingresar: "totalesPeriodo",
        }).done(function(data) {
            let datos = JSON.parse(data);
            // console.log(datos)
            document.getElementById("dataBoosmap").innerHTML = `
                                                    <td>${formatoMoneda(datos[0].total)}</td>
                                                    <td>${formatoMoneda(datos[2].total)}</td>
                                                    <td>${formatoMoneda(datos[1].total)}</td>
                                                    `
        }).fail(function(error) {
            console.log(error)
        });
    }

    function opcionesRegistradorGanancias() {
        $.post("conexiones_app.php", {
            ingresar: "appRegistradas",
        }).done(function(data) {
            let datos = JSON.parse(data);
            creaOpcionesdeRegistro(datos);
            tipoDatoParaTablaAplicacion(datos);
            llenaDataApps();
        }).fail(function(error) {
            console.log(error)
        });
    }

    function creaOpcionesdeRegistro(datos) {
        document.getElementById("appNombre").innerHTML = "<option>Aplicaciones registradas</option>";
        datos.forEach(element => {
            document.getElementById("appNombre").innerHTML +=
                `<option value="${element.id}">${element.nombre_app}</option>`
        });
    }

    function tipoDatoParaTablaAplicacion(datos) {
        if (typeof(datos) != "object" && datos == 'duplicate') {
            showModal(datos);
        } else {
            if (typeof(datos) != "object") {
                $.post("conexiones_app.php", {
                    ingresar: "appRegistradas",
                    nombreApp: datos
                }).done(function(data) {
                    let datos = JSON.parse(data);
                    creaOpcionesdeRegistro(datos)
                    creaTablaAplicacion(datos);
                    showModal();
                }).fail(function(error) {
                    console.log(error)
                });


            } else {
                creaTablaAplicacion(datos);
            }
        }
    }

    function creaTablaAplicacion(datos) {

        datos.forEach(element => {
            document.getElementById("carruselGanancias").innerHTML +=
                `<div class="carousel-item">
                            <div class="text-center">
                                <table class="table table-striped table-sm">
                                    <thead class="table-danger">
                                        <tr>
                                            <td colspan="3">App: ${element.nombre_app}</td>
                                        </tr>
                                    </thead>
                                    <thead>
                                        <tr>
                                            <td style="width: 30%;">SEMANA</td>
                                            <td style="width: 30%;">HOY</td>
                                            <td style="width: 30%;">MES</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="data-${element.id}">
                                            <td>$0</td>
                                            <td>$0</td>
                                            <td>$0</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>`
        });

    }

    function llenaDataApps() {
        $.post("conexiones_app.php", {
            ingresar: "obtenerDataApps",
        }).done(function(data) {
            let montosApp = JSON.parse(data);
            montosApp.forEach(montos => {
                document.getElementById(`data-${montos.idapp}`).innerHTML = "";
                document.getElementById(`data-${montos.idapp}`).innerHTML += `
                            <td>${formatoMoneda(montos.monto_semana)}</td>
                            <td>${formatoMoneda(montos.monto_dia)}</td>
                            <td>${formatoMoneda(montos.monto_mes)}</td>
                            `
            })
        }).fail(function(error) {
            console.log(error)
        });
    }

    document.getElementById("registraGanancias").addEventListener("submit", function(event) {
        event.preventDefault();
        let appNombre = document.getElementById("appNombre")
        let inputRegistraGanancia = document.getElementById("inputRegistraGanancia")
        let fecha = document.getElementById("fecha");
        let fechaRegistro = fecha.value == "" ? "" : fecha.value + " 12:00:00";

        if (validaCampo(appNombre, inputRegistraGanancia)) {

            $.post("conexiones_app.php", {
                ingresar: "registraGanancia",
                idApp: appNombre.value,
                monto: inputRegistraGanancia.value,
                fecha: fecha.value,
                fechaYHora: fechaRegistro
            }).done(function(datos) {

                let data = JSON.parse(datos);
                let resultado = data[0].resultado;
                if (resultado != 'ok') {
                    alert(resultado)
                } else {
                    appNombre.value = "";
                    inputRegistraGanancia.value = "";
                    fecha.value = "";
                    obtenerUltimasGanancias(true)
                    llenaDataApps();
                    totalesMes();
                }
            }).fail(function(error) {
                console.log(error)
            });
        };
    })

    function validaCampo(nombre, monto, fecha) {
        if (nombre.value == "" || monto.value == "") {
            document.getElementById("campoVacioAlerta").style.display = "block";
            return false;
        } else {
            return true;
        }
    }

    function obtenerUltimasGanancias(nuevoViaje) {
        let tablaRegistro = document.getElementById("tablaRegistro")
        $.post("conexiones_app.php", {
            ingresar: "ultimasGanancias",
        }).done(function(data) {

            let datos = JSON.parse(data);
            tablaRegistro.innerHTML = "";
            datos.forEach((element, index) => {

                tablaRegistro.innerHTML +=
                    `<tr class=${nuevoViaje == true && index == 0 ? "nuevoDato" : ""}>
                    <td>${element.nombre_app}</td>
                    <td>${formatoMoneda(element.monto)}</td>
                    <td>${element.fecha}</td>
                    <td><a style="cursor: pointer" onclick="borrarGanancia(${element.id})"><i class="fas fa-trash-alt"></i></a></td>
                </tr>`
            });

        }).fail(function(error) {
            console.log(error)
        });
    }

    function borrarGanancia(id) {

        $.post("conexiones_app.php", {
            ingresar: "borrarGanancia",
            id: id
        }).done(function(datos) {
            obtenerUltimasGanancias(false)
        }).fail(function(error) {
            console.log(error)
        })
    }

    function totalesMes() {
        $.post("conexiones_app.php", {
            ingresar: "totalesMes",
        }).done(function(data) {
            let datos = JSON.parse(data);
            document.getElementById("dataTotales").innerHTML = "";
            document.getElementById("dataTotales").innerHTML = `<td>${formatoMoneda(datos[0].total_monto_semana)}</td>
                                                                <td>${formatoMoneda(datos[0].total_monto_dia)}</td>
                                                                <td>${formatoMoneda(datos[0].total_monto_mes)}</td>`;
        })

    }

    function showModal(datos) {
        if (datos == 'duplicate') {

            document.getElementById("modalAppLabel").textContent = "ERROR";
            document.getElementById("modalAppMensaje").textContent = "La aplicación ya ha sido agregada"
        }
        $('#modalApp').modal('show');
    }
</script>