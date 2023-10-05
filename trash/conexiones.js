var meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
var selectMes = document.getElementById("selectMes");

datosMes(selectMes.value);

selectMes.addEventListener("change", function (e) {
  datosMes(e.target.form.selectMes.value);
});

window.onload = function () {
  fechaActual();
  llenarSelect();
  getRutas();
};
function fechaActual() {
  var fecha = "";
  var fechaActual = new Date();
  mes = String(fechaActual.getMonth() + 1).padStart(2, "0");
  anio = fechaActual.getFullYear();
  fecha = mes + "-" + anio;
  return fecha;
}
function llenarSelect() {
  $.post("conexiones_estadisticas.php", {
    ingresar: "select_mes",
  })
    .done(function (datos) {
      var selectMes = $("#selectMes");
      var jsonDatos = JSON.parse(datos);
      let sinOpciones = false;

      for (var i = 0; i < jsonDatos.length; i++) {
        var mes = meses[jsonDatos[i].mes - 1]; // Obtiene el nombre del mes en español
        var anio = jsonDatos[i].anio;
        var option = $("<option>")
          .val(jsonDatos[i].mes + "-" + jsonDatos[i].anio)
          .text(mes + " " + anio);

        if (String(jsonDatos[i].mes).padStart(2, "0") + "-" + jsonDatos[i].anio === fechaActual()) {
          option.attr("selected", "selected");
          sinOpciones = true;
        }
        selectMes.append(option);
      }
      if (!sinOpciones) {
        var fechaActualString = fechaActual();
        var mesActual = parseInt(fechaActualString.split("-")[0]); // Obtiene el mes actual a partir del string
        var anioActual = parseInt(fechaActualString.split("-")[1]);
        var option = $("<option>")
          .val(mesActual + "-" + anioActual)
          .text(meses[mesActual - 1] + " " + anio);
        option.attr("selected", "selected");
        selectMes.append(option);
      }
    })
    .fail(function () {
      alert("error");
    });
}

function datosMes(fecha) {
  if (fecha == "") {
    fecha = fechaActual();
  }
  $.post("conexiones_estadisticas.php", {
    ingresar: "pedidostotales",
    mes: fecha,
  })
    .done(function (datos) {
      totalviajes(datos);
      viajesporruta(false, "destino", fecha);
    })
    .fail(function () {
      alert("error");
    });
}

function totalviajes(datos) {
  var tablaestadisticas = document.getElementById("estadisticas");
  tablaestadisticas.innerHTML = datos;
}

function viajesporruta(ordenColumna, tipoorden, fecha) {
  fecha = fecha == undefined ? selectMes.value : fecha;
  var mesAnio = fecha;
  var partes = mesAnio.split("-"); // Divide el string en base al guión ("-")
  var mes = partes[0];

  var mes = mes == undefined ? document.getElementById("selectMes").value : mes;
  var conteoviajes = document.getElementById("conteoviajes");
  var ascdesc = !ordenColumna ? "" : document.getElementById("ordenconteo").value;
  let orden = "";
  if (ascdesc != "") {
    orden = `${tipoorden} ${ascdesc}`;
  }
  document.getElementById("ordenconteo").value = ascdesc;
  conteoviajes.innerHTML = "";
  $.post("conexiones_estadisticas.php", {
    ingresar: "viajesxruta",
    mes: mes,
    tipoorden: orden,
  })
    .done(function (datos) {
      conteoviajes.innerHTML = datos;
      var ascdesc = document.getElementById("ordenconteo").value;
      cambiaIconoOrden(tipoorden, ascdesc);
    })
    .fail(function () {
      alert("error");
    });
}

function cambiaIconoOrden(tipoorden, ascdesc) {
  let idIcono = tipoorden == "destino" ? "iconoOrdenDestino" : "iconoOrdenViajesMes";
  let icono = document.getElementById(idIcono);

  // Resetear iconos a su valor inicial "fas fa-sort"
  document.getElementById("iconoOrdenDestino").className = "fas fa-sort";
  document.getElementById("iconoOrdenViajesMes").className = "fas fa-sort";

  if (ascdesc == "asc") {
    ascdesc = "desc";
    icono.classList.remove("fa-sort");
    icono.classList.add("fa-sort-up");
  } else if (ascdesc == "desc") {
    ascdesc = "";
    icono.classList.remove("fa-sort");
    icono.classList.add("fa-sort-down");
  } else {
    ascdesc = "asc";
    icono.classList.remove("fa-sort");
    icono.classList.add("fa-sort");
  }
  document.getElementById("ordenconteo").value = ascdesc;
}
