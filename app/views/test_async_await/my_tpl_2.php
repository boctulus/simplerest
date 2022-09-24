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
       // always blocking
    </script>

    <img src="<?= asset('img/avatar.png'); ?>" />

    <?= $content ?? ''; ?>

    <!--
        footer section
    -->

    <script src="<?= asset('js/test_async_await/my_blocking.js') ?>"></script>

</body>
</html>
