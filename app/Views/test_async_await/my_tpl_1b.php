<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            background-color: aquamarine;
            height:100vh;
        }
    </style>

</head>
<body>
    <img src="<?= asset('img/avatar.png'); ?>" />

    <?= $content ?? ''; ?>

    <script>
        // blocking
        function count($till) {
            for (let x=0; x<$till; x++){

            }
        }

        // Puede llegar a ejecutarse antes de que termine de cargarse la pagina bloqueando igual el rendering
        count(9999999999);
        alert('Soy un Alert');
    </script>
</body>
</html>
