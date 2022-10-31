<?php
session_start();
$sesion = isset($_SESSION['usuario']);

require __DIR__ . '/../config.php';
include __DIR__."/../partials/header.php"; 
$indice = "rutas";
?>

<body>
    <div class="container px-0" style="max-width:850px">
        <?php include __DIR__."/../partials/navbar.php"; ?>
       <div class="row-cols-lg-2 m-3">
        <form action="conexiones_rutas.php" method="post" class="mx-auto">
            <h4>Configuración de Rutas</h4>
            <div>
                <label class="form-label" for="region">Región</label>
            <select class="custom-select" name="region" id="region">
                <option value="">Seleccione</option>
            </select>
            
        </div>
        <div>
            <label class="form-label" for="comunas">Comuna</label>
            <select class="custom-select" name="comunas" id="comunas">
                <option value="">Seleccion</option>
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

btnAgregar.addEventListener("click", function (e) {
    var comunaseleccionada = e.target.form.comunas.value;
    var costoruta = e.target.form.costoruta.value;
    if(comunaseleccionada != "" && costoruta != ""){
        var conf = `¿Desea ingrear la comuna de ${comunaseleccionada} con un monto por viaje de $${costoruta}`;
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
        alert("Debe llenar todos los campos.")
    }
});

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
</script>