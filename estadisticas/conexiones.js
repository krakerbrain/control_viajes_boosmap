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
  llenaGrafico();
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
      const jsonDatos = JSON.parse(datos);
      let selectMes = document.querySelector("#selectMes");
      let mesActual = fechaActual().split("-")[0];
      let anioActual = fechaActual().split("-")[1];
      let optionsHTML = "";

      if (jsonDatos.length == 0) {
        optionsHTML = `<option value="${mesActual}-${anioActual}" selected>${meses[mesActual - 1]} ${anioActual}</option>`;
      } else {
        jsonDatos.forEach(({ mes, anio }) => {
          let selected = mes == mesActual && anio == anioActual ? "selected" : "";
          optionsHTML += `<option value="${mes}-${anio}" ${selected}>${meses[mes - 1]} ${anio}</option>`;
        });
      }
      selectMes.innerHTML = optionsHTML;
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
      let data = JSON.parse(datos);
      totalviajes(data);
      viajesporruta(false, "destino", fecha);
    })
    .fail(function () {
      alert("error");
    });
}

async function totalviajes(datos) {
  let fechaEstadistica = document.getElementById("selectMes").value == "" ? fechaActual() : document.getElementById("selectMes").value;
  var tablaestadisticas = document.getElementById("estadisticas");
  const { factor } = await calculaFactorIslr(fechaEstadistica);
  datos.forEach((element) => {
    let montoBruto = element.montoLiquido / factor;
    let extrasBruto = element.totalExtras / factor;
    let totalBruto = parseInt(montoBruto) + parseInt(extrasBruto);
    let totalLiquido = parseInt(element.montoLiquido) + parseInt(element.totalExtras);
    let displayExtra = "";
    let colspan = "";
    if (element.conteoExtras == 0) {
      displayExtra = "d-none";
      colspan = "colspan=2";
    }
    let displayPeaje = element.conteoPeajes == 0 ? "d-none" : "";

    tablaestadisticas.innerHTML = `
                                  <tr>
                                    <td ${colspan}">VIAJES COMPLETADOS: ${element.totalviajes}</td>
                                    <td class="${displayExtra}">EXTRAS: ${element.conteoExtras}</td>
                                  </tr>
                                  <tr class="${displayExtra}">
                                    <td>VIAJES BRUTO: ${formatoMoneda(montoBruto)}</td>
                                    <td>VIAJES LIQUIDO: ${formatoMoneda(element.montoLiquido)}</td>
                                  </tr>
                                  <tr class="${displayExtra}">
                                    <td>EXTRAS BRUTO: ${formatoMoneda(extrasBruto)}</td>
                                    <td>EXTRAS LIQUIDO: ${formatoMoneda(element.totalExtras)}</td>
                                  </tr>
                                  <tr >
                                    <td>TOTAL BRUTO: ${formatoMoneda(totalBruto)}</td>
                                    <td>TOTAL LIQUIDO: ${formatoMoneda(totalLiquido)}</td>
                                  </tr>
                                  <tr class="${displayPeaje}">
                                    <td colspan="2">PEAJES (${element.conteoPeajes}): ${formatoMoneda(
      element.totalPeajes
    )} <span class="text-danger small">**Los peajes no afectan el total**</span></td>
                                  </tr>
                                  `;
  });
}

function viajesporruta(ordenColumna, tipoorden, fecha) {
  fecha = fecha == undefined ? selectMes.value : fecha;
  let mesAnio = fecha;
  let partes = mesAnio.split("-"); // Divide el string en base al guión ("-")
  let mes = partes[0];
  let annio = partes[1];
  mes = mes == undefined ? document.getElementById("selectMes").value : mes;
  let conteoviajes = document.getElementById("conteoviajes");
  let ascdesc = !ordenColumna ? "" : document.getElementById("ordenconteo").value;
  let orden = "";
  if (ascdesc != "") {
    orden = `${tipoorden} ${ascdesc}`;
  }
  document.getElementById("ordenconteo").value = ascdesc;
  conteoviajes.innerHTML = "";
  $.post("conexiones_estadisticas.php", {
    ingresar: "viajesxruta",
    mes: mes,
    annio: annio,
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

function llenaGrafico() {
  $.post("conexiones_estadisticas.php", {
    ingresar: "mesesConDatos",
  })
    .done(function (datos) {
      // Parsea los datos JSON obtenidos
      var datosJSON = JSON.parse(datos);

      // Extrae los valores de 'mes', 'viajes', y 'monto'
      var meses = datosJSON.map(function (item) {
        return item.mes;
      });

      var viajes = datosJSON.map(function (item) {
        return item.viajes;
      });

      var montos = datosJSON.map(function (item) {
        return item.monto;
      });

      // Crea el gráfico con los datos obtenidos
      const ctx = document.getElementById("myChart");

      var myChart = new Chart(ctx, {
        type: "bar",
        data: {
          labels: meses,
          datasets: [
            {
              label: "Ingresos",
              data: montos,
              yAxisID: "ingresos",
              fill: true,
              // ... other options
            },
            {
              label: "Viajes",
              data: viajes,
              yAxisID: "viajes",
              fill: true,
              // ... other options
            },
          ],
        },
        options: {
          scales: {
            ingresos: {
              position: "left",
              beginAtZero: true,
              title: {
                display: true,
                text: "Ingresos",
              },
              ticks: {
                callback: function (value, index, values) {
                  if (value >= 1000000) {
                    return (value / 1000000).toFixed(value % 1000000 === 0 ? 0 : 1) + "Millón";
                  } else {
                    return (value / 1000).toFixed(value % 1000 === 0 ? 0 : 1) + "mil";
                  }
                },
              },
            },
            viajes: {
              position: "right",
              beginAtZero: true,
              title: {
                display: true,
                text: "Viajes",
              },
            },
          },
          // ... other options
        },
      });
    })
    .fail(function () {
      alert("error");
    });
}
