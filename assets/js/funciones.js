const formatoMoneda = (moneda) =>
  Math.round(moneda).toLocaleString("es-CL", {
    style: "currency",
    currency: "CLP",
  });

async function cargarArchivoJSON(url) {
  try {
    const response = await fetch(url);
    const data = await response.json();
    return data;
  } catch (error) {
    console.error("Error al cargar el archivo JSON:", error);
    return null;
  }
}

async function calculaFactorIslr(fecha) {
  try {
    const islrData = await cargarArchivoJSON(`${baseUrl}islr.json?v=2028`);
    const anioCalculado = calculaAnio(fecha);
    // Buscar el factor correspondiente al año actual en el archivo islr.json
    let factor;
    let impuesto;

    for (const islr of islrData) {
      if (islr.anio === anioCalculado) {
        factor = islr.factor;
        impuesto = islr.impuesto;
        break;
      }
    }

    return {
      factor,
      impuesto,
    };
  } catch (error) {
    console.error("Error al leer el archivo JSON:", error);
    return null;
  }
}

function calculaAnio(fecha) {
  let anioActual;
  let mesActual;

  if (fecha == undefined) {
    const currentDate = new Date();
    anioActual = currentDate.getFullYear();
    mesActual = currentDate.getMonth() + 1;
  } else {
    let [mes, anio] = fecha.split("-");
    mesActual = parseInt(mes);
    anioActual = parseInt(anio);
  }

  const anioParaCalculo = mesActual === 12 ? anioActual + 1 : anioActual;
  return anioParaCalculo;
}
