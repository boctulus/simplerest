<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $title ?? ''; ?></title>

    <base href="<?= base_url(); ?>">

    <!-- ico -->
    <link rel="shortcut icon" href="<?= assets('img/favicon.ico') ?>" />

    <!-- google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

    <!-- ICONOS FONTAWESOME -->
    <script src="https://kit.fontawesome.com/3f60db90e4.js" crossorigin="anonymous"></script>

    
    <!--
        Me traigo los nombres de las keys con que debo armar los JSON
        para pegarle a los endpoints
    -->
    <script>
        let $__email    = '<?php echo $__email    ?? null; ?>'; 
        let $__username = '<?php echo $__username ?? null; ?>';
        let $__password = '<?php echo $__password ?? null; ?>';
    </script>

    <!-- TEMPLATE STARTUI -->
    <link rel="stylesheet" href="<?= assets('css/lib/lobipanel/lobipanel.min.css') ?>">
    <link rel="stylesheet" href="<?= assets('css/separate/vendor_css/lobipanel.min.css') ?>">
    <link rel="stylesheet" href="<?= assets('css/lib/jqueryui/jquery-ui.min.css') ?>">
    <link rel="stylesheet" href="<?= assets('css/separate/pages/widgets.min.css') ?>">
    <link rel="stylesheet" href="<?= assets('css/lib/font-awesome/font-awesome.min.css') ?>">

    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="<?= assets('css/separate/vendor_css/bootstrap-select/bootstrap-select.min.css') ?>">

    <link rel="stylesheet" href="<?= assets('css/separate/vendor_css/bootstrap-datetimepicker.min.css') ?>">

    <!-- BOOTSTRAP -->
    <link rel="stylesheet" href="<?= assets('css/lib/datatables-net/datatables.min.css') ?>">
    <link rel="stylesheet" href="<?= assets('css/lib/datatables-net/datatables-net.min.css') ?>">

    <!-- FILEPOND -->
    <link rel="stylesheet" href="<?= assets('js/plugins/filepond/dist/filepond.css') ?>">

    <script src="<?= assets('js/helpers.js') ?>"></script>
    <script src="<?= assets('js/login.js') ?>"></script>

    <link  rel="stylesheet" href="<?= assets('css/core.css"') ?>"/>

</head>
<body>
    <div class="container-fluid">
        <main>
           <?= $content; ?>
        </main>
    </div>
</body>
</html>
