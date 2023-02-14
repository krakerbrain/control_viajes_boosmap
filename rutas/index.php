<?php
session_start();
$sesion = isset($_SESSION['usuario']);
require __DIR__ . '/../config.php';
include __DIR__."/../partials/header.php"; 
$indice = "rutas";
$creado = isset($_REQUEST['creado']) ? $_REQUEST['creado'] : "";
?>

<body>
    <div class="container px-0" style="max-width:850px">
        <?php include __DIR__."/../partials/navbar.php"; ?>
        <?php if($creado != "") {?>
            <div class="alert alert-danger mt-4  alert-dismissible fade show" role="alert">
                Bienvenido al sistema. Debe ingresar las rutas para poder llevar el registro.
                Cuando lo desee puede ingresar aquí para modificar o agregar nuevas Rutas.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } ?>
        <fieldset style="margin-top:20px">
            <legend style="font-size:1em;cursor:pointer" class="align-items-center bg-danger d-flex justify-content-between p-1 text-light" onclick="ocultaFieldset('ocultaRutas')">
                <span>Configuración de Rutas</span>
                <div class="ocultaRutas">
                    <i class="bi bi-caret-down-square-fill"></i>
                </div>
            </legend>
            <div class="ocultaRutas" style="display:block">
                <div id="montosActualizados"class="alert alert-danger" role="alert" style="display:none">
                    Los montos de rutas han sido actualizados
                </div>
                <div class="row-cols-lg-2 m-3">
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
                            <input type="checkbox" class="form-check-input" id="nobruto">
                            <label class="form-check-label" for="nobruto">Seleccione si no conoce el monto bruto</label>
                        </div>
                        <div>
                            <label for="costoruta">Monto Líquido</label>
                            <input class="form-control" type="number" id="costoruta" placeholder = "0" disabled>
                        </div>
                        <div>
                            <input class="btn btn-danger w-100 my-4" type="button" value="Agregar" id="agregar">
                        </div>
                    </form>
                </div> 
                <table class="table table-striped">
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
                <table class="table table-striped w-75 mx-auto">
                    <thead class="table-danger text-center">
                        <td>Destino</td>
                        <td nowrap>Monto Bruto <input type="checkbox" id="checkEditaBruto" name="" onclick="editaMonto(this,'editaBruto')"></td>
                        <td nowrap>Monto Líquido <input type="checkbox" id="checkEditaLiquido" name="" onclick="editaMonto(this,'editaLiquido')"></td>
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
                <!-- Modal -->
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
                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="location.reload()">Cerrar</button>
            </div>
        </div>
        <!-- fin modal -->
    </div>
</body>
    <?php include __DIR__."/../partials/boostrap_script.php" ?>
</html>

<script>
function activaGuardar(e){
    let actualizaMes = document.getElementById('actualizaMes').checked;
    let actualizaActual = document.getElementById('actualizaActual').checked;
    if(e.checked || actualizaMes || actualizaActual){
        document.getElementById('guardar').disabled = false;
    }else{
        document.getElementById('guardar').disabled = true;
    }
}

function obtieneNuevosPrecios(){
    let inputConPrecios = document.querySelectorAll('.editaLiquido');
    let actualizaMes = document.getElementById('actualizaMes').checked;
    let actualizaActual = document.getElementById('actualizaActual').checked;
    let nuevosPrecios = new Array;
    for (let i = 0; i < inputConPrecios.length; i++) {
        const element = inputConPrecios[i];
        if(element.value != element.defaultValue){
            data = {
                id: element.id,
                precio: element.value
            }
            nuevosPrecios.push(data);
        }
    }
    actualizaPrecios(nuevosPrecios, actualizaMes, actualizaActual)
}

function actualizaPrecios(nuevosprecios,actualizaMes, actualizaActual){
    if(nuevosprecios != ""){
        $.post("conexiones_rutas.php", {
            ingresar: "actualizaPrecios",
            nuevosPrecios: JSON.stringify(nuevosprecios),
            actualizaMes:actualizaMes,
            actualizaActual:actualizaActual
        }).done(function(datos){
            sessionStorage.setItem('actualizado', true);
        }).fail(function() {
            alert( "error" );
        });
    }
}


function ocultaFieldset(elemento){  
    if(document.getElementsByClassName(elemento)[1].style.display == "block"){
        document.getElementsByClassName(elemento)[1].style.display = "none"
        document.getElementsByClassName(elemento)[0].firstElementChild.classList.remove("bi-caret-up-square-fill");
        document.getElementsByClassName(elemento)[0].firstElementChild.classList.add("bi-caret-down-square-fill");
    }else{
        document.getElementsByClassName(elemento)[1].style.display = "block"
        document.getElementsByClassName(elemento)[0].firstElementChild.classList.remove("bi-caret-down-square-fill");
        document.getElementsByClassName(elemento)[0].firstElementChild.classList.add("bi-caret-up-square-fill");
    }
}

function calculomonto(val,campoActualiza){
   let montobruto = val.value == "" ? 0 : val.value
   let monto;
   if(typeof(campoActualiza) == 'number' || campoActualiza == 'costoruta'){
       monto = parseInt(montobruto)-(parseInt(montobruto) * 0.13)
    }else{
       monto = parseInt(montobruto)+(parseInt(montobruto) /0.870)
    }
   document.getElementById(campoActualiza).value   =  Math.round(monto);
}

var nobruto = document.getElementById('nobruto');
nobruto.addEventListener('change', function(e){
    if(e.target.checked){
        document.getElementById('montobruto').disabled = "true"
        document.getElementById('costoruta').removeAttribute("disabled")
    }else{
        document.getElementById('costoruta').disabled = "true"
        document.getElementById('costoruta').value = 0
        document.getElementById('montobruto').removeAttribute("disabled")

    }
})

const seleccionaRegion = document.getElementById("region");
const btnAgregar = document.getElementById('agregar');

window.onload = function(){
    if(sessionStorage.getItem('actualizado')){
        activaAlerta()
    }
    $.get("conexiones_rutas.php", {ingresar: "cargaregiones"})
    .done(function(datos){
        region.innerHTML += datos;
        obtenerruta();
    })
    .fail(function() {alert( "error" )});
}

function activaAlerta(){
    document.getElementById('montosActualizados').style.display = "block";
    setTimeout(() => {
        document.getElementById('montosActualizados').style.display = "none"
        sessionStorage.removeItem('actualizado')
    }, 4000);
}

seleccionaRegion.addEventListener("change", function (e) {
    $.post("conexiones_rutas.php", {
        ingresar: "cargacomunas",
        region: e.target.form.region.value
    }).done(function(datos){
        comunas.innerHTML = datos;
    }).fail(function() {
        alert( "error" );
    });
})
document.querySelector('form').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        agregaRuta(e)
    }
});

btnAgregar.addEventListener("click", function (e) {
    agregaRuta(e)
});

function agregaRuta(e){
    var comunaseleccionada = e.target.form.comunas.selectedIndex == -1 ? 0 : e.target.form.comunas[e.target.form.comunas.selectedIndex].innerText;
    var costoruta = e.target.form.costoruta.value;
    var montobruto = document.getElementById('montobruto');
    if(comunaseleccionada != "" && comunaseleccionada != "Seleccione" && costoruta != "" && costoruta != 0){
        var conf = `¿Desea ingrear la comuna de ${comunaseleccionada} con un monto líquido por viaje de $${costoruta}`;
            if(confirm(conf) == true){
                $.post("conexiones_rutas.php", {
                    ingresar: "agregaviaje",
                    comuna:  comunaseleccionada,
                    costoruta: costoruta,
                }).done(function(datos){
                obtenerruta();
            }).fail(function() {
                alert( "error" );
            });
        }else{
            return
        }
    }else{
        if(costoruta == 0){
            alert("El monto no puede ser 0.")
        }else{
            alert("Debe llenar todos los campos.")
        }
    }
}

function obtenerruta(){
    $.post("conexiones_rutas.php", {
      ingresar: "obtenerRutas"
    }).done(function(datos){
        let data = JSON.parse(datos);
        let configuraRutas = "";
        let modificaMontos = "";

        data.map(function(clave){
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
                                            data-value='${Math.round(clave.costoruta/0.870)}' 
                                            value='${Math.round(clave.costoruta/0.870)}'
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
      alert( "error" );
    });
}

function editaMonto(checkbox, campos){
    let campo = document.getElementsByClassName(campos);
    if(checkbox.id == 'checkEditaBruto'){
        if(checkbox.checked){
            document.getElementById('checkEditaLiquido').disabled = true
        }else{
            document.getElementById('checkEditaLiquido').disabled = false
        }
    }else{
        if(checkbox.checked){
            document.getElementById('checkEditaBruto').disabled = true
        }else{
            document.getElementById('checkEditaBruto').disabled = false
        }
    }

    for (let i = 0; i < campo.length; i++) {
        const  element = campo[i];
        if(checkbox.checked){
            element.disabled = false;
            element.classList.remove('bg-light', 'border-0');
            campo[0].focus();
        }else{
            obtenerruta();
        }
    }
}


function eliminaRuta(ruta,id){
    var mensaje = `¿Está seguro de eliminar la ruta ${ruta}`;
    if(confirm(mensaje) == true){
        $.post("conexiones_rutas.php", {
            ingresar: "eliminaRuta",
            idruta: id
        }).done(function(datos){
            obtenerruta();
        }).fail(function() {
            alert( "error" );
        });
    }else{
        return
    }
}


function borraDatos(){
    document.getElementById('costoruta').value = "";
    document.getElementById('costoruta').focus();
}

var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})

$('#myModal').on('shown.bs.modal', function () {
  $('#myInput').trigger('focus')
})

</script>