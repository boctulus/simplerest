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

     <!--
        Me traigo los nombres de las keys con que debo armar los JSON
        para pegarle a los endpoints
    -->
    <script>
        const base_url  = '<?= base_url(); ?>';

        /*
            Auth
        */

        let $__email    = '<?php echo $__email    ?? null; ?>'; 
        let $__username = '<?php echo $__username ?? null; ?>';
        let $__password = '<?php echo $__password ?? null; ?>';
    </script>

    <script src="<?= asset('js/login.js') ?>"></script>

    <!-- ICONOS FONTAWESOME -->
    <script src="https://kit.fontawesome.com/3f60db90e4.js" crossorigin="anonymous"></script>
    
    <!-- TEMPLATE ADMIN LTE -->

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= asset('adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
    
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->

    <!-- bootstrap 5.1.3 solo css -->
    <link rel="stylesheet" href="<?= asset('css/bootstrap.min.css') ?>">
    
    <!-- iCheck -->
    <link rel="stylesheet" href="<?= asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') ?>">
    <!-- JQVMap -->
    <link rel="stylesheet" href="<?= asset('adminlte/plugins/jqvmap/jqvmap.min.css') ?>">
   
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?= asset('adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') ?> ">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="<?= asset('adminlte/plugins/daterangepicker/daterangepicker.css') ?>">
    <!-- summernote -->
    <link rel="stylesheet" href="<?= asset('adminlte/plugins/summernote/summernote-bs4.min.css') ?>">

   
    <!-- Datatables -->
    <link rel="stylesheet" href="<?= asset('css/lib/datatables-net/datatables.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/lib/datatables-net/datatables-net.min.css') ?>">

     <!-- jQuery -->
     <script src="<?= asset('adminlte/plugins/jquery/jquery.min.js') ?>"></script>
     
    <!-- JavaScript Bundle with Popper -->
    <script src="<?= asset('js/bootstrap.bundle.min.js') ?>"></script>

    <!-- FILEPOND -->
    <!--link rel="stylesheet" href="... 'js/plugins/filepond/dist/filepond.css') ?>"-->


    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.3/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.2.0/dist/select2-bootstrap-5-theme.min.css" />

    <!-- DualListbox -->
    <link rel="stylesheet" href="<?= asset('adminlte/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.css') ?>"/>
    <script src="<?= asset('adminlte/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') ?>"></script>

    <!-- InputMask -->
    <script src="<?= asset('adminlte/plugins/moment/moment.min.js') ?>"></script>
    <script src="<?= asset('adminlte/plugins/inputmask/jquery.inputmask.min.js') ?>"></script>

    <!-- date-range-picker -->
    <link rel="stylesheet" href="<?= asset('adminlte/plugins/daterangepicker/daterangepicker.css') ?>"/>
    <script src="<?= asset('adminlte/plugins/daterangepicker/daterangepicker.js') ?>"></script>
    

    <link rel="stylesheet" href="<?= asset('css/main.css') ?>"/>

    <?php
        echo meta('content-type','text/html; charset=utf-8','equiv') . PHP_EOL;

        /*
            Quizas se puede dividir en directivas para reemplazar los foreach:
            
            render_metas($arr)
            render_css($arr)
            render_js($arr)

            El problema creo esta en poder desde una vista empujar una meta, css o jss a head / footer
        */

        if (isset($head) && is_array($head))
        {
            if (isset($head['meta'])){
                foreach ($head['meta'] as $m)
                    echo meta($m['name'], $m['content']) . PHP_EOL;
            }
    
            if (isset($head['css'])){
                foreach ($head['css'] as $_css)
                    echo link_tag("$_css") . PHP_EOL;
            }	
            
            if (isset($head['js'])){
                foreach ($head['js'] as $_js){
                    if (substr($_js, 0, 4) != 'http'){
                        $path = base_url() . $_js;
                    } else {
                        $path = $_js;
                    }							
                ?>
                    <script type="text/javascript" src="<?= $path ?>" ></script>
                <?php
                }

            }
        }
    ?>
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
    <!--script src="< ?= asset('adminlte/plugins/jquery-ui/jquery-ui.min.js') ?>"></script-->

    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        //$.widget.bridge('uibutton', $.ui.button)
    </script>

    <!-- ChartJS -->
    <script src="<?= asset('adminlte/plugins/chart.js/Chart.min.js') ?>"></script>
    
    <!-- Sparkline -->
    <script src="<?= asset('adminlte/plugins/sparklines/sparkline.js') ?>"></script>
    
    <!-- JQVMap -->
    <script src="<?= asset('adminlte/plugins/jqvmap/jquery.vmap.min.js') ?>"></script>
    <script src="<?= asset('adminlte/plugins/jqvmap/maps/jquery.vmap.usa.js') ?>"></script>
    
    <!-- jQuery Knob Chart -->
    <script src="<?= asset('adminlte/plugins/jquery-knob/jquery.knob.min.js') ?>"></script>
    
    <!-- daterangepicker -->
    <script src="<?= asset('adminlte/plugins/moment/moment.min.js') ?>"></script>
    <script src="<?= asset('adminlte/plugins/daterangepicker/daterangepicker.js') ?>"></script>
    
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="<?= asset('adminlte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') ?>"></script>
    
    <!-- Summernote -->
    <script src="<?= asset('adminlte/plugins/summernote/summernote-bs4.min.js') ?>"></script>
    
    <!-- overlayScrollbars -->
    <script src="<?= asset('adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') ?>"></script>

    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.3/dist/js/select2.min.js"></script>

    
    <script src="<?= asset('js/boostrap_notices.js') ?>"></script>

    <footer id="footer">
        <?= $footer_content ?? '' ?>
      
        <!--
            JS rendering in footer
        -->

        <?php    
            if (isset($footer) && is_array($footer)){
                if (isset($footer['js'])){
                    foreach ($footer['js'] as $_js){
                        if (substr($_js, 0, 4) != 'http'){
                            $path = base_url() . $_js;
                        } else {
                            $path = $_js;
                        }							
                        ?>
                
                        <script type="text/javascript" src="<?= $path ?>" ></script>
                        <?php
                    }
                }   
            }            
        ?>
    </footer>
</body>
</html>
