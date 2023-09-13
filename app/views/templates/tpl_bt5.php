<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $title ?? '' ?></title>

    <?= base() ?>

    <link rel="stylesheet" href="<?= asset('vendors/bootstrap/5.x/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('vendors/adminlte/plugins/fontawesome-free/css/all.min.css?v=6.2') ?>">

    <!-- jQuery -->
    <script src="<?= asset('vendors/adminlte/plugins/jquery/jquery.min.js') ?>"></script>

    <?php
        head();  
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
            
        <!-- Bootstrap 5.1.3 -->
        <script src="<?= asset('vendors/bootstrap/5.x/bootstrap.bundle.min.js') ?>"></script>
    
        <?php
            footer();  
        ?>
    </footer>
</body>
</html>
