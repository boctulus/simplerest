<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $title ?? '' ?></title>

    <?= base() ?>

    <!-- TailWind -->
    <script src="<?= asset('third_party/tailwind/3.4/tailwind-3.4.16.js') ?>"></script>

    <?= head() ?>

</head>
<body>
    <?= $content ?? '' ?>

    <footer id="footer">
        <?= $footer_content ?? '' ?>    
        <?= footer() ?>
    </footer>
</body>
</html>
