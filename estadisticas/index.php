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
    <div class="mx-3">
        <h6 class="my-2">Seleccione un mes para ver sus estadísticas</h6>
        <form action="conexiones_estadisticas.php" method="post" class="mx-auto">
            <select name="selectMes" id="selectMes"></select>
        </form>
        <table id="estadisticas">
        </table>
        <table class="table table-striped mt-3 table-sm">
            <thead class="table-danger text-center">
                <td class='p1' style="cursor:pointer" title="Puede ordenar por destino" onclick="viajesporruta(true,'destino')">Destino <i id="iconoOrdenDestino" class="fas fa-sort"></i></td>
                <td class='p1' style="cursor:pointer" title="Puede ordenar por cantidad de viajes" onclick="viajesporruta(true,'conteo')">Viajes x Mes <i id="iconoOrdenViajesMes" class="fas fa-sort"></i></td>
            </thead>
            <tbody id="conteoviajes" class="text-center">

            </tbody>
        </table>
        <input type="hidden" name="ordenconteo" id="ordenconteo" value="">
    </div>
    <div>
        <?php include __DIR__."/../filtros/index.php"; ?>
    </div>    
</div>
</body>
<?php include __DIR__."/../partials/boostrap_script.php" ?>
<script type="text/javascript" src="../componente/js/manejo-de-fieldsets.js"></script>
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

datosMes(selectMes.value)

selectMes.addEventListener("change", function (e) {
    datosMes(e.target.form.selectMes.value)
})

window.onload = function () {
    fechaActual()
    llenarSelect()
    getRutas()
}
function fechaActual(){
    var fecha = "";
    var fechaActual = new Date();
    mes = String(fechaActual.getMonth() + 1).padStart(2, '0');
    anio = fechaActual.getFullYear();
    fecha = mes + '-' + anio;
    return fecha;
}
function llenarSelect(){
    $.post("conexiones_estadisticas.php", {
        ingresar: "select_mes"
    }).done(function (datos) {
        var selectMes = $("#selectMes");
        var jsonDatos = JSON.parse(datos);

        for (var i = 0; i < jsonDatos.length; i++) {
            var mes = meses[jsonDatos[i].mes - 1] // Obtiene el nombre del mes en español
            var anio = jsonDatos[i].anio;
            var option = $("<option>").val(jsonDatos[i].mes + '-' + jsonDatos[i].anio).text(mes + ' ' + anio);

            if ((String(jsonDatos[i].mes).padStart(2,'0')) + '-' + jsonDatos[i].anio === fechaActual()) {
                option.attr('selected', 'selected');
            }
            selectMes.append(option);
        }
    }).fail(function () {
        alert('error');
    });
}


function datosMes(fecha){
    if(fecha == ""){
        fecha = fechaActual()
    }
    $.post("conexiones_estadisticas.php", {
        ingresar: "pedidostotales",
        mes: fecha
    }).done(function(datos){
        totalviajes(datos)
        viajesporruta(false,'destino',fecha)
    }).fail(function() {
        alert( "error" );
    });
}

function totalviajes(datos){
    var tablaestadisticas = document.getElementById('estadisticas')
    tablaestadisticas.innerHTML = datos
}

function viajesporruta(ordenColumna, tipoorden, fecha){
    fecha = fecha == undefined ? selectMes.value : fecha;
    var mesAnio = fecha;
    var partes = mesAnio.split('-'); // Divide el string en base al guión ("-")
    var mes = partes[0];
    
    var mes = mes == undefined ? document.getElementById('selectMes').value : mes;
    var conteoviajes = document.getElementById('conteoviajes')
    var ascdesc = !ordenColumna  ? "" : document.getElementById('ordenconteo').value;
    let orden = "";
    if(ascdesc != ""){
        orden = `${tipoorden} ${ascdesc}`;
    }
    document.getElementById('ordenconteo').value = ascdesc;
    conteoviajes.innerHTML = ""
    $.post("conexiones_estadisticas.php", {
        ingresar: "viajesxruta",
        mes: mes,
        tipoorden: orden
    }).done(function(datos){
        // console.log(tipoorden)
        conteoviajes.innerHTML = datos
        var ascdesc = document.getElementById('ordenconteo').value;
        cambiaIconoOrden(tipoorden, ascdesc)

    }).fail(function() {
        alert( "error" );
    });
}

function cambiaIconoOrden(tipoorden, ascdesc){
    let idIcono = tipoorden == 'destino' ? 'iconoOrdenDestino' : 'iconoOrdenViajesMes';
    let icono = document.getElementById(idIcono);

    // Resetear iconos a su valor inicial "fas fa-sort"
    document.getElementById('iconoOrdenDestino').className = "fas fa-sort";
    document.getElementById('iconoOrdenViajesMes').className = "fas fa-sort";

    if(ascdesc == "asc"){
        ascdesc = "desc";
        icono.classList.remove('fa-sort');
        icono.classList.add('fa-sort-down');
    } else if(ascdesc == "desc"){
        ascdesc = "";
        icono.classList.remove('fa-sort');
        icono.classList.add('fa-sort-up');
    } else {
        ascdesc = "asc";
        icono.classList.remove('fa-sort');
        icono.classList.add('fa-sort');
    }
    document.getElementById('ordenconteo').value = ascdesc;
}




</script>
</html>


