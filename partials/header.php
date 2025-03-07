<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registro de Viajes Boosmap</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous" />
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker3.standalone.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href=<?= $baseUrl . "assets/css/styles.css" ?>>
    <script src=<?= $baseUrl . "assets/js/funciones.js" ?>></script>
    <script>
        const baseUrl = "<?= $baseUrl ?>";
    </script>
</head>
<?php if ($indice != 'inicio' && $indice != 'login') { ?>

    <body>
        <div class="container px-0" style="max-width:850px">
            <?php include __DIR__ . "/../partials/navbar.php"; ?>
        <?php } ?>