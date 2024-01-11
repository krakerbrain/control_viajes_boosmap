<div class="modal fade" id="modalDetalles" tabindex="-1" role="dialog" aria-labelledby="modalDetalles" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-light">
                <h5 class="modal-title" id="modalDetalles">AGREGA DETALLES DEL VIAJE</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar" onclick="limpiarModal()">
                    <span aria-hidden="true" class="text-light">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="idViajeModal" id="idViajeModal" value="">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="checkPeaje" onchange="checkPeaje(event)">
                    <label class="form-check-label" for="checkPeaje">
                        Agrega Peaje
                    </label>
                </div>
                <div>
                    <input type="number" name="montoPeaje" id="montoPeaje" placeholder="Monto Peaje" class="my-1" oninput="escribeMontoPeaje()" disabled>
                    <i id="iconAgregarPeaje" class="fa-solid fa-check text-muted mx-3" onclick="agregaDato('Peaje')"></i>
                    <i id="iconBorrarPeaje" class='fa-solid fa-xmark text-muted' onclick="limpiaInput('montoPeaje')"></i>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="checkBono" onchange="checkBono(event)">
                    <label class="form-check-label" for="checkBono">
                        Agrega Bono o Extra
                    </label>
                </div>
                <div class="form-check d-flex my-1">
                    <div class="mr-5">
                        <input class="form-check-input" type="checkbox" value="" id="checkBruto" onchange="checkTipoMonto(event)" disabled>
                        <label class="form-check-label" for="checkBruto">
                            Bruto
                        </label>
                    </div>
                    <div>
                        <input class="form-check-input" type="checkbox" value="" id="checkLiquido" onchange="checkTipoMonto(event)" disabled>
                        <label class="form-check-label" for="checkLiquido">
                            Liquido
                        </label>
                    </div>
                </div>
                <div>
                    <input type="number" name="montoExtra" id="montoExtra" placeholder="Monto Extra" onkeyup="calculomonto(this)" oninput="cambiaIconos('activa', 'Extra')" disabled>
                    <span id="calculoMonto">$0</span>
                    <i id="iconAgregarExtra" class="fa-solid fa-check text-muted mx-3" onclick="agregaDato('Extra')"></i>
                    <i id="iconBorrarExtra" class='fa-solid fa-xmark text-muted' onclick="limpiaInput('montoExtra')"></i>
                </div>
                <table id="tableDatosModal" class="table table-striped mt-3">
                    <thead>
                        <tr class="text-center">
                            <th>Descripción</th>
                            <th>Monto</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>
                    <tbody id="tableBodyModal"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="limpiarModal()">Cancelar</button>
                <button type="button" class="btn btn-danger" id="modalConfirm" onclick="guardarDetalles()">Guardar</button>
            </div>
        </div>
    </div>
</div>
<script>
    let datos = [];
    // let contadorParaIdFilas = 0;

    function mostrarModalDetalles(id) {
        // Mostrar el modal
        document.getElementById("idViajeModal").value = id;
        cargaDatos(id);
        $('#modalDetalles').modal('show');

    }

    function cargaDatos(id) {
        $.post("conexiones.php", {
                ingresar: "getDetalles",
                idViaje: id
            })
            .done(function(data) {
                datos = JSON.parse(data);
                actualizarTabla()
            })
            .fail(function(error) {
                console.error(error);
                alert("Error al procesar la solicitud. Por favor, inténtelo de nuevo.");
            });
    }

    function limpiarModal(excepcion) {
        let inputMontoPeaje = document.getElementById("montoPeaje");
        let inputMontoExtra = document.getElementById("montoExtra");


        inputMontoPeaje.value = "";
        inputMontoExtra.value = "";

        inputMontoPeaje.disabled = true;
        inputMontoExtra.disabled = true;

        document.getElementById("checkPeaje").checked = false;
        document.getElementById("checkBono").checked = false;

        checkBruto.disabled = true;
        checkLiquido.disabled = true;
        checkBruto.checked = false;
        checkLiquido.checked = false;
        document.getElementById("montoExtra").disabled = true;

        limpiaInput('montoExtra');

        cambiaIconos("reset", "Peaje");
        cambiaIconos("reset", "Extra");
        if (excepcion != "noTable") {
            datos = [];
            document.getElementById("tableBodyModal").innerHTML = "";

        }
    }


    function checkPeaje(event) {
        let check = event.target.checked;
        let inputMontoPeaje = document.getElementById("montoPeaje");
        if (check) {
            inputMontoPeaje.disabled = false;
            inputMontoPeaje.focus();
        } else {
            inputMontoPeaje.disabled = true;
            inputMontoPeaje.value = "";
            cambiaIconos("reset", "Peaje");
        }
    }

    function checkBono(event) {

        let check = event.target.checked;
        let checkBruto = document.getElementById("checkBruto");
        let checkLiquido = document.getElementById("checkLiquido");

        if (check) {
            checkBruto.disabled = false;
            checkLiquido.disabled = false;
        } else {
            checkBruto.disabled = true;
            checkLiquido.disabled = true;
            checkBruto.checked = false;
            checkLiquido.checked = false;
            document.getElementById("montoExtra").disabled = true;
            document.getElementById("montoExtra").value = "";
            cambiaIconos("reset", "Extra");
            limpiaInput('montoExtra');

        }
    }

    function escribeMontoPeaje() {
        let inputMontoPeaje = document.getElementById("montoPeaje");
        let iconoAgregar = document.getElementById("iconAgregar");
        let iconoBorrar = document.getElementById("iconBorrar");

        if (inputMontoPeaje.value > 0) {
            cambiaIconos("activa", "Peaje");
        } else {
            cambiaIconos("reset", "Peaje");
        }
    }

    function cambiaIconos(accion, icono) {
        let iconoAgregar = document.getElementById("iconAgregar" + icono);
        let iconoBorrar = document.getElementById("iconBorrar" + icono);
        if (accion == "activa") {
            iconoAgregar.classList.remove("text-muted");
            iconoBorrar.classList.remove("text-muted");
            iconoAgregar.classList.add("text-success");
            iconoBorrar.classList.add("text-danger");
        } else {
            iconoAgregar.classList.remove("text-success");
            iconoBorrar.classList.remove("text-danger");
            iconoAgregar.classList.add("text-muted");
            iconoBorrar.classList.add("text-muted");
        }
    }

    function checkTipoMonto(event) {
        let idCheck = event.target.id;
        let check = event.target.checked;
        let montoExtraInput = document.getElementById("montoExtra");
        montoExtraInput.disabled = !check;
        montoExtraInput.value = "";
        montoExtraInput.focus();
        if (idCheck == "checkBruto") {
            document.getElementById("checkLiquido").disabled = check;

        } else {
            document.getElementById("checkBruto").disabled = check;
        }
        cambiaIconos("reset", "Extra");
        limpiaInput('montoExtra');

    }

    async function calculaFactorIslr() {
        try {
            const islrData = await cargarArchivoJSON('../islr.json');

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

    async function calculomonto(val) {
        const {
            factor,
            impuesto
        } = await calculaFactorIslr();

        let montobruto = val.value == "" ? 0 : val.value
        let monto;
        if (document.getElementById("checkBruto").checked) {
            monto = parseInt(montobruto) - (parseInt(montobruto) * (impuesto / 100))
        } else if (document.getElementById("checkLiquido").checked) {
            monto = parseInt(montobruto) / factor
        }
        document.getElementById("calculoMonto").innerHTML = `$${Math.round(monto)}`;
    }

    function limpiaInput(input) {
        document.getElementById(input).value = "";
        if (input == "montoExtra") {
            document.getElementById("calculoMonto").innerHTML = "$0";
            cambiaIconos("reset", "Extra");
        }

    }



    function agregaDato(input) {
        let tipoMontoExtra = document.getElementById("checkBruto").checked ? document.getElementById('calculoMonto')
            .textContent.slice(1) : document.getElementById("monto" +
                input).value;

        let idViaje = document.getElementById("idViajeModal").value;
        let descripcion = input;
        let monto = tipoMontoExtra;
        // Agrega el dato al array
        datos.push({
            id: "fila_" + datos.length,
            idViaje,
            descripcion,
            monto
        });

        // Actualiza la tabla en el HTML
        actualizarTabla();

        // Limpia el modal
        limpiarModal("noTable");
    }


    function eliminaDetalle(valor, descripcion) {


        if (typeof(valor) != 'number') {
            document.getElementById(valor.id).remove();
            eliminaEnArray(valor.id);
        } else {
            eliminaDataDetalles(valor, descripcion);
        }

    }

    function eliminaEnArray(id) {

        let encontrado = datos.findIndex(function(objeto) {
            return objeto.id === id;
        });

        if (encontrado !== -1) {
            datos.splice(encontrado, 1);
        }


    }

    function eliminaDataDetalles(id, descripcion) {
        $.post("conexiones.php", {
                ingresar: "eliminaDataDetalle",
                id: id,
                descripcion: descripcion
            })
            .done(function(data) {
                if (data == "true") {
                    let idViaje = document.getElementById("idViajeModal").value;
                    cargaDatos(idViaje)
                }
            })
            .fail(function(error) {
                console.error(error);
                alert("Error al procesar la solicitud. Por favor, inténtelo de nuevo.");
            });
    }

    function actualizarTabla() {
        let tabla = document.getElementById("tableBodyModal");
        // Limpia la tabla antes de volver a llenarla
        tabla.innerHTML = "";
        // Recorre el array datos y agrega cada elemento a la tabla
        for (let i = 0; i < datos.length; i++) {
            let row = tabla.insertRow(i);
            row.id = 'fila_' + i;
            row.classList.add("text-center");
            let cell1 = row.insertCell(0);
            let cell2 = row.insertCell(1);
            let cell3 = row.insertCell(2);

            cell1.innerHTML = datos[i].descripcion;
            cell2.innerHTML = datos[i].monto;



            cell3.innerHTML =
                ` <i class='fa-solid fa-xmark text-danger text-center' onclick='eliminaDetalle(${datos[i].id},"${datos[i].descripcion}")' title="Elimina Detalle"></i>`
        }
    }

    function guardarDetalles() {
        // Verificar si 'datos' está definido y tiene la estructura correcta
        if (typeof datos !== 'undefined' && datos !== null) {

            // Enviar datos a través de AJAX
            $.post("conexiones.php", {
                    ingresar: "agregaDetalles",
                    datos: JSON.stringify(datos)
                })
                .done(function(data) {
                    let paginaActiva = obtenerPaginaActiva();
                    obtenerUltimosViajes(false, paginaActiva);
                    if (data == "true") {
                        datos = [];
                        limpiarModal();
                        $('#modalDetalles').modal('hide');
                    }
                })
                .fail(function(error) {
                    console.error(error);
                    alert("Error al procesar la solicitud. Por favor, inténtelo de nuevo.");
                });
        } else {
            console.error("El objeto 'datos' no está definido o es nulo.");
        }
    }
</script>