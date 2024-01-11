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

async function calculaFactorIslr() {
  try {
    const islrData = await cargarArchivoJSON("../islr.json");

    // Obtener el año actual y el mes actual
    const currentDate = new Date();
    const anioActual = currentDate.getFullYear();
    const mesActual = currentDate.getMonth() + 1;
    const anioParaCalculo = mesActual === 12 ? anioActual + 1 : anioActual;

    // Buscar el factor correspondiente al año actual en el archivo islr.json
    let factor;
    let impuesto;

    for (const islr of islrData) {
      if (islr.anio === anioParaCalculo) {
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
