<?php
session_start();
$sesion = isset($_SESSION['usuario']);
require __DIR__ . '/../config.php';
include __DIR__ . "/../partials/header.php";
$indice = "rutas";

if (isset($_REQUEST['creado'])) {
    $creado = $_REQUEST['creado'];
} else {
    $sqlUsuario = $con->prepare("SELECT idusuario FROM usuarios WHERE nombre = :nombreUsuario");
    $sqlUsuario->bindParam(':nombreUsuario', $_SESSION['usuario']);
    $sqlUsuario->execute();

    $resultadoUsuario = $sqlUsuario->fetch(PDO::FETCH_ASSOC);
    $idusuario = $resultadoUsuario['idusuario'];

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

<body>
    <div class="container px-0" style="max-width:850px">
        <?php include __DIR__ . "/../partials/navbar.php"; ?>
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
            <legend style="font-size:1em;cursor:pointer" class="align-items-center bg-danger d-flex justify-content-between p-1 text-light" onclick="ocultaFieldset('ocultaRutas')">
                <span>Configuración de Rutas </span>
                <div class="ocultaRutas">
                    <i class="bi bi-caret-down-square-fill"></i>
                </div>
            </legend>
            <div class="ocultaRutas" style="display: <?= $creado == "false" ? 'none' : 'block' ?>">
                <div class="text-right">
                    <i class="text-danger  mr-1 far fa-question-circle" style="font-size:1.5rem" data-toggle="popover" data-placement="bottom" data-content="Aquí podrás agregar nuevas rutas o modificar las actuales. Al ir agregando rutas se irán creando botones en la página inicial que servirán para ir llenando los registros de viajes"></i>
                </div>
                <div id="montosActualizados" class="alert alert-danger" role="alert" style="display:none">
                    Los montos de rutas han sido actualizados
                </div>
                <div class="row-cols-lg-2 m-2">
                    <form action="conexiones_rutas.php" method="post" class="mx-auto">
                        <button type="submit" disabled hidden aria-hidden="true"></button>
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
                        <td>Monto</td>
                        <td>Eliminar</td>
                    </thead>
                    <tbody id="tablarutas" class="text-center"></tbody>
                </table>
            </div>
        </fieldset>
        <fieldset>
            <legend style="font-size:1em;cursor:pointer" class="align-items-center bg-danger d-flex justify-content-between p-1 text-light" onclick="ocultaFieldset('modificaMontos')">
                <span>Modificar montos</span>
                <div class="modificaMontos">
                    <i class="bi bi-caret-down-square-fill"></i>
                </div>
            </legend>
            <div class="modificaMontos text-center" style="display:none">
                <div class="text-right">
                    <i class="text-danger  mr-1 far fa-question-circle" style="font-size:1.5rem" data-toggle="popover" data-placement="bottom" data-content="Si tenemos suerte, puede ser que alguna vez aumenten las tarifas. Aquí podrás modificar los montos de las rutas ya sea el líquido o el bruto (al modificar uno el cálculo se hace automático en el otro). Si chequeas 'Actualizar viajes del mes', se actualizan todos los viajes al nuevo monto. Si chequeas 'Actualizar monto actual' se actualizarán a partir de la fecha en que hagas el cambio"></i>
                </div>
                <table class="table table-striped mx-auto">
                    <thead class="table-danger text-center">
                        <td>Destino</td>
                        <td>Monto Bruto <input type="checkbox" id="checkEditaBruto" name="" onclick="editaMonto(this,'editaBruto')"></td>
                        <td>Monto Líquido <input type="checkbox" id="checkEditaLiquido" name="" onclick="editaMonto(this,'editaLiquido')"></td>
                    </thead>
                    <tbody id="tablamodifica" class="text-center"></tbody>
                </table>
                <div class="form-check mx-auto text-left w-75" data-bs-toggle="tooltip" data-placement="left" title="Se actualizaran todos los viajes del mes">
                    <input class="form-check-input" type="checkbox" id="actualizaMes" onclick="activaGuardar(this)">
                    <label class="form-check-label" for="flexCheckDefault">
                        Actualizar viajes del mes
                    </label>
                </div>
                <div class="form-check mx-auto text-left w-75" style="margin-left:-3px" data-bs-toggle="tooltip" data-placement="left" title="Todos los viajes que se registren desde ahora tendrán el monto actualizado. Se mantienen los montos de rutas anteriores">
                    <input class="form-check-input" type="checkbox" id="actualizaActual" onclick="activaGuardar(this)">
                    <label class="form-check-label" for="flexCheckDefault">
                        Actualizar monto actual
                    </label>
                </div>
                <div data-bs-toggle="tooltip" data-bs-placement="top" title="Debe seleccionar al menos una opción">
                    <button type="button" id="guardar" class="btn btn-danger w-75 my-4" data-toggle="modal" data-target="#exampleModal" onclick="obtieneNuevosPrecios()" disabled>
                        Guardar
                    </button>
                </div>
            </div>
        </fieldset>
        <!-- Modal Actualiza Precios-->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">PRECIOS ACTUALIZADOS</h5>
                    </div>
                    <div class="modal-body">
                        Los precios han sido actualizados
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-danger" id="modalConfirm" style="display:none">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- fin modal -->
</body>
<?php include __DIR__ . "/../partials/boostrap_script.php" ?>
<script type="text/javascript" src="../componente/js/manejo-de-fieldsets.js"></script>

</html>

<script>
    function activaGuardar(e) {
        let actualizaMes = document.getElementById('actualizaMes').checked;
        let actualizaActual = document.getElementById('actualizaActual').checked;
        if (e.checked || actualizaMes || actualizaActual) {
            document.getElementById('guardar').disabled = false;
        } else {
            document.getElementById('guardar').disabled = true;
        }
    }

    function obtieneNuevosPrecios() {
        let inputConPrecios = document.querySelectorAll('.editaLiquido');
        let actualizaMes = document.getElementById('actualizaMes').checked;
        let actualizaActual = document.getElementById('actualizaActual').checked;
        let nuevosPrecios = new Array;
        for (let i = 0; i < inputConPrecios.length; i++) {
            const element = inputConPrecios[i];
            if (element.value != element.defaultValue) {
                data = {
                    id: element.id,
                    precio: element.value
                }
                nuevosPrecios.push(data);
            }
        }
        actualizaPrecios(nuevosPrecios, actualizaMes, actualizaActual)
    }

    function actualizaPrecios(nuevosprecios, actualizaMes, actualizaActual) {
        if (nuevosprecios != "") {
            $.post("conexiones_rutas.php", {
                ingresar: "actualizaPrecios",
                nuevosPrecios: JSON.stringify(nuevosprecios),
                actualizaMes: actualizaMes,
                actualizaActual: actualizaActual
            }).done(function(datos) {
                sessionStorage.setItem('actualizado', true);
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
        if (sessionStorage.getItem('actualizado')) {
            activaAlerta()
        }
        $.get("conexiones_rutas.php", {
                ingresar: "cargaregiones"
            })
            .done(function(datos) {
                region.innerHTML += datos;
                obtenerruta();
            })
            .fail(function() {
                alert("error")
            });
    }

    function activaAlerta() {
        document.getElementById('montosActualizados').style.display = "block";
        setTimeout(() => {
            document.getElementById('montosActualizados').style.display = "none"
            sessionStorage.removeItem('actualizado')
        }, 4000);
    }

    seleccionaRegion.addEventListener("change", function(e) {
        $.post("conexiones_rutas.php", {
            ingresar: "cargacomunas",
            region: e.target.form.region.value
        }).done(function(datos) {
            comunas.innerHTML = datos;
        }).fail(function() {
            alert("error");
        });
    })

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
                mostrarModal("Error", "La comuna seleccionada ya ha sido ingresada", 'cierre');
                return;
            }

            if (comunaSeleccionada === "" || isNaN(costoruta) || isNaN(montobruto) || region === "") {
                if (checkBruto && costoruta === 0) {

                    mostrarModal("Error", "El monto no puede ser 0.", 'cierre');
                } else {
                    mostrarModal("Error", "Debe llenar todos los campos.", 'cierre');
                }
                return;
            } else if (costoruta === 0) {
                mostrarModal("Error", "El monto no puede ser 0.", 'cierre');
                return;
            }

            var conf =
                `¿Desea ingresar la comuna de ${comunaSeleccionada} con un monto líquido por viaje de ${parseFloat(costoruta).toLocaleString('es-CL', {style: 'currency', currency: 'CLP'})}`;
            mostrarModal("Confirmación", conf, 'confirmacion', comunaSeleccionada, costoruta);
        } catch (error) {
            console.error(error);
            mostrarModal("Error", "Error al realizar la verificación.", 'cierre');
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

    function mostrarModal(titulo, contenido, accion, comuna, costo) {
        var modalTitle = document.getElementById("exampleModalLabel");
        var modalBody = document.querySelector("#exampleModal .modal-body");
        var modalFooter = document.querySelector("#exampleModal .modal-footer");
        var closeButton = document.querySelector("#exampleModal .modal-footer button");

        modalTitle.innerText = titulo;
        modalBody.innerText = contenido;


        if (accion === "cierre") {
            closeButton.setAttribute("onclick", `$('#exampleModal').modal('hide')`);
        } else if (accion === 'confirmacion') {
            var confirmButton = document.getElementById('modalConfirm');
            confirmButton.style.display = "block";
            confirmButton.addEventListener("click", function() {
                agregaRuta(comuna, costo);
            });
        } else {
            closeButton.setAttribute("onclick", "location.reload()");
        }

        $("#exampleModal").modal("show");
    }

    async function agregaRuta(comunaSeleccionada, costoruta) {
        var datos = await verificaComuna(comunaSeleccionada);
        if (datos != "true") {
            $.post("conexiones_rutas.php", {
                ingresar: "agregaviaje",
                comuna: comunaSeleccionada,
                costoruta: costoruta,
            }).done(function(datos) {
                obtenerruta();
                document.getElementById('modalConfirm').style.display = "none";
                mostrarModal("Éxito", "La comuna ha sido agregada correctamente.", "cierre");
            }).fail(function() {
                alert("error");
            });
        }
        return
    }

    async function agregaRutaVina() {
        try {

            // Cargar los datos de los archivos JSON
            const comunasData = await cargarArchivoJSON('ruta-vina.json');
            const islrData = await cargarArchivoJSON('../islr.json');

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
                let factor = 0.87; // Valor predeterminado si no se encuentra el año actual
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
        let campo = document.getElementsByClassName(campos);
        if (checkbox.id == 'checkEditaBruto') {
            if (checkbox.checked) {
                document.getElementById('checkEditaLiquido').disabled = true
            } else {
                document.getElementById('checkEditaLiquido').disabled = false
            }
        } else {
            if (checkbox.checked) {
                document.getElementById('checkEditaBruto').disabled = true
            } else {
                document.getElementById('checkEditaBruto').disabled = false
            }
        }

        for (let i = 0; i < campo.length; i++) {
            const element = campo[i];
            if (checkbox.checked) {
                element.disabled = false;
                element.classList.remove('bg-light', 'border-0');
                campo[0].focus();
            } else {
                obtenerruta();
            }
        }
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

    $('#myModal').on('shown.bs.modal', function() {
        $('#myInput').trigger('focus')
    })
    $(function() {
        $('[data-toggle="popover"]').popover()
    })
</script>