<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $title ?? '' ?></title>

    <?= base() ?>

    <link rel="stylesheet" href="<?= asset('third_party/bootstrap/5.x/bootstrap.min.css') ?>">

    <!-- jQuery -->
    <script src="<?= asset('third_party/jquery/3.3.1/jquery.min.js') ?>"></script>

    <?= head() ?>

</head>
<body>
    <div class="container">
        <main>
           <?= $content ?? '' ?>
        </main>
    </div>

    <footer id="footer">
        <?= $footer_content ?? '' ?>
            
        <!-- Bootstrap 5.1.3 -->
        <script src="<?= asset('third_party/bootstrap/5.x/bootstrap.bundle.min.js') ?>"></script>
    
        <?= footer() ?>
    </footer>
</body>
</html>
