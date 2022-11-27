<?php
session_start();
$sesion = isset($_SESSION['usuario']);

require __DIR__ . '/../config.php';
include __DIR__."/../partials/header.php"; 
$indice = "estadisticas";

?>
<body>
<div class="container px-0" style="max-width:850px">
<?php include __DIR__."/../partials/navbar.php"; ?>
    <h4>Seleccione el mes del cual desea ver sus estadísticas mensuales</h4>
    <form action="conexiones_estadisticas.php" method="post" class="mx-auto">
        <select name="selectMes" id="selectMes"></select>
    </form>
    <table id="estadisticas">
    </table>
    <table class="table table-striped">
        <thead class="table-danger text-center">
            <td class='p1' style="cursor:pointer" title="Puede ordenar por destino" onclick="viajesporruta('destino')">Destino</td>
            <td class='p1' style="cursor:pointer" title="Puede ordenar por cantidad de viajes" onclick="viajesporruta('conteo')">Viajes x Mes</td>
        </thead>
        <tbody id="conteoviajes" class="text-center">
           
        </tbody>
    </table>
    <input type="hidden" name="ordenconteo" id="ordenconteo" value="">
</div>
</body>
<?php include __DIR__."/../partials/boostrap_script.php" ?>
<script>
   var meses = [
  'Enero',
  'Febrero',
  'Marzo',
  'Abril',
  'Mayo',
  'Junio',
  'Julio',
  'Agosto',
  'Septiembre',
  'Octubre',
  'Noviembre',
  'Diciembre'
];
var selectMes = document.getElementById('selectMes')

for (let i = 0; i < meses.length; i++) {
    const element = meses[i];
    var mes = `<option value="${i+1}">${meses[i]}</option>`;
    selectMes.innerHTML += mes;
}

selectMes.addEventListener("change", function (e) {
    $.post("conexiones_estadisticas.php", {
        ingresar: "pedidostotales",
        mes: e.target.form.selectMes.value
    }).done(function(datos){
        totalviajes(datos)
        viajesporruta('destino',e.target.form.selectMes.value)
    }).fail(function() {
        alert( "error" );
    });
})

function totalviajes(datos){
    var tablaestadisticas = document.getElementById('estadisticas')
    tablaestadisticas.innerHTML = datos
}

function viajesporruta(tipoorden,mes){
    var mes = mes == undefined ? document.getElementById('selectMes').value : mes;
    var conteoviajes = document.getElementById('conteoviajes')
    var ascdesc = document.getElementById('ordenconteo').value;
    ascdesc = ascdesc == "" ?  "asc" : ascdesc;
    var orden = `${tipoorden} ${ascdesc}`;
    document.getElementById('ordenconteo').value = ascdesc;
    conteoviajes.innerHTML = ""


    $.post("conexiones_estadisticas.php", {
        ingresar: "viajesxruta",
        mes: mes,
        tipoorden: orden
    }).done(function(datos){
        conteoviajes.innerHTML = datos
        var ascdesc = document.getElementById('ordenconteo').value;
        ascdesc = ascdesc == "asc" ?  "desc" : "asc";
        document.getElementById('ordenconteo').value = ascdesc;
    }).fail(function() {
        alert( "error" );
    });
}

</script>
</html>


