<?php
require dirname(__DIR__) . '/seguridad/auth.php';
$indice = "rutas";
include dirname(__DIR__) . "/partials/header.php";
$idusuario = $datosUsuario['idusuario'];

if (isset($_REQUEST['creado'])) {
    $creado = $_REQUEST['creado'];
} else {
    // Realizar la consulta en la tabla viajes utilizando el idusuario
    $sqlViajes = $con->prepare("SELECT COUNT(*) as count FROM rutas WHERE idusuario = :idusuario");
    $sqlViajes->bindParam(':idusuario', $idusuario);
    $sqlViajes->execute();

    $resultadoViajes = $sqlViajes->fetch(PDO::FETCH_ASSOC);
    $count = $resultadoViajes['count'];

    if ($count > 0) {
        $creado = "true";
    } else {
        $creado = "false";
    }
}

?>

<?php if ($creado == "false") { ?>
    <div id="alerta-primera-vez" class="alert alert-danger mt-4  alert-dismissible fade show" role="alert">
        <span class="d-block font-weight-bold">Bienvenido al sistema.</span>
        <span>Parece que es la primera vez que ingresas, por lo tanto, debes
            <a href="" onclick="event.preventDefault();ocultaFieldset('ocultaRutas')">configurar las rutas</a> para
            crear los botones de
            registro.
        </span>
        <span class="d-block">Si eres de <span class="font-weight-bold">Viña del Mar</span> puedes crear
            automáticamente las rutas haciendo
            <a href="" onclick="event.preventDefault();agregaRutaVina()">CLICK AQUI</a>
        </span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php } ?>
<fieldset style="margin-top:20px">
    <legend style="font-size:1em;cursor:pointer"
        class="align-items-center bg-danger d-flex justify-content-between p-1 text-light"
        onclick="ocultaFieldset('ocultaRutas')">
        <span>Configuración de Rutas </span>
        <div class="ocultaRutas">
            <i class="bi bi-caret-down-square-fill"></i>
        </div>
    </legend>
    <div class="ocultaRutas" style="display: <?= $creado == "false" ? 'none' : 'block' ?>">
        <div class="text-right">
            <i class="text-danger  mr-1 far fa-question-circle" style="font-size:1.5rem" data-toggle="popover"
                data-placement="bottom"
                data-content="Aquí podrás agregar nuevas rutas o modificar las actuales. Al ir agregando rutas se irán creando botones en la página inicial que servirán para ir llenando los registros de viajes"></i>
        </div>

        <div class="row-cols-lg-2 m-2">
            <form id="formRutas" class="form-group mx-auto">
                <div>
                    <label class="form-label" for="region">Región</label>
                    <select class="custom-select" name="region" id="region">
                        <option value="">Seleccione</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="comunas">Comuna</label>
                    <select class="custom-select" name="comunas" id="comunas" onchange="borraDatos()">
                        <option value="">Seleccione</option>
                    </select>
                </div>
                <div>
                    <label for="montobruto">Monto Bruto</label>
                    <input class="form-control" type="number" id="montobruto" onkeyup="calculomonto(this,'costoruta')">
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="nobruto" onchange="checkNoBruto(event)">
                    <label class="form-check-label" for="nobruto">Seleccione si no conoce el monto bruto</label>
                </div>
                <div>
                    <label for="costoruta">Monto Líquido</label>
                    <input class="form-control" type="number" id="costoruta" placeholder="0" disabled>
                </div>
                <div>
                    <input class="btn btn-danger w-100 my-4" type="button" value="Agregar" id="agregar">
                </div>
            </form>
        </div>
        <table id="tabla-rutas" class="table table-striped">
            <thead class="table-danger text-center">
                <td>Destino</td>
                <td>Monto Líquido</td>
                <td>Eliminar</td>
            </thead>
            <tbody id="tablarutas" class="text-center"></tbody>
        </table>
    </div>
</fieldset>
<fieldset>
    <legend style="font-size:1em;cursor:pointer"
        class="align-items-center bg-danger d-flex justify-content-between p-1 text-light"
        onclick="ocultaFieldset('modificaMontos')">
        <span>Modificar montos</span>
        <div class="modificaMontos">
            <i class="bi bi-caret-down-square-fill"></i>
        </div>
    </legend>
    <div class="modificaMontos text-center" style="display:none">
        <div id="montosActualizados" class="alert alert-danger" role="alert" style="display:none">
            Los montos de rutas han sido actualizados
        </div>
        <div class="text-right">
            <i class="text-danger  mr-1 far fa-question-circle" style="font-size:1.5rem" data-toggle="popover"
                data-placement="bottom"
                data-content="Si tenemos suerte, puede ser que alguna vez aumenten las tarifas. Aquí podrás modificar los montos de las rutas ya sea el líquido o el bruto (al modificar uno el cálculo se hace automático en el otro). Si chequeas 'Actualizar viajes del mes', se actualizan todos los viajes al nuevo monto. Si chequeas 'Actualizar monto actual' se actualizarán a partir de la fecha en que hagas el cambio"></i>
        </div>
        <table class="table table-striped mx-auto">
            <thead class="table-danger text-center">
                <td>Destino</td>
                <td>Monto Bruto <input type="checkbox" id="checkEditaBruto" name=""
                        onclick="editaMonto(this,'editaBruto')"></td>
                <td>Monto Líquido <input type="checkbox" id="checkEditaLiquido" name=""
                        onclick="editaMonto(this,'editaLiquido')"></td>
            </thead>
            <tbody id="tablamodifica" class="text-center"></tbody>
        </table>
        <div class="form-check mx-auto text-left w-75" data-bs-toggle="tooltip" data-placement="left"
            title="Se actualizaran todos los viajes del mes">
            <input class="form-check-input" type="checkbox" id="actualizaMes">
            <label class="form-check-label" for="flexCheckDefault">
                Actualizar viajes del mes
            </label>
        </div>
        <button type="button" id="guardar" class="btn btn-danger w-75 my-4" data-toggle="modal"
            data-target="#exampleModal" onclick="guardaCambioMontoRuta()">
            Guardar
        </button>

    </div>
</fieldset>

<?php
require_once 'modales_rutas.php';
require_once dirname(__DIR__) . '/modal/warningModal.php';
?>
</body>
<?php include dirname(__DIR__) . "/partials/boostrap_script.php" ?>
<script type="text/javascript" src="../componente/js/manejo-de-fieldsets.js"></script>

</html>

