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
        document.addEventListener("DOMContentLoaded", ()=>{
            alert('Soy un Alert');
        });
       
    </script>
</body>
</html>
