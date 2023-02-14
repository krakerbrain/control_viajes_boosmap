<?php
session_start();
$sesion = isset($_SESSION['usuario']);
require __DIR__ . './config.php';
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

<header class="row m-1">
  <div class="col-sm-7">
  <table class="table table-striped table-bordered table-sm">
                                            <thead class="text-center">
                                              <td colspan="3">
                                                <button class="btn btn-outline-danger btn_periodo" onclick="detallesViajes('semana',this)">Semana</button>
                                                <button class="btn btn-danger btn_periodo mx-4" onclick="detallesViajes('hoy',this)">Hoy</button>
                                                <button class="btn btn-outline-danger btn_periodo" onclick="detallesViajes('mes',this)">Mes</button>
                                              </td>
                                            </thead>
                                            <thead class="table-secondary text-center">
                                              <td>Monto Bruto</td>
                                              <td>Monto Líquido</td>
                                              <td>Viajes</td>
                                            </thead>
                                            <tbody  id="detalles_viajes"></tbody>
                                          </table>
  </div>


  <div  class="col-sm-5" style="text-align:center"> 
      <div class="input-group date mb-2" id="datepicker">
        <input type="text" class="form-control">
        <span class="input-group-append">
          <span class="input-group-text bg-white">
            <i class="fa fa-calendar" ></i>
          </span>
        </span>
      </div>
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

const date = new Date();
let day = date.getDate();
let month = date.getMonth();
let year = date.getFullYear();
$("#datepicker").datepicker({
  format: "dd/mm/yyyy"
});
$("#datepicker").datepicker("setEndDate", new Date(year, month, day));
$("#datepicker").datepicker("update", new Date(year, month, day));

window.onload = function () {
  cargaBotonesRutas();
  detallesViajes('hoy', 'inicial');
  obtenerUltimosViajes();
};

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
      detallesViajes('hoy', 'inicial');
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

function detallesViajes(periodo, elemento) {
  let btn_periodo = document.getElementsByClassName("btn_periodo")
  for (let i = 0; i < btn_periodo.length; i++) {
    const element = btn_periodo[i];
    if(elemento != 'inicial'){
      element.classList.remove("btn-danger")
      element.classList.add("btn-outline-danger");
    }
  }
  if(elemento && elemento != 'inicial'){
    elemento.classList.remove("btn-outline-danger");
    elemento.classList.add("btn-danger");
  }
  $.post("conexiones.php", { 
      ingresar: "totalmes",
      periodo: periodo 
    }).done (function (data) {
      let datos = JSON.parse(data);
          datos.forEach(element => {
            detalles_viajes.innerHTML = `
                                            <td class="text-center">$${Math.round(element.monto_total/0.870)}</td>
                                            <td class="text-center">$${element.monto_total == null ? 0 : element.monto_total}</td>
                                            <td class="text-center">${element.viajes}</td>
                                        `
          })
      // detallesViajesTabla.innerHTML = datos;
  });
}
function eliminaViaje(id) {
  $.post("conexiones.php", { 
      ingresar: "eliminar", 
      id_viaje: id 
    }).done (function (datos) {
      obtenerUltimosViajes();
      detallesViajes('hoy', 'inicial');
  });
}

function cargaBotonesRutas(){
  $.post("conexiones.php", { 
    ingresar: "cargarutas" 
  }).done(function(datos) {
     rutas.innerHTML = datos;
  });
}

  </script>

</html>
