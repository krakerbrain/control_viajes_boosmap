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
        <section style="max-height: 400px; overflow-y: auto;">
            <table class="table table-striped" style="width:97%;margin: 0 auto; table-layout:fixed;font-size:small">
                <thead class="table-danger text-center" style="position: sticky; top:-1px; z-index: 1;">
                    <td style="width:23%">Destino</td>
                    <td style="width:20%">Fecha</td>
                    <td style="width:13%">Monto</td>
                    <td style="width:16%">Eliminar</td>
                </thead>
                <tbody id="tablaUltimosViajes" class="text-center"></tbody>
            </table>
        </section>

    </div>

    <!-- Modal de mensaje -->
    <div class="modal fade" id="modalMensaje" tabindex="-1" role="dialog" aria-labelledby="modalMensajeGeneral" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-light">
                    <h5 class="modal-title" id="modalMensajeGeneral">AVISO DE ACTUALIZACIÓN</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true" class="text-light">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Durante los próximos días se harán algunas actualizaciones de la app para modificar el nuevo
                        porcentaje
                        del ISLR.</p>
                    <p> El mismo pasa de 13% a 13.75%.</p>
                    <p>Como el pago de la última quincena (en Viña del Mar) es en Enero va a haber una diferencia en el
                        pago y para que los montos sean mostrados lo más cercano posible a la realidad es necesario
                        actualizar
                        el monto de todos los viajes desde el 16/12 en adelante.</p>
                    <p>También se actualizará el monto actual de las rutas ya que, como esta app refleja el monto
                        líquido pagado por viaje,
                        al haber un cambio de % de retención de ISLR el monto líquido es menos dinero por viaje que el
                        año anterior.</p>
                    <p>Trataré de ser lo más cuidadoso posible para no alterar los datos registrados, pero si llega a
                        pasar algo recuerden que pueden
                        contactarme para ayudarlos a corregir cualquier problema. Los que tienen mi número pueden
                        hacerlo
                        por ahí y si no por correo: admin@biowork.xyz</p>
                    <!-- Checkbox -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkNoMostrar">
                        <label class="form-check-label small" for="checkNoMostrar">
                            He leído y no deseo ver de nuevo este mensaje
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="guardarDecision()">Aceptar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- fin modal mensaje-->

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
        mostrarModal();

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
            ingresar: "obtener",
            limit: limit,
            offset: offset,
            dataType: "json"
        }).done(function(datos) {
            let data = JSON.parse(datos).data;
            let filas = JSON.parse(datos).cantidadFilas;
            data.forEach((element, index) => {
                tablaUltimosViajes.innerHTML += `<tr class=${nuevoViaje == true && index == 0 ? "nuevoDato" : ""}>
      <td nowrap>${element.destino}</td>
      <td nowrap>${element.fecha}</td>
      <td class='text-center'>${element.monto.toLocaleString('es-CL', {style: 'currency', currency: 'CLP'})}</td>
      <td style='cursor:pointer' class='text-center' onclick='eliminaViaje(${element.idviaje})' >
      <i class='fa-solid fa-xmark text-danger'></i>
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
                detalles_viajes.innerHTML = `
                                            <td class="text-center">${Math.round(element.monto_total/factor).toLocaleString('es-CL', {style: 'currency', currency: 'CLP'})}</td>
                                            <td class="text-center">${(element.monto_total == null ? 0 : element.monto_total).toLocaleString('es-CL', {style: 'currency', currency: 'CLP'})}</td>
                                            <td class="text-center">${element.viajes}</td>
                                        `
            })
        });
    }
    async function cargarArchivoJSON(url) {
        try {
            const response = await fetch(url);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error al cargar el archivo JSON:', error);
            return null;
        }
    }

    async function calculaFactorIslr() {
        try {
            const islrData = await cargarArchivoJSON('islr.json');

            // Obtener el año actual y el mes actual
            const currentDate = new Date();
            const anioActual = currentDate.getFullYear();
            const mesActual = currentDate.getMonth() + 1;
            const anioParaCalculo = mesActual === 12 ? anioActual + 1 : anioActual;

            // Buscar el factor correspondiente al año actual en el archivo islr.json
            let factor;
            let impuesto;

            for (const islr of islrData) {
                if (islr.anio === anioParaCalculo) {
                    factor = islr.factor;
                    impuesto = islr.impuesto;
                    break;
                }
            }

            return {
                factor,
                impuesto
            };
        } catch (error) {
            console.error('Error al leer el archivo JSON:', error);
            return null;
        }
    }

    function eliminaViaje(id) {
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
            localStorage.setItem('noMostrarModal', 'true');
        }
    }

    function mostrarModal() {
        // Verificar si la decisión de no mostrar el modal está almacenada en localStorage
        if (localStorage.getItem('noMostrarModal') !== 'true') {
            // Mostrar el modal
            $('#modalMensaje').modal('show');
        }
    }
</script>

</html>