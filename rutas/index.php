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
        <div class="row-cols-lg-2 m-3">
        <form action="conexiones_rutas.php" method="post" class="mx-auto">
        <button type="submit" disabled hidden aria-hidden="true"></button>
            <h4>Configuración de Rutas</h4>
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
                <label for="costoruta">Monto Líquido</label>
                <input class="form-control" type="text" id="costoruta">
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
</body>
    <?php include __DIR__."/../partials/boostrap_script.php" ?>
</html>

<script>

    
const seleccionaRegion = document.getElementById("region");
const btnAgregar = document.getElementById('agregar');

window.onload = function(){
    $.get("conexiones_rutas.php", {ingresar: "cargaregiones"})
    .done(function(datos){
        region.innerHTML += datos;
        obtenerruta();
    })
    .fail(function() {alert( "error" )});
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
    if(comunaseleccionada != "" && comunaseleccionada != "Seleccione" && costoruta != ""){
        var conf = `¿Desea ingrear la comuna de ${comunaseleccionada} con un monto por viaje de $${costoruta}`;
            if(confirm(conf) == true){
                $.post("conexiones_rutas.php", {
                    ingresar: "agregaviaje",
                    comuna:  comunaseleccionada,
                    costoruta: costoruta,
                }).done(function(datos){
                obtenerruta();
                debugger
            }).fail(function() {
                alert( "error" );
            });
        }else{
            return
        }
    }else{
        alert("Debe llenar todos los campos.")
    }
}

function obtenerruta(){
    $.post("conexiones_rutas.php", {
      ingresar: "obtenerRutas"
    }).done(function(datos){
        tablarutas.innerHTML = datos;
    }).fail(function() {
      alert( "error" );
    });
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

</script>