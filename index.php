<?php
session_start();
$sesion = isset($_SESSION['usuario']);
require __DIR__ . '/config.php';
$indice = "inicio";
if($sesion == null || $sesion == ""){
  header($_ENV['URL_LOCAL']);
}
include "partials/header.php";

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

<header class="row mx-1">
  <div class="col-sm-6">
    <div class="pb-2">
      <h5>Fecha</h5>
      <div class="input-group date" id="datepicker">
        <input type="text" class="form-control">
        <span class="input-group-append">
          <span class="input-group-text bg-white">
            <i class="fa fa-calendar" ></i>
          </span>
        </span>
      </div>
    </div>
    <table class="table table-striped table-bordered table-sm">
      <thead class="table-danger text-center">
        <td colspan="2">Mes</td>
        <td colspan="2">Semana</td>
        <td colspan="2">Dia</td>
      </thead>
      <thead class="table-secondary text-center">
        <td>Monto</td>
        <td>Viajes</td>
        <td>Monto</td>
        <td>Viajes</td>
        <td>Monto</td>
        <td>Viajes</td>
      </thead>
      <tbody id="detallesViajes"></tbody>
    </table>
  </div>
  <div  class="col-sm-6" style="text-align:center"> 
      <h5>Rutas</h5>
      <div id="rutas">
      </div>
  </div>
</header>
    <section>
    <h5 class="mx-3">Ultimos 10 viajes</h5>
      <table  class="table table-striped" style="width:97%;margin: 0 auto">
        <thead class="table-danger text-center">
          <td>Destino</td>
          <td>Fecha</td>
          <td>Monto</td>
          <td style="width:10%">Eliminar</td>
        </thead>
        <tbody id="tablaUltimosViajes" class="text-center"></tbody>
      </table>
    </section>
  </div>
</body>
<?php include "partials/boostrap_script.php" ?>
<script>
window.onload = function () {
  detallesViajes();
  cargaBotonesRutas();
  obtenerUltimosViajes();
};

const date = new Date();
let day = date.getDate();
let month = date.getMonth();
let year = date.getFullYear();
$("#datepicker").datepicker({
  format: "dd/mm/yyyy"
});
$("#datepicker").datepicker("setEndDate", new Date(year, month, day));
$("#datepicker").datepicker("update", new Date(year, month, day));

function cargaBotonesRutas(){
  $.post("conexiones.php", { 
    ingresar: "cargarutas" 
  }).done(function(datos) {
     rutas.innerHTML = datos;
  });
}

function agregaRuta(e){
    var hora = new Date();
    var dia = $("#datepicker").datepicker("getFormattedDate");
    $.post("conexiones.php", {
      ingresar: "insertar",
      destino: e.value,
      dia: dia,
      hora: hora.toLocaleTimeString(),
    }).done(function(data,error){
      obtenerUltimosViajes();
      detallesViajes();
    }).fail(function() {
      alert( "error" );
    });
}

function obtenerUltimosViajes() {
  $.post("conexiones.php", { 
    ingresar: "obtener" 
  }).done(function(datos) {
    tablaUltimosViajes.innerHTML = datos;
  });
}

function detallesViajes() {
  $.post("conexiones.php", { 
      ingresar: "totalmes" 
    }).done (function (datos) {
      detallesViajes.innerHTML = datos;
  });
}

function eliminaViaje(id) {
  $.post("conexiones.php", { 
      ingresar: "eliminar", 
      id_viaje: id 
    }).done (function (datos) {
      obtenerUltimosViajes();
      detallesViajes();
  });
}
  </script>
</html>
