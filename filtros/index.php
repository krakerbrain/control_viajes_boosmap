
<fieldset>
    <legend style="font-size:1em;cursor:pointer" class="align-items-center bg-danger d-flex justify-content-between p-1 text-light" onclick="ocultaFieldset('filtro')">
        <span>Filtros</span>
        <div class="filtro">
            <i class="bi bi-caret-down-square-fill"></i>
        </div>
    </legend>

    <div class="filtro container" style="display:none">
        <form action="conexiones_filtro.php" method="post" id="formFiltro" class="d-flex justify-content-center row">
            <div class="d-flex flex-column col-sm-3 mt-2">
                <label for="ruta">Ruta</label>
                <select name="ruta" id="ruta" class="form-control">
                    <option id=""></option>
                </select>
            </div>
            <div class="d-flex flex-column col-sm-3 mt-2">
                <label for="desde">Desde</label>
                <input type="date" name="desde" id="desde" class="form-control">
            </div>
            <div class="d-flex flex-column col-sm-3 my-2">
                <label for="hasta">Hasta</label>
                <input type="date" name="hasta" id="hasta" class="form-control">
            </div>
            <div class="d-flex mt-auto">
                <input class="btn btn-danger mt-auto" type="submit" value="Buscar" style="margin-bottom: 9px;width:85px;margin-right: 10px;">
                <input class="btn btn-outline-danger mt-auto" type="button" value="Limpiar" style="margin-bottom: 9px;width:85px" onclick="limpiarFormulario()">
            </div>
        </form>
        <div id="mensajeResultados" class="d-flex flex-wrap justify-content-around"style="margin-left: 13px;margin-bottom: 10px;">
            <!-- <span>Se encontraron</span> -->
        </div>
        <section style="max-height: 400px; overflow-y: auto;">
        <table  class="table table-striped" style="width:97%;margin: 0 auto; table-layout:fixed;font-size:small">
            <thead class="table-danger text-center" style="position: sticky; top:-1px; z-index: 1;">
                <td style="width:23%">Destino</td>
                <td style="width:20%">Fecha</td>
                <td style="width:13%">Monto</td>
            </thead>
            <tbody id="tablaFiltros" class="text-center"></tbody>
        </table>
        </section>
    </div>
</fieldset>

<script>

    function limpiarFormulario(){
        document.getElementById('formFiltro').reset();
        tablaFiltros.innerHTML = "";
        mensajeResultados.innerHTML = ""; 
    }

    function getRutas(){
        // debugger
        $.post("../filtros/conexiones_filtro.php", { 
        ingresar: "getRutas" 
        }).done(function(datos) {
            var jsonDatos = JSON.parse(datos);
                // ruta.innerHTML = `<option id=""></option>`
            jsonDatos.forEach(element => {
                ruta.innerHTML += `<option id="${element.idruta}">${element.ruta}</option>`
            });
        });
    }
document.getElementById("formFiltro").addEventListener("submit", function(event) {
  event.preventDefault(); // Esto evita que el formulario se envíe automáticamente

  // Aquí puedes realizar cualquier validación o procesamiento de los datos del formulario

  // Obtén los datos del formulario
  var ruta = document.getElementById("ruta").value;
  var desde = document.getElementById("desde").value;
  var hasta = document.getElementById("hasta").value;

  // Realiza la petición POST a conexiones.php con los datos del formulario
  $.post("../filtros/conexiones_filtro.php", { 
    ingresar: "getViajes",
    ruta: ruta,
    desde: desde,
    hasta: hasta,
    orden: "ORDER BY idviaje ASC"
  }).done(function(datos) {
        let jsonResultados = JSON.parse(datos);
        let jsonDatos = JSON.parse(jsonResultados.resultados);
        let jsonFilas = JSON.parse(jsonResultados.filas);
        mensajeResultados.innerHTML = ""
        tablaFiltros.innerHTML = ""; 
        let sumaMonto = jsonDatos.reduce((total, element) => total + parseFloat(element.monto), 0);

    jsonDatos.forEach(element => {
        tablaFiltros.innerHTML += `<tr id="${element.idviaje}">
      <td nowrap>${element.destino}</td>
      <td nowrap>${element.fecha}</td>
      <td class='text-center'>${element.monto.toLocaleString('es-CL', {style: 'currency', currency: 'CLP'})}</td>
      </tr>`
    })
    mensajeResultados.innerHTML = `<span>Se obtuvieron ${jsonFilas} resultados</span>
                                    <span>Total(liquido): ${sumaMonto.toLocaleString('es-CL', {style: 'currency', currency: 'CLP'})}</span>`;
    // Aquí puedes manejar la respuesta del servidor, si es necesario
  }).fail(function(error){
        console.log(error)
    });
});




</script>

