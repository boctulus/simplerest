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

        if (isset($head) && is_array($head)){
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
    <div class="container">
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
           <?= $content ?? ''; ?>
        </main>
    </div>

    <footer id="footer">
        <?php
    
            if (isset($footer) && is_array($footer)){
                if (isset($footer['content'])) 
                    echo $footer['content'] ?? ''; 
            
                if (isset($footer['js'])) 
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
            
        ?>
    </footer>
</body>
</html>
