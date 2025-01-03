<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $title ?? ''; ?></title>

    <?= base() ?>

    <!-- ico -->
    <link rel="shortcut icon" href="<?= asset('img/favicon.ico') ?>" />

    <!-- google fonts 

        For download 
        https://github.com/majodev/google-webfonts-helper
    -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

    <!-- jQuery -->
    <script src="<?= asset('third_party/adminlte/plugins/jquery/jquery.min.js') ?>"></script>

    <script src="<?= asset('js/utilities.js') ?>"></script>

    <?= 
        umodel() .
        head();  
    ?>

    <script src="<?= asset('js/login.js') ?>"></script>

    <!-- ICONOS FONTAWESOME -->
    <script src="<?= asset('third_party/fontawesome/5/fontawesome_kit.js') ?>" crossorigin="anonymous"></script>
    
    <!-- TEMPLATE ADMIN LTE -->

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= asset('third_party/adminlte/plugins/fontawesome-free/css/all.min.css?v=6.2') ?>">
    
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?= asset('third_party/ionicframework/ionicons.min.css') ?>">

    <!-- bootstrap 5.1.3 solo css -->
    <link rel="stylesheet" href="<?= asset('third_party/bootstrap/5.x/bootstrap.min.css') ?>">

    <!-- Bootstrap 5.1.3 -->
    <script src="<?= asset('third_party/bootstrap/5.x/bootstrap.bundle.min.js') ?>"></script>
    
    <!-- iCheck -->
    <link rel="stylesheet" href="<?= asset('third_party/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
    
    <!-- JQVMap -->
    <link rel="stylesheet" href="<?= asset('third_party/adminlte/plugins/jqvmap/jqvmap.min.css') ?>">
   
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?= asset('third_party/adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') ?> ">

    <!-- Daterange picker -->
    <link rel="stylesheet" href="<?= asset('third_party/adminlte/plugins/daterangepicker/daterangepicker.css') ?>">
    
    <!-- summernote -->
    <link rel="stylesheet" href="<?= asset('third_party/adminlte/plugins/summernote/summernote-bs4.min.css') ?>">
 
    <!-- Datatables -->
    <link rel="stylesheet" href="<?= asset('css/lib/datatables-net/datatables.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/lib/datatables-net/datatables-net.min.css') ?>">

    
    <!-- FILEPOND -->
    <!--link rel="stylesheet" href="... 'js/plugins/filepond/dist/filepond.css') ?>"-->


    <!-- DualListbox -->
    <link rel="stylesheet" href="<?= asset('third_party/adminlte/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.css') ?>"/>
    <script src="<?= asset('third_party/adminlte/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') ?>"></script>

    <!-- InputMask -->
    <script src="<?= asset('third_party/adminlte/plugins/moment/moment.min.js') ?>"></script>
    <script src="<?= asset('third_party/adminlte/plugins/inputmask/jquery.inputmask.min.js') ?>"></script>

    <!-- date-range-picker -->
    <link rel="stylesheet" href="<?= asset('third_party/adminlte/plugins/daterangepicker/daterangepicker.css') ?>"/>
    <script src="<?= asset('third_party/adminlte/plugins/daterangepicker/daterangepicker.js') ?>"></script>
    

    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">

</head>
<body>
    <div class="container-fluid">
        <nav>
            <script>
                if (logged()){
                    console.log("[x] Cerrar session");
                } else {
                    console.log("[>] Login");
                }
            </script>
        </nav>
    
        <main>
           <?= $content; ?>
        </main>
    </div>
    

    <!-- jQuery UI 1.11.4 -->
    <!--script src="< ?= asset('third_party/adminlte/plugins/jquery-ui/jquery-ui.min.js') ?>"></script-->

    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        //$.widget.bridge('uibutton', $.ui.button)
    </script>

    <!-- ChartJS -->
    <script src="<?= asset('third_party/adminlte/plugins/chart.js/Chart.min.js') ?>"></script>
    
    <!-- Sparkline -->
    <script src="<?= asset('third_party/adminlte/plugins/sparklines/sparkline.js') ?>"></script>
    
    <!-- JQVMap -->
    <script src="<?= asset('third_party/adminlte/plugins/jqvmap/jquery.vmap.min.js') ?>"></script>
    <script src="<?= asset('third_party/adminlte/plugins/jqvmap/maps/jquery.vmap.usa.js') ?>"></script>
    
    <!-- jQuery Knob Chart -->
    <script src="<?= asset('third_party/adminlte/plugins/jquery-knob/jquery.knob.min.js') ?>"></script>
    
    <!-- daterangepicker -->
    <script src="<?= asset('third_party/adminlte/plugins/moment/moment.min.js') ?>"></script>
    <script src="<?= asset('third_party/adminlte/plugins/daterangepicker/daterangepicker.js') ?>"></script>
    
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="<?= asset('third_party/adminlte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') ?>"></script>
    
    <!-- Summernote -->
    <script src="<?= asset('third_party/adminlte/plugins/summernote/summernote-bs4.min.js') ?>"></script>
    
    <!-- overlayScrollbars -->
    <script src="<?= asset('third_party/adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') ?>"></script>

    
    <script src="<?= asset('js/bootstrap/bt_notices.js') ?>"></script>

    <footer id="footer">
        <?= $footer_content ?? '' ?>
    
        <?=
            footer();  
        ?>
    </footer>
</body>
</html>
