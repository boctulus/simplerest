<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $title ?? ''; ?></title>

    <base href="<?= base_url(); ?>">

    <script src="<?= assets('js/bootstrap/bootstrap.bundle.min.js') ?>"></script>

    <link href="<?= assets('styles/bootstrap/bootstrap.min.css') ?>" rel="stylesheet"/>

    <script src="<?= assets('js/fontawesome-5.js') ?>"></script>
</head>
<body>
    <?php
        if (!isset($hidenav) || !$hidenav){
            section('navbar.php'); 
        }            
    ?>
    
    <div class="container">
        <main>
           <?= $content; ?>
        </main>
    </div>

    <div class="mt-3">
        <?= section('footer.php') ?>
    </div>
</body>
</html>
