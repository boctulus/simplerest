<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $title ?? ''; ?></title>

    <script>
        const base_url  = '<?= base_url(); ?>';
    </script>

    <base href="<?= base_url(); ?>">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <?php
        echo meta('content-type','text/html; charset=utf-8','equiv') . PHP_EOL;

        render_metas();
        render_css();        
        render_js(true);   
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
    
        <?php    
            render_js(false);     
        ?>
    </footer>
</body>
</html>
