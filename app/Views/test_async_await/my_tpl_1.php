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
    <script>
        /*
            non-blocking 

            https://stackoverflow.com/a/39914235/980631
        */
        function sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }

        // blocking
        function count($till) {
            for (let x=0; x<$till; x++){

            }
        }
    </script>

    <img src="<?= asset('img/avatar.png'); ?>" />

    <?= $content ?? ''; ?>

    <script>
        // Puede llegar a ejecutarse antes de que termine de cargarse la pagina bloqueando igual el rendering
        count(9999999999);
        alert('Soy un Alert');
    </script>
</body>
</html>
