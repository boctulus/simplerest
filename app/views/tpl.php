<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $title ?? ''; ?></title>

    <base href="<?= base_url(); ?>">

    <!-- ico -->
    <link rel="shortcut icon" href="<?= asset('img/favicon.ico') ?>" />

    <!-- google fonts 

        For download 
        https://github.com/majodev/google-webfonts-helper
    -->
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
    <link rel="stylesheet" href="<?= asset('css/lib/lobipanel/lobipanel.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/separate/vendor_css/lobipanel.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/lib/jqueryui/jquery-ui.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/separate/pages/widgets.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/lib/font-awesome/font-awesome.min.css') ?>">

    <!-- bootstrap 5.1.3 solo css -->
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">

    <!-- JavaScript Bundle with Popper -->
    <script src="<?= asset('js/bootstrap.bundle.min.js') ?>"></script>

    <link rel="stylesheet" href="<?= asset('css/separate/vendor_css/bootstrap-select/bootstrap-select.min.css') ?>">

    <link rel="stylesheet" href="<?= asset('css/separate/vendor_css/bootstrap-datetimepicker.min.css') ?>">

    <!-- BOOTSTRAP -->
    <link rel="stylesheet" href="<?= asset('css/lib/datatables-net/datatables.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/lib/datatables-net/datatables-net.min.css') ?>">

    <!-- FILEPOND -->
    <!--link rel="stylesheet" href="... 'js/plugins/filepond/dist/filepond.css') ?>"-->

    <!--script src="< ? = asset('js/helpers.js') ?>"></script-->
    <!--script src="< ?= asset('js/login.js') ?>"></script-->


</head>
<body>
    <div class="container-fluid">
        <main>
           <?= $content; ?>
        </main>
    </div>
</body>
</html>
