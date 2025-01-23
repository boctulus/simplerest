<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $title ?? '' ?></title>

    <?= base() ?>

    <link rel="stylesheet" href="<?= asset('third_party/bootstrap/3.x/bootstrap.min.css') ?>">  <!-- Bootstrap 3.x -->
    <link rel="stylesheet" href="<?= asset('third_party/adminlte/plugins/fontawesome-free/css/all.min.css?v=6.2') ?>">

    <!-- jQuery -->
    <script src="<?= asset('third_party/adminlte/plugins/jquery/jquery.min.js') ?>"></script>

    <?=
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
            
        <!-- Bootstrap 3.x -->
        <script src="<?= asset('third_party/bootstrap/3.x/bootstrap.bundle.min.js') ?>"></script>
    
        <?=
            footer();  
        ?>
    </footer>
</body>
</html>
