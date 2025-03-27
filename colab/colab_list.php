<?php
require dirname(__DIR__) . '/seguridad/auth.php';
$indice = "colaboracion";
include dirname(__DIR__) . "/partials/header.php";
?>
<style>
    @media (max-width: 1000px) {
        body {
            font-size: 0.85rem;
        }

        tr.head-table th {
            display: none;
        }

        tr.body-table td {
            display: block;
        }

        .data::before {
            content: attr(data-cell) ": ";
            font-weight: 700;
            text-transform: capitalize;
        }
    }
</style>

<body class="bg-light">
    <div class="container py-5">
        <div class="mb-3">
            <h6 class="mb-3">Colaboraciones</h6>
        </div>
        <table class="table table-striped" style="width:97%;margin: 0 auto; table-layout:fixed;font-size:small">
            <thead class="table-danger text-center" style="position: sticky; top:-1px; z-index: 1;">
                <tr class="head-table">
                    <th style="width:23%">Usuario</th>
                    <th style="width:23%">Correo</th>
                    <th style="width:20%">Fecha</th>
                    <th style="width:13%">Monto</th>
                    <th style="width:13%">No. Transaccion</th>
                    <th style="width:16%">Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaColaboraciones" class="text-center"></tbody>
        </table>
</body>
<?php include dirname(__DIR__) . "/partials/boostrap_script.php" ?>
<script>
    window.onload = function() {
        cargaListaColaboraciones();
    };

    function cargaListaColaboraciones() {
        tablaColaboraciones.innerHTML = "";

        $.post("conexion_colab.php", {
            ingresar: "colab_list"
        }).done(function(data, error) {
            let datos = JSON.parse(data);
            let listaColabHtml = "";
            datos.forEach(element => {
                listaColabHtml += `
            <tr class="body-table">
                <td data-cell="Usuario" class="data">${element.user}</td>
                <td data-cell="Correo" class="data">${element.correo}</td>
                <td data-cell="Fecha" class="data">${element.fecha_colaboracion}</td>
                <td data-cell="Monto" class="data">${element.monto}</td>
                <td data-cell="No. Tansaccion" class="data" Transacción">
                    <input type="text" class="numero-transaccion w-100" data-id="${element.idcolaboracion}" value="${element.numero_transaccion || ''}">
                </td>
                <td data-cell="Acciones" class="data">
                    <input type="checkbox" class="verificar-colab" data-id="${element.idcolaboracion}" ${element.verificado == 1 ? 'checked' : ''}>
                </td>
            </tr>
            `;
            });
            tablaColaboraciones.innerHTML = listaColabHtml;

            document.querySelectorAll('.verificar-colab').forEach(item => {
                item.addEventListener('change', function() {
                    let idColab = this.dataset.id;
                    let numeroTransaccion = document.querySelector(
                        `.numero-transaccion[data-id="${idColab}"]`).value;

                    // Verificar si el número de transacción está vacío
                    if (!numeroTransaccion.trim()) {
                        // Si no hay número de transacción, mostrar un alert y desmarcar el checkbox
                        alert("Debes ingresar un número de transacción para verificar.");
                        this.checked = false; // Desmarcar el checkbox
                        return; // Evitar que la función se ejecute si no hay número de transacción
                    }

                    // Si el número de transacción existe, llamar a la función para actualizar
                    actualizarVerificacion(idColab, this.checked, numeroTransaccion);
                });
            });
        });
    }

    function actualizarVerificacion(idColab, estado, numeroTransaccion) {
        $.post("conexion_colab.php", {
            ingresar: "actualizar_verificado",
            id: idColab,
            verificado: estado ? 1 : 0,
            numero_transaccion: numeroTransaccion
        }).done(function(respuesta) {
            let data = JSON.parse(respuesta);
            if (data.success) {
                console.log(data.message); // Mostrar mensaje de éxito en la consola
            } else {
                alert(data.message); // Mostrar mensaje de error al usuario
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            // En caso de que ocurra un error con la petición
            alert("Error de comunicación con el servidor: " + textStatus + " " + errorThrown);
        });
    }
</script>