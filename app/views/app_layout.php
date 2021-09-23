<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $title ?? ''; ?></title>

    <base href="<?= base_url(); ?>">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="<?= assets('css/core.css"') ?>" rel="stylesheet"/>
    
    <!--
        Me traigo los nombres de las keys con que debo armar los JSON
        para pegarle a los endpoints
    -->
    <script>
        let $__email    = '<?php echo $__email    ?? null; ?>'; 
        let $__username = '<?php echo $__username ?? null; ?>';
        let $__password = '<?php echo $__password ?? null; ?>';
    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="<?= assets('js/helpers.js') ?>"></script>
    <script src="<?= assets('js/login.js') ?>"></script>
    <script src="<?= assets('js/jqtable.js') ?>"></script>

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
</body>
</html>
