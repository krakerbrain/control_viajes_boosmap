<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../seguridad/JWT/jwt.php';
include __DIR__ . "/../partials/header.php";

$datosUsuario = validarToken();
$indice = "estadisticas";

if (!$datosUsuario) {
    header($_ENV['URL_LOCAL']);
    exit;
}

?>

<body>
    <div class="container px-0" style="max-width:850px">
        <?php include __DIR__ . "/../partials/navbar.php"; ?>
        <div class="mx-3">

            <div>
                <div>
                    <h6 class="my-2">Seleccione un mes para ver sus estadísticas</h6>
                    <form action="conexiones_estadisticas.php" method="post" class="mx-auto">
                        <select name="selectMes" id="selectMes"></select>
                    </form>

                    <table class="table table-sm table-striped table-bordered text-center mt-3" style="font-size:0.8rem;font-weight:400 ;">
                        <tbody id="estadisticas"></tbody>
                    </table>
                </div>
                <div>
                    <h6 class=" my-2">Gráfico mes a mes</h6>
                    <canvas id="myChart"></canvas>
                </div>
            </div>
            <table class="table table-striped mt-3 table-sm">
                <thead class="table-danger text-center">
                    <td class='p1' style="cursor:pointer" title="Puede ordenar por destino" onclick="viajesporruta(true,'destino')">Destino <i id="iconoOrdenDestino" class="fas fa-sort"></i></td>
                    <td class='p1' style="cursor:pointer" title="Puede ordenar por cantidad de viajes" onclick="viajesporruta(true,'conteo')">Viajes x Mes <i id="iconoOrdenViajesMes" class="fas fa-sort"></i></td>
                </thead>
                <tbody id="conteoviajes" class="text-center">

                </tbody>
            </table>
            <input type="hidden" name="ordenconteo" id="ordenconteo" value="">
        </div>
        <div>
            <?php include __DIR__ . "/../filtros/index.php"; ?>
        </div>
    </div>
</body>
<?php include __DIR__ . "/../partials/boostrap_script.php" ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript" src="../componente/js/manejo-de-fieldsets.js"></script>
<script type="text/javascript" src="conexiones.js"></script>

</html>