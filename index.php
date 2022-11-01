<?php
session_start();
$sesion = isset($_SESSION['usuario']);
require __DIR__ . '/config.php';
$indice = "inicio";
if($sesion == null || $sesion == ""){
  header($_ENV['URL_LOCAL']);
}
include "partials/header.php";

$mes = date("m");
$meses = [
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
$mesactual = $meses[$mes-1];

?>

  <style>
    .btn{
      font-size:12px;
      width:100px;
      margin-bottom: 3px;
    }
    .datepicker{
      left:540px
    }
  </style>
<body>
  <div class="container px-0" style="max-width:850px">
    <?php include "partials/navbar.php" ?>
    <div class="ml-3">
      <h4>Datos del Mes de <?= $mesactual ?></h4>
      <table>
        <tr>
          <td>Monto líquido del mes: </td>
          <td id="monto"></td>
        </tr>
        <tr>
          <td>Viajes realizados: </td>
          <td id="numerodeviajes"></td>
        </tr>
      </table>
    </div>
    <header class="row ml-0">
    <div class="col-6">
      <h4>Fecha</h4>
      <div class="input-group date" id="datepicker">
        <input type="text" class="form-control">
        <span class="input-group-append">
          <span class="input-group-text bg-white">
            <i class="fa fa-calendar" ></i>
          </span>
        </span>
      </div>
    </div>
    <div  class="col-6" style="text-align:center"> 
      <h4>Rutas</h4>
      <div id="rutas">
      </div>
    </div>
    </header>
    <section class="mx-3">
    <h4>Viajes Realizados</h4>
      <table  class="table table-striped">
        <thead class="table-danger text-center">
          <td>Destino</td>
          <td>Fecha</td>
          <td>Monto</td>
          <td style="width:10%">Eliminar</td>
        </thead>
        <tbody id="tabla" class="text-center"></tbody>
      </table>
    </section>
  </div>
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

var tabla = document.getElementById("tabla");
var numerodeviajes = document.getElementById("numerodeviajes");
var totalmes = document.getElementById("monto");

window.onload = function () {
  obtener();
  conteo();
  total();
  cargarutas();
};

function insertaRuta(e){
    var hora = new Date();
    var dia = $("#datepicker").datepicker("getFormattedDate");
    $.post("conexiones.php", {
      ingresar: "insertar",
      destino: e.value,
      dia: dia,
      hora: hora.toLocaleTimeString(),
    }).done(function(data,error){
      obtener();
      conteo();
      total();
    }).fail(function() {
      alert( "error" );
    });
}

function obtener() {
  $.post("conexiones.php", { 
    ingresar: "obtener" 
  }).done(function(datos) {
     tabla.innerHTML = datos;
  });
}

function conteo() {
  $.post("conexiones.php", { 
      ingresar: "conteo" 
    }).done(function (datos) {
    numerodeviajes.innerHTML = datos;
  });
}
function total() {
  $.post("conexiones.php", { 
      ingresar: "totalmes" 
    }).done (function (datos) {
    totalmes.innerHTML = " $"+datos;
  });
}

function eliminaviaje(id) {
  $.post("conexiones.php", { 
      ingresar: "eliminar", 
      id_viaje: id 
    }).done (function (datos) {
 
      obtener();
      conteo();
      total();
  
  });
}

function cargarutas(){
  $.post("conexiones.php", { 
    ingresar: "cargarutas" 
  }).done(function(datos) {
     rutas.innerHTML = datos;
  });
}

  </script>

</html>
