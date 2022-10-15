<?php
session_start();
$sesion = isset($_SESSION['usuario']);
require __DIR__ . '/config.php';

if($sesion == null || $sesion == ""){
  header($_ENV['URL_LOCAL']);
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registro de Viajes Boosmap</title>
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
      integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.standalone.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/styles.css">
  </head>
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
  <div class="container" style="max-width:850px">
  <nav class="navbar navbar-dark bg-danger">
    <div class="container-fluid  d-flex  justify-content-between">
      <p class="text-light my-auto">Hola, <?= $_SESSION['usuario'] ?></p>
      <a class="navbar-brand" style="font-size:12px"href="<?= $_ENV['URL_SESSION'] ?>">Cerrar Sesión</a>
     </div>

  </nav>
    <div>
      <h4>Datos del Mes</h4>
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
    <header class="row">
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
        <button type="submit" value="vina" class="btn btn-danger ">Viña del Mar</button>
        <button type="submit" value="renaca" class="btn btn-danger">Reñaca</button>
        <button type="submit" value="concon" class="btn btn-danger ">Concon</button>
        <button type="submit" value="quilpue" class="btn btn-danger ">Quilpué</button>
        <button type="submit" value="v_alemana" class="btn btn-danger ">V. Alemana</button>
        <button type="submit" value="valpo" class="btn btn-danger">Valparaiso</button>
      </div>
    </header>
    
<section>
  <h4>Viajes Realizados</h4>
  <table  class="table table-striped">
<thead class="table-danger">
  <td>Destino</td>
  <td>Fecha</td>
  <td>Monto</td>
  <td style="width:10%">Eliminar</td>
</thead>
<tbody id="tabla"></tbody>
  </table>
</section>
</div>
  </body>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js" 
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" 
    crossorigin="anonymous">
    </script>
  <script
    src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
    integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN"
    crossorigin="anonymous"
  ></script>
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
    integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+"
    crossorigin="anonymous"
  ></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
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

// $("#datepicker").on("changeDate", function (e) {
//   let fechaseleccionada = new Date(e.date).toLocaleDateString();
//   $("#my_hidden_input").val($("#datepicker").datepicker("getFormattedDate"));
//   console.log(fechaseleccionada);
// });

var botones = document.querySelectorAll("button");
var tabla = document.getElementById("tabla");
var numerodeviajes = document.getElementById("numerodeviajes");
var totalmes = document.getElementById("monto");
var montos = {
  vina: 3400,
  quilpue: 3800,
  valpo: 3800,
  v_alemana: 4100,
  concon: 4100,
  renaca: 3800,
};
window.onload = function () {
  obtener();
  conteo();
  total();
};
for (let i = 0; i < botones.length; i++) {
  botones[i].addEventListener("click", function (e) {
    var hora = new Date();
    var dia = $("#datepicker").datepicker("getFormattedDate");
  
    $.post("conexiones.php", {
      ingresar: "insertar",
      destino: e.target.innerText,
      dia: dia,
      hora: hora.toLocaleTimeString(),
      monto: montos[e.target.value],
    }).done(function(){
      obtener();
      conteo();
      total();
    }).fail(function() {
      alert( "error" );
    });

    
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

  </script>

</html>
