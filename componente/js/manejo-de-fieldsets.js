/**
 * En el HTML donde se use esta funcion deberá crearse un fieldset con estas características:
 * <fieldset>
    <legend style="font-size:1em;cursor:pointer" class="align-items-center bg-danger d-flex justify-content-between p-1 text-light" onclick="ocultaFieldset('filtro')">
        <span>[Titulo a eleccion]</span>
        <div class="[clase a eleccion]">
            <i class="bi bi-caret-down-square-fill"></i>
        </div>
    </legend>
 * La class del div que contiene al icono debe ser igual al div que contendrá lo que se quiere ocultar al activar este fieldset, por ejemplo:

    <div class="[clase a eleccion]" style="display:none">
        <table  class="table table-striped" style="width:97%;margin: 0 auto; table-layout:fixed;font-size:small">
            <thead class="table-danger text-center" style="position: sticky; top:-1px; z-index: 1;">
                <td style="width:23%">Destino</td>
                <td style="width:20%">Fecha</td>
                <td style="width:13%">Monto</td>
            </thead>
            <tbody id="tablaFiltros" class="text-center"></tbody>
        </table>
    </div>

    y deberá tener el estilo display: none o block segun si quiere mostrarse o no
    Se debe incluir esta ruta en el archivo en el que se vaya a usar
    <script type="text/javascript" src="../componente/js/manejo-de-fieldsets.js"></script>
 */

function ocultaFieldset(elemento) {
  if (document.getElementsByClassName(elemento)[1].style.display == "block") {
    document.getElementsByClassName(elemento)[1].style.display = "none";
    document.getElementsByClassName(elemento)[0].firstElementChild.classList.remove("bi-caret-up-square-fill");
    document.getElementsByClassName(elemento)[0].firstElementChild.classList.add("bi-caret-down-square-fill");
  } else {
    document.getElementsByClassName(elemento)[1].style.display = "block";
    document.getElementsByClassName(elemento)[0].firstElementChild.classList.remove("bi-caret-down-square-fill");
    document.getElementsByClassName(elemento)[0].firstElementChild.classList.add("bi-caret-up-square-fill");
  }
}
