<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

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

    <?= 
        umodel();
        head(); 
    ?>


    <!-- ICONOS FONTAWESOME -->
    <script src="https://kit.fontawesome.com/3f60db90e4.js" crossorigin="anonymous"></script>
    
    <!-- TEMPLATE ADMIN LTE -->

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= asset('vendors/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
    
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->

    <!-- bootstrap 5.1.3 solo css -->
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
    
    <!-- iCheck -->
    <link rel="stylesheet" href="<?= asset('vendors/adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
    <!-- JQVMap -->
    <link rel="stylesheet" href="<?= asset('vendors/adminlte/plugins/jqvmap/jqvmap.min.css') ?>">
   
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?= asset('vendors/adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') ?> ">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="<?= asset('vendors/adminlte/plugins/daterangepicker/daterangepicker.css') ?>">
    <!-- summernote -->
    <link rel="stylesheet" href="<?= asset('vendors/adminlte/plugins/summernote/summernote-bs4.min.css') ?>">

   
    <!-- Datatables -->
    <link rel="stylesheet" href="<?= asset('css/lib/datatables-net/datatables.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/lib/datatables-net/datatables-net.min.css') ?>">

     <!-- jQuery -->
     <script src="<?= asset('vendors/adminlte/plugins/jquery/jquery.min.js') ?>"></script>
     
    <!-- JavaScript Bundle with Popper -->
    <script src="<?= asset('js/bootstrap.bundle.min.js') ?>"></script>

    <!-- FILEPOND -->
    <!--link rel="stylesheet" href="... 'js/plugins/filepond/dist/filepond.css') ?>"-->


    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.2.0/dist/select2-bootstrap-5-theme.min.css" />

    <!-- DualListbox -->
    <link rel="stylesheet" href="<?= asset('vendors/adminlte/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.css') ?>"/>
    <script src="<?= asset('vendors/adminlte/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') ?>"></script>

    <!-- InputMask -->
    <script src="<?= asset('vendors/adminlte/plugins/moment/moment.min.js') ?>"></script>
    <script src="<?= asset('vendors/adminlte/plugins/inputmask/jquery.inputmask.min.js') ?>"></script>

    <!-- date-range-picker -->
    <link rel="stylesheet" href="<?= asset('vendors/adminlte/plugins/daterangepicker/daterangepicker.css') ?>"/>
    <script src="<?= asset('vendors/adminlte/plugins/daterangepicker/daterangepicker.js') ?>"></script>
    

    <!-- OneSignal push notificaciones -->
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async></script>
    <script>
            window.OneSignal = window.OneSignal || [];
            OneSignal.push(function() {
                OneSignal.init({
                appId: "9381a718-414c-4f09-b810-2288913de0a0",
                });
            });
    </script>
    

    <link rel="stylesheet" href="<?= asset('css/main.css') ?>"/>
</head>
<body>
    <div class="container-fluid">
        <main>
           <?= $content; ?>
        </main>
    </div>
    

    <!-- jQuery UI 1.11.4 -->
    <!--script src="< ?= asset('vendors/adminlte/plugins/jquery-ui/jquery-ui.min.js') ?>"></script-->

    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        //$.widget.bridge('uibutton', $.ui.button)
    </script>

    <!-- ChartJS -->
    <script src="<?= asset('vendors/adminlte/plugins/chart.js/Chart.min.js') ?>"></script>
    
    <!-- Sparkline -->
    <script src="<?= asset('vendors/adminlte/plugins/sparklines/sparkline.js') ?>"></script>
    
    <!-- JQVMap -->
    <script src="<?= asset('vendors/adminlte/plugins/jqvmap/jquery.vmap.min.js') ?>"></script>
    <script src="<?= asset('vendors/adminlte/plugins/jqvmap/maps/jquery.vmap.usa.js') ?>"></script>
    
    <!-- jQuery Knob Chart -->
    <script src="<?= asset('vendors/adminlte/plugins/jquery-knob/jquery.knob.min.js') ?>"></script>
    
    <!-- daterangepicker -->
    <script src="<?= asset('vendors/adminlte/plugins/moment/moment.min.js') ?>"></script>
    <script src="<?= asset('vendors/adminlte/plugins/daterangepicker/daterangepicker.js') ?>"></script>
    
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="<?= asset('vendors/adminlte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') ?>"></script>
    
    <!-- Summernote -->
    <script src="<?= asset('vendors/adminlte/plugins/summernote/summernote-bs4.min.js') ?>"></script>
    
    <!-- overlayScrollbars -->
    <script src="<?= asset('vendors/adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') ?>"></script>

    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <!-- OneSignal -->
    <script>
        OneSignal.push(function() {
            OneSignal.isPushNotificationsEnabled(function(isEnabled) {
                if (isEnabled){
                    console.log("Push notifications are enabled!");
                } else {
                    console.log("Push notifications are not enabled yet."); 
                }   
            });
        });
    </script>
    
    
    <script src="<?= asset('js/boostrap_notices.js') ?>"></script>
    <script src="<?= asset('js/login.js') ?>"></script>
</body>
</html>