<script>
    function guardaCambioMontoRuta() {
        const inputConPrecios = document.querySelectorAll('.editaLiquido');
        const actualizaMes = document.getElementById('actualizaMes').checked;
        const nuevosPrecios = [];

        for (const element of inputConPrecios) {
            if (actualizaMes || element.value !== element.defaultValue) {
                nuevosPrecios.push({
                    id: element.id,
                    precio: element.value
                });
            }
        }

        if (nuevosPrecios.length > 0) {
            actualizaPrecios(nuevosPrecios, actualizaMes);
        } else {
            warningModal("Atención", "No se han realizado cambios en los montos.");
        }
    }

    function actualizaPrecios(nuevosprecios, actualizaMes) {
        if (nuevosprecios != "") {
            $.post("conexiones_rutas.php", {
                ingresar: "actualizaPrecios",
                nuevosPrecios: JSON.stringify(nuevosprecios),
                actualizaMes: actualizaMes,
                // actualizaActual: actualizaActual
            }).done(function(datos) {
                warningModal("Éxito", datos);
                // sessionStorage.setItem('actualizado', true);
                recargaFieldsetCambioMontos()
            }).fail(function() {
                alert("error");
            });
        }
    }

    async function calculomonto(val, campoActualiza) {
        const {
            factor,
            impuesto
        } = await calculaFactorIslr();
        let montobruto = val.value == "" ? 0 : val.value
        let monto;
        if (typeof(campoActualiza) == 'number' || campoActualiza == 'costoruta') {
            monto = parseInt(montobruto) - (parseInt(montobruto) * (impuesto / 100))
        } else {
            monto = parseInt(montobruto) / factor
        }
        document.getElementById(campoActualiza).value = Math.round(monto);
    }

    function checkNoBruto(e) {
        if (e != undefined && e.target.checked) {
            document.getElementById('montobruto').disabled = "true"
            document.getElementById('costoruta').removeAttribute("disabled")
        } else {
            document.getElementById('costoruta').disabled = "true"
            document.getElementById('costoruta').value = 0
            document.getElementById('montobruto').removeAttribute("disabled")
        }
    }

    const seleccionaRegion = document.getElementById("region");
    const btnAgregar = document.getElementById('agregar');

    window.onload = function() {
        cargarRegiones(); // Llamada a la función para cargar regiones
    }

    function cargarRegiones() {
        fetch('comunas-regiones.json') // Ruta al archivo JSON
            .then(response => response.json()) // Convierte la respuesta a JSON
            .then(data => {
                // Obtener el select de regiones
                const region = document.getElementById("region");

                // Limpiar las opciones existentes
                region.innerHTML = "";

                // Crear la opción inicial "Seleccione"
                region.innerHTML += '<option value="">Seleccione</option>';

                // Recorrer las regiones y agregar las opciones al select
                data.regiones.forEach(reg => {
                    region.innerHTML += `<option value="${reg.clave}">${reg.region}</option>`;
                });

                // Llamar a la función obtenerruta si es necesario
                obtenerruta();
            })
            .catch(error => {
                console.error("Error al cargar las regiones:", error);
                alert("Hubo un error al cargar las regiones");
            });
    }

    function activaAlerta() {
        document.getElementById('montosActualizados').style.display = "block";
        setTimeout(() => {
            document.getElementById('montosActualizados').style.display = "none"
            // sessionStorage.removeItem('actualizado')
        }, 4000);
    }

    // Evento para cuando se selecciona una región
    document.getElementById("region").addEventListener("change", function(e) {
        // Obtener la clave de la región seleccionada
        const claveRegion = e.target.value;

        // Obtener el select de comunas
        const selectComunas = document.getElementById("comunas");

        // Limpiar las opciones previas
        selectComunas.innerHTML = '<option value="">Seleccione</option>';

        // Cargar el archivo JSON de las comunas y regiones
        fetch('comunas-regiones.json')
            .then(response => response.json()) // Convertir la respuesta a JSON
            .then(data => {
                // Buscar las comunas de la región seleccionada
                const regionSeleccionada = data.regiones.find(region => region.clave === claveRegion);

                // Si encontramos la región seleccionada, cargar sus comunas
                if (regionSeleccionada) {
                    // Usamos backticks para crear las opciones dinámicamente
                    const opcionesComunas = regionSeleccionada.comunas.map(comuna => {
                        return `<option value="${comuna}">${comuna}</option>`;
                    }).join(""); // `.join("")` une todas las opciones generadas en un solo string

                    // Insertamos las opciones generadas en el select
                    selectComunas.innerHTML += opcionesComunas;
                }
            })
            .catch(error => {
                console.error("Error al cargar el archivo JSON:", error);
            });
    });


    document.querySelector('form').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            validaRuta(e)
        }
    });

    btnAgregar.addEventListener("click", function(e) {
        validaRuta(e)
    });

    async function validaRuta(e) {

        var region = document.getElementById('region').value;
        var comunaSeleccionada = document.getElementById('comunas').value;
        var montobruto = parseFloat(document.getElementById('montobruto').value);
        var costoruta = parseFloat(document.getElementById('costoruta').value);
        var checkBruto = document.getElementById('nobruto').checked;

        try {
            var datos = await verificaComuna(comunaSeleccionada);

            if (datos === "true") {
                warningModal("Error", "La comuna seleccionada ya ha sido ingresada");
                return;
            }

            if (comunaSeleccionada === "" || isNaN(costoruta) || isNaN(montobruto) || region === "") {
                if (checkBruto && costoruta === 0) {

                    warningModal("Error", "El monto no puede ser 0.");
                } else {
                    warningModal("Error", "Debe llenar todos los campos.");
                }
                return;
            } else if (costoruta === 0) {
                warningModal("Error", "El monto no puede ser 0.");
                return;
            }

            confirmModal(comunaSeleccionada, costoruta);
        } catch (error) {
            console.error(error);
            warningModal("Error", "Error al realizar la verificación.");
        }
    }

    function verificaComuna(comunaSeleccionada) {
        return new Promise(function(resolve, reject) {
            $.post("conexiones_rutas.php", {
                ingresar: "verificarComunas",
                comuna: comunaSeleccionada
            }).done(function(datos) {
                resolve(datos);
            }).fail(function() {
                reject("error");
            });
        });
    }

    function confirmModal(comunaSeleccionada, costoruta) {

        let modalTitle = document.querySelector("#confirmModalTitle");
        let modalBody = document.querySelector("#confirmModalBody");
        modalTitle.innerText = "Confirmación";
        modalBody.innerText =
            `¿Desea ingresar la comuna de ${comunaSeleccionada} con un monto líquido por viaje de ${parseFloat(costoruta).toLocaleString('es-CL', {style: 'currency', currency: 'CLP'})}?`;


        // Mostrar modal
        $('#confirmModalRutas').modal('show');
    }

    document.getElementById('confirmModalAction').addEventListener('click', function() {
        let comunaSeleccionada = document.getElementById('comunas').value;
        let costoruta = document.getElementById('costoruta').value;
        agregaRuta(comunaSeleccionada, costoruta);
    })

    function warningModal(titulo, contenido) {

        let modalTitle = document.querySelector("#alertModalTitle");
        let modalBody = document.querySelector("#alertModalBody");

        modalTitle.innerText = titulo;
        modalBody.innerText = contenido;

        // Mostrar modal
        $('#alertModal').modal('show');
    }

    async function agregaRuta(comunaSeleccionada, costoruta) {
        // Verifica la comuna antes de enviar los datos
        var datos = await verificaComuna(comunaSeleccionada);
        // Cierra el modal
        $('#confirmModalRutas').modal('hide');

        if (datos === "true") {
            warningModal("Error", "La comuna ya está registrada.");
            return;
        }

        // Enviar los datos al servidor
        try {
            const response = await $.post("conexiones_rutas.php", {
                ingresar: "agregaruta",
                comuna: comunaSeleccionada,
                costoruta: costoruta,
            });

            // Mostrar el mensaje basado en la respuesta del servidor
            warningModal("Éxito", response); // El servidor devuelve un mensaje de éxito o error
            document.getElementById('formRutas').reset();
            obtenerruta();
        } catch (error) {
            console.error("Error al agregar la ruta:", error);
            warningModal("Error", "Hubo un problema al agregar la comuna.");
        }
    }

    async function agregaRutaVina() {
        try {

            // Cargar los datos de los archivos JSON
            const comunasData = await cargarArchivoJSON('ruta-vina.json?v=2');
            const islrData = await cargarArchivoJSON('../islr.json?v=2028');

            if (!comunasData || !islrData) {
                console.error('No se pudieron cargar los datos necesarios.');
                return;
            }

            // Obtener el año actual y el mes actual
            const currentDate = new Date();
            const anioActual = currentDate.getFullYear();
            const mesActual = currentDate.getMonth() + 1;
            const anioParaCalculo = mesActual === 12 ? anioActual + 1 : anioActual;

            for (let i = 0; i < comunasData.length; i++) {
                const comuna = comunasData[i].comuna;
                const montoBruto = comunasData[i]["monto-bruto"];

                // Si el mes actual es diciembre, se toma el año siguiente

                // Buscar el factor correspondiente al año actual en el archivo islr.json
                let factor;
                for (const islr of islrData) {
                    if (islr.anio === anioParaCalculo) {
                        factor = islr.factor;
                        break;
                    }
                }
                // Calcular el costo líquido
                const costoLiquido = Math.round(montoBruto * factor);
                var datos = await verificaComuna(comuna);
                if (datos != "true") {
                    // Realizar la inserción en la base de datos utilizando la consulta SQL
                    $.post("conexiones_rutas.php", {
                        ingresar: "agregaviaje",
                        comuna: comuna,
                        costoruta: costoLiquido
                    }).done(function(datos) {
                        obtenerruta();
                        document.getElementsByClassName('ocultaRutas')[1].style.display = "block";
                        document.getElementById('alerta-primera-vez').classList.remove('show');
                        document.getElementById('alerta-primera-vez').style.display = "none";
                        var tablaRutas = document.getElementById('tabla-rutas');
                        tablaRutas.scrollIntoView({
                            behavior: 'smooth',
                            block: 'end'
                        });
                    }).fail(function() {
                        alert("Error al insertar los datos");
                    });
                }
            }
        } catch (error) {
            console.error('Error al leer el archivo JSON:', error);
        }
    }

    function obtenerruta() {
        $.post("conexiones_rutas.php", {
            ingresar: "obtenerRutas"
        }).done(async function(datos) {
            let data = JSON.parse(datos);
            let configuraRutas = "";
            let modificaMontos = "";
            const {
                factor
            } = await calculaFactorIslr();
            data.map(function(clave) {
                configuraRutas += `<tr>
                        <td nowrap>${clave.ruta}</td>
                        <td class='text-center'>${clave.costoruta}</td>
                        <td style='cursor:pointer' class='text-center' onclick='eliminaRuta("${clave.ruta}",${clave.idruta})'>
                        <i class='fa-solid fa-xmark text-danger'></i>
                        </td>
                     </tr>`;
                modificaMontos += `<tr>
                                <td nowrap>${clave.ruta}</td>
                                <td class='text-center'>
                                    <input type='text' 
                                            id='bruto${clave.idruta}'
                                            class='editaBruto text-center border-0 bg-light w-100' 
                                            data-value='${Math.round(clave.costoruta/factor)}' 
                                            value='${Math.round(clave.costoruta/factor)}'
                                            onkeyup="calculomonto(this,${clave.idruta})"
                                            disabled />
                                </td>
                                <td class='text-center'>
                                    <input type='text'
                                            id='${clave.idruta}'  
                                            class='editaLiquido text-center border-0 bg-light w-100' 
                                            data-value='${clave.costoruta}' 
                                            value='${clave.costoruta}'
                                            onkeyup="calculomonto(this,'bruto${clave.idruta}')"
                                            disabled/>
                                </td>
                                </tr>`;


            })
            tablarutas.innerHTML = configuraRutas;
            tablamodifica.innerHTML = modificaMontos;

        }).fail(function() {
            alert("error");
        });
    }


    function editaMonto(checkbox, campos) {
        const otroCheckbox = checkbox.id === 'checkEditaBruto' ?
            document.getElementById('checkEditaLiquido') :
            document.getElementById('checkEditaBruto');

        // Deshabilitar el otro checkbox cuando este está activado
        otroCheckbox.disabled = checkbox.checked;

        // Manejar los campos relacionados
        const elementos = document.getElementsByClassName(campos);
        const habilitar = checkbox.checked;

        for (let i = 0; i < elementos.length; i++) {
            elementos[i].disabled = !habilitar;

            if (habilitar) {
                elementos[i].classList.remove('bg-light', 'border-0');
                if (i === 0) elementos[i].focus();
            }
        }

        // Si se desmarca, obtener ruta (solo si todos están desmarcados)
        if (!checkbox.checked && !otroCheckbox.checked) {
            obtenerruta();
        }
    }

    function recargaFieldsetCambioMontos() {
        const todosCheckboxes = document.querySelectorAll('input[type="checkbox"]');
        todosCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
            checkbox.disabled = false;
        });

        // Deshabilitar todos los campos asociados (bruto y líquido)
        const camposBruto = document.getElementsByClassName('clase-campos-bruto'); // Ej: "monto-bruto"
        const camposLiquido = document.getElementsByClassName('clase-campos-liquido'); // Ej: "monto-liquido"

        // Deshabilitar campos de bruto
        for (let campo of camposBruto) {
            campo.disabled = true;
            campo.classList.add('bg-light', 'border-0');
        }

        // Deshabilitar campos de líquido
        for (let campo of camposLiquido) {
            campo.disabled = true;
            campo.classList.add('bg-light', 'border-0');
        }

        // Opcional: Resetear valores o llamar a obtenerruta()
        obtenerruta();
        activaAlerta();
    }


    function eliminaRuta(ruta, id) {
        var mensaje = `¿Está seguro de eliminar la ruta ${ruta}`;
        if (confirm(mensaje) == true) {
            $.post("conexiones_rutas.php", {
                ingresar: "eliminaRuta",
                idruta: id
            }).done(function(datos) {
                obtenerruta();
            }).fail(function() {
                alert("error");
            });
        } else {
            return
        }
    }


    function borraDatos() {
        document.getElementById('costoruta').value = "";
        document.getElementById('montobruto').value = "";
        checkNoBruto()
        document.getElementById('montobruto').focus();
    }

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    $(function() {
        $('[data-toggle="popover"]').popover()
    })
</script>