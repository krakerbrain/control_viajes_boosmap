<?php

require __DIR__ . '/../config.php';
require __DIR__ . '/../seguridad/JWT/jwt.php';
$datosUsuario = validarToken();
$indice = "aplicaciones";

if (!$datosUsuario) {
    header($_ENV['URL_LOCAL']);
    exit;
}
include __DIR__ . '/../partials/header.php';
?>

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
                    <input class="form-control" type="number" id="inputRegistraGanancia"
                        placeholder="Último registro App">
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
            <button class="carousel-control-prev" type="button" data-target="#carouselExampleControls"
                data-slide="prev">
                <span class="bg-secondary carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-target="#carouselExampleControls"
                data-slide="next">
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
    window.onload = async function() {
        opcionesRegistradorGanancias();
        obtenerUltimasGanancias(false);
        await getDataApps();
        creaCarruselApps();
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

    function opcionesRegistradorGanancias() {
        $.post("conexiones_app.php", {
            ingresar: "appRegistradas",
        }).done(function(data) {
            let datos = JSON.parse(data);
            creaOpcionesdeRegistro(datos);
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

    function getDataApps() {
        return new Promise((resolve, reject) => {
            $.post("conexiones_app.php", {
                ingresar: "obtenerDataApps",
            }).done(function(data) {
                let datos = JSON.parse(data);
                let localStorageData = localStorage.getItem("dataApps");
                if (JSON.stringify(datos) !== localStorageData) {
                    localStorage.setItem("dataApps", JSON.stringify(datos));
                }
                resolve();
            }).fail(function(error) {
                reject(error);
            });
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
            }).done(async function(datos) {

                let data = JSON.parse(datos);
                let resultado = data[0].resultado;
                if (resultado != 'ok') {
                    alert(resultado)
                } else {
                    appNombre.value = "";
                    inputRegistraGanancia.value = "";
                    fecha.value = "";
                    obtenerUltimasGanancias(true)
                    await getDataApps();
                    creaCarruselApps();
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
            tablaRegistroHtml = "";
            datos.forEach((element, index) => {

                tablaRegistroHtml +=
                    `<tr class=${nuevoViaje == true && index == 0 ? "nuevoDato" : ""}>
                        <td>${element.nombre_app}</td>
                        <td>${formatoMoneda(element.monto)}</td>
                        <td>${element.fecha}</td>
                        <td><a style="cursor: pointer" onclick="borrarGanancia(${element.id})"><i class="fas fa-trash-alt"></i></a></td>
                    </tr>`
            });

            tablaRegistro.innerHTML = tablaRegistroHtml;

        }).fail(function(error) {
            console.log(error)
        });
    }

    function borrarGanancia(id) {

        $.post("conexiones_app.php", {
            ingresar: "borrarGanancia",
            id: id
        }).done(async function(datos) {
            obtenerUltimasGanancias(false)
            await getDataApps();
            creaCarruselApps();
            totalesMes();
        }).fail(function(error) {
            console.log(error)
        })
    }

    function creaCarruselApps() {
        try {
            //modficar para obtener data del localStorage

            const datos = JSON.parse(localStorage.getItem("dataApps"));
            // console.log(datos)
            let carruselHTML = '';
            for (let aplicacion in datos) {
                let detalles = datos[aplicacion];
                let isActive = aplicacion === "BOOSMAP" ? "active" :
                    ""; // Verifica si la aplicación es "boosmap" para activarla

                carruselHTML += `
        <div class="carousel-item ${isActive}">
            <div class="text-center">
                <table class="table table-striped table-sm">
                    <thead class="table-danger">
                        <tr>
                            <td colspan="3">App: ${aplicacion.toUpperCase()}</td>
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
                        <tr id="data-${aplicacion}">
                            <td>${formatoMoneda(detalles.semana)}</td>
                            <td>${formatoMoneda(detalles.dia)}</td>
                            <td>${formatoMoneda(detalles.mes)}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>`;
            }

            // Agregar el HTML al elemento con id "carruselGanancias"
            document.getElementById("carruselGanancias").innerHTML = carruselHTML

        } catch (error) {
            console.log(error)
        }
    }

    function totalesMes() {
        try {
            const data = JSON.parse(localStorage.getItem("dataApps"));
            // console.log(data)
            // Variables para almacenar los totales
            let totalDia = 0;
            let totalSemana = 0;
            let totalMes = 0;
            document.getElementById("dataTotales").innerHTML = "";
            // Itera sobre las claves (nombre de las aplicaciones) del objeto data
            for (let appName in data) {
                if (data.hasOwnProperty(appName)) {
                    // Obtén los datos de la aplicación actual
                    let appData = data[appName];
                    // Suma los montos de la aplicación a los totales respectivos
                    totalDia += parseFloat(appData.dia);
                    totalSemana += parseFloat(appData.semana);
                    totalMes += parseFloat(appData.mes);
                }
            }

            // Construye una fila de tabla HTML para mostrar los totales
            let html = `<tr><td>${formatoMoneda(totalSemana)}</td>
                 <td>${formatoMoneda(totalDia)}</td>
                 <td>${formatoMoneda(totalMes)}</td></tr>`;

            // Coloca el HTML generado en el elemento con id "dataTotales"
            document.getElementById("dataTotales").innerHTML = html;
        } catch (error) {
            console.error("Error al obtener los datos totales:", error);
        }
    }



    function showModal(datos) {
        if (datos == 'duplicate') {

            document.getElementById("modalAppLabel").textContent = "ERROR";
            document.getElementById("modalAppMensaje").textContent = "La aplicación ya ha sido agregada"
        }
        $('#modalApp').modal('show');
    }
</script>