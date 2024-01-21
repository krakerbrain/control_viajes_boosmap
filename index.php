<?php
session_start();
$sesion = isset($_SESSION['usuario']);
require __DIR__ . '/config.php';
$indice = "inicio";
if ($sesion == null || $sesion == "") {
    header($_ENV['URL_LOCAL']);
}
include "partials/header.php";

?>

<body>
    <div class="container px-0" style="max-width:850px">
        <?php include "partials/navbar.php" ?>

        <header class="row m-1">
            <div class="col-sm-7">
                <p id="lista_excel"></p>
                <table class="table table-striped table-bordered table-sm" style="font-size:small">
                    <thead class="text-center">
                        <td colspan="3">
                            <button class="btn btn-outline-danger btn_periodo" onclick="detallesViajes('semana',this)">Semana</button>
                            <button class="btn btn-danger btn_periodo" onclick="detallesViajes('hoy',this)">Hoy</button>
                            <button class="btn btn-outline-danger btn_periodo" onclick="detallesViajes('mes',this)">Mes</button>
                        </td>
                    </thead>
                    <thead class="table-secondary text-center">
                        <td>Monto Bruto</td>
                        <td>Monto Líquido</td>
                        <td>Viajes</td>
                    </thead>
                    <tbody id="detalles_viajes"></tbody>
                </table>
            </div>
            <div class="col-sm-5" style="text-align:center">
                <div class="input-group date mb-2" id="datepicker">
                    <input type="text" class="form-control">
                    <span class="input-group-append">
                        <span class="input-group-text bg-white">
                            <i class="fa fa-calendar"></i>
                        </span>
                    </span>
                </div>
                <div id="rutas" class="d-flex flex-wrap justify-content-center">
                </div>
            </div>
        </header>
        <h6 class="mx-3">Ultimos viajes</h6>
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center mt-3" style="font-size:0.8rem">
            </ul>
        </nav>


        <div class="container text-right mx-n3">
            <span class="small">Leyenda de acciones: </span>
            <a class="ml-3" tabindex="0" role="button" data-toggle="popover" data-trigger="focus" data-placement="bottom" data-content="Agrega Viajes"><i class="fa-solid fa-plus text-success" style="font-size: 1rem"></i></a>
            <a class="ml-3" tabindex="0" role="button" data-toggle="popover" data-trigger="focus" data-placement="bottom" data-content="Viaje con detalles"><i class="fa-solid fa-check text-warning" style="font-size: 1rem"></i></a>
            <a class="ml-3" tabindex="0" role="button" data-toggle="popover" data-trigger="focus" data-placement="bottom" data-content="Elimina Viaje"><i class="fa-solid fa-xmark text-danger" style="font-size: 1rem"></i></a>
        </div>
        <section style="max-height: 400px; overflow-y: auto;">
            <table class="table table-striped" style="width:97%;margin: 0 auto; table-layout:fixed;font-size:small">
                <thead class="table-danger text-center" style="position: sticky; top:-1px; z-index: 1;">
                    <td style="width:23%">Destino</td>
                    <td style="width:20%">Fecha</td>
                    <td style="width:13%">Monto</td>
                    <td style="width:16%">Acciones</td>
                </thead>
                <tbody id="tablaUltimosViajes" class="text-center"></tbody>
            </table>
        </section>

    </div>
    <?php include "modal_detalles/index.php" ?>
    <?php include "modals.php" ?>

</body>
<?php include "partials/boostrap_script.php" ?>
<script>
    const date = new Date();
    let day = date.getDate();
    let month = date.getMonth();
    let year = date.getFullYear();
    $("#datepicker").datepicker({
        format: "dd/mm/yyyy"
    });
    $("#datepicker").datepicker("setEndDate", new Date(year, month, day));
    $("#datepicker").datepicker("update", new Date(year, month, day));

    window.onload = function() {
        cargaBotonesRutas();
        detallesViajes('hoy', 'inicial');
        obtenerUltimosViajes(false);
        mostrarModalMensaje();
    };

    function agregaRuta(e) {
        var hora = new Date();
        var dia = $("#datepicker").datepicker("getFormattedDate");

        $.post("conexiones.php", {
            ingresar: "insertar",
            destino: e.value,
            dia: dia,
            hora: hora.toLocaleTimeString(),
        }).done(function(data, error) {
            obtenerUltimosViajes(comparaFecha(dia));
            detallesViajes('hoy', 'inicial');
        }).fail(function() {
            alert("error");
        });
    }

    function comparaFecha(dia) {
        // Esta funcion verifica si la fecha ingresada es igual a la fecha de hoy, si es asi retorna true, de lo contrario false. Si es true
        // la funcion obtenerUltimosViajes() se encarga de mostrar los viajes de hoy y crea un efecto para mostrar al usuario que se agregó un viaje actual
        var hoy = new Date();
        var hoyDia = ("0" + hoy.getDate()).slice(-2);
        var hoyMes = ("0" + (hoy.getMonth() + 1)).slice(-2);
        var hoyAnio = hoy.getFullYear();
        var fechaHoy = hoyDia + "/" + hoyMes + "/" + hoyAnio;
        return dia === fechaHoy ? true : false
    }



    function obtenerUltimosViajes(nuevoViaje, numeroPagina = 1) {
        tablaUltimosViajes.innerHTML = ""
        let limit = 10;
        let offset = (numeroPagina - 1) * limit;
        $.post("conexiones.php", {
            ingresar: "getUltimosViajes",
            limit: limit,
            offset: offset,
            dataType: "json"
        }).done(function(datos) {
            let data = JSON.parse(datos).data;
            let filas = JSON.parse(datos).cantidadFilas;
            data.forEach((element, index) => {

                let iconoDetalle = element.tiene_detalles == 1 ? "fa-check" : "fa-plus";
                let colorIcono = element.tiene_detalles == 1 ? "text-warning" : "text-success";
                tablaUltimosViajes.innerHTML += `<tr class=${nuevoViaje == true && index == 0 ? "nuevoDato" : ""}>
                                                    <td nowrap>${element.destino}</td>
                                                    <td nowrap>${element.fecha}</td>
                                                    <td class='text-center'>${element.monto.toLocaleString('es-CL', {style: 'currency', currency: 'CLP'})}</td>
                                                    <td style='cursor:pointer' class='text-center'  >
                                                    <div class="d-flex justify-content-around ">
                                                        <i class="fa-solid ${iconoDetalle} mr-lg-n5 ${colorIcono}" style="font-size: 1rem" title="Agrega Detalles" onclick='mostrarModalDetalles(${element.idviaje})'></i>
                                                        <i class='fa-solid fa-xmark text-danger' style="font-size: 1rem" onclick='mostrarModaleliminaViaje(${element.idviaje})' title="Elimina Viaje"></i>
                                                    </div>
                                                    </td>
                                                </tr>`
            })
            crearPaginas(filas, numeroPagina);
        });
    }

    function crearPaginas(filas, paginaActual) {
        let limit = 10; // Cantidad de resultados por página
        let numPaginas = Math.ceil(filas / limit); // Cálculo del número total de páginas

        // Obtener el elemento de la barra de paginación
        let paginacion = document.querySelector('.pagination');
        paginacion.innerHTML = ''; // Limpiar el contenido actual de la barra de paginación

        // Calcular el rango de páginas a mostrar
        let rangoInicio = Math.max(1, paginaActual - 2);
        let rangoFin = Math.min(numPaginas, rangoInicio + 4);

        // Crear enlace "Anterior" si no está en la primera página
        if (paginaActual > 1) {
            let liAnterior = document.createElement('li');
            liAnterior.classList.add('page-item');
            let enlaceAnterior = document.createElement('a');
            enlaceAnterior.classList.add('page-link');
            enlaceAnterior.href = '#';
            enlaceAnterior.textContent = 'Previous';
            enlaceAnterior.onclick = function() {
                obtenerUltimosViajes(false, paginaActual - 1); // Llamar a la función con la página anterior
            };
            liAnterior.appendChild(enlaceAnterior);
            paginacion.appendChild(liAnterior);
        }

        // Crear enlaces numéricos de páginas dentro del rango
        for (let i = rangoInicio; i <= rangoFin; i++) {
            let li = document.createElement('li');
            li.classList.add('page-item');
            let enlace = document.createElement('a');
            enlace.classList.add('page-link');
            enlace.href = '#';
            enlace.textContent = i;
            if (i === paginaActual) {
                li.classList.add('active'); // Marcar como activa la página actual
            } else {
                enlace.onclick = function() {
                    obtenerUltimosViajes(false, i); // Llamar a la función con el número de página correspondiente
                };
            }
            li.appendChild(enlace);
            paginacion.appendChild(li);
        }

        // Crear enlace "Siguiente" si no está en la última página
        if (paginaActual < numPaginas) {
            let liSiguiente = document.createElement('li');
            liSiguiente.classList.add('page-item');
            let enlaceSiguiente = document.createElement('a');
            enlaceSiguiente.classList.add('page-link');
            enlaceSiguiente.href = '#';
            enlaceSiguiente.textContent = 'Next';
            enlaceSiguiente.onclick = function() {
                obtenerUltimosViajes(false, paginaActual + 1); // Llamar a la función con la página siguiente
            };
            liSiguiente.appendChild(enlaceSiguiente);
            paginacion.appendChild(liSiguiente);
        }
    }

    async function detallesViajes(periodo, elemento) {

        let btn_periodo = document.getElementsByClassName("btn_periodo")
        for (let i = 0; i < btn_periodo.length; i++) {
            const element = btn_periodo[i];
            if (elemento != 'inicial') {
                element.classList.remove("btn-danger")
                element.classList.add("btn-outline-danger");
            }
        }
        if (elemento && elemento != 'inicial') {
            elemento.classList.remove("btn-outline-danger");
            elemento.classList.add("btn-danger");
        }
        $.post("conexiones.php", {
            ingresar: "totalmes",
            periodo: periodo
        }).done(async function(data) {
            let datos = JSON.parse(data);
            const {
                factor
            } = await calculaFactorIslr();
            datos.forEach(element => {
                let montoBruto = element.monto_total / factor;
                let montoLiquido =
                    detalles_viajes.innerHTML = `
                                            <td class="text-center">${formatoMoneda(montoBruto)}</td>
                                            <td class="text-center">${formatoMoneda(element.monto_total)}</td>
                                            <td class="text-center">${element.viajes}</td>
                                        `
            })
        });
    }

    function eliminaViaje() {
        let id = document.getElementById("idEliminaViaje").value;
        $.post("conexiones.php", {
            ingresar: "eliminar",
            id_viaje: id
        }).done(function(datos) {
            let paginaActiva = obtenerPaginaActiva();
            obtenerUltimosViajes(false, paginaActiva);
            detallesViajes('hoy', 'inicial');
        });
    }

    function obtenerPaginaActiva() {
        // Obtener el elemento de la página activa
        let paginaActivaElement = document.querySelector('.pagination .page-item.active');

        // Verificar si se encontró un elemento activo
        if (paginaActivaElement) {
            // Obtener el número de la página activa
            let numeroPaginaActiva = paginaActivaElement.querySelector('.page-link').textContent;

            // Imprimir el número de la página activa en la consola
            return parseInt(numeroPaginaActiva);
        } else {
            return 1;
        }

    }

    function cargaBotonesRutas() {
        $.post("conexiones.php", {
            ingresar: "cargarutas"
        }).done(function(datos) {
            rutas.innerHTML = datos;
        });
    }

    // Función para guardar la decisión en localStorage
    function guardarDecision() {
        // Verificar si el checkbox está marcado
        var checkbox = document.getElementById('checkNoMostrar');
        if (checkbox.checked) {
            // Guardar en localStorage
            localStorage.setItem('modalActualizacion10012024', 'true');
        }
    }
    $(function() {
        $('[data-toggle="popover"]').popover()

    })
    $('.popover-dismiss').popover({
        trigger: 'focus'
    })
</script>

</html>