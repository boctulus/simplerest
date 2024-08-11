<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $title ?? '' ?></title>

    <?=
        base() . 
        head() 
    ?>

</head>
<body>
    <div class="container">
        <main>
           <?= $content ?? ''; ?>
        </main>
    </div>

    <footer id="footer">
        <?= $footer_content ?? '' ?>
        <?= footer(); ?>
    </footer>
</body>
</html>
