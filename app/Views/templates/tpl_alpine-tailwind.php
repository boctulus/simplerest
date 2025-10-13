<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $title ?? '' ?></title>

    <?= base() ?>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

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
            
        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
        <?= footer() ?>
    </footer>
</body>
</html>
