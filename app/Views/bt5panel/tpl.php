<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $title ?? '' ?></title>

    <?= base() ?>

    <link rel="stylesheet" href="<?= asset('third_party/bootstrap/5.x/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= asset('./css/main.css') ?>">

    <!-- jQuery -->
    <script src="<?= asset('third_party/jquery/3.3.1/jquery.min.js') ?>"></script>

    <?= head() ?>

</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Mi Aplicación</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php section('bt5panel/navbar_content.php') ?>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div id="sidebar" class="bg-dark collapsed">
        <div class="sidebar-header">
            <h4>Menú</h4>
        </div>
        <?php section('bt5panel/sidebar_content.php') ?>
    </div>

    <!-- Content -->
    <div id="content" class="p-4">
        <button id="sidebarCollapse" class="btn btn-dark">Toggle Sidebar</button>
        <?php echo $content; ?>
    </div>

    <footer id="footer">
        <!-- Bootstrap 5.1.3 -->
        <script src="<?= asset('third_party/bootstrap/5.x/bootstrap.bundle.min.js') ?>"></script>
    
        <?php section('bt5panel/footer_content.php') ?>

        <script>
             document.addEventListener("DOMContentLoaded", () => {

                document.getElementById('sidebarCollapse').addEventListener('click', function () {
                    document.getElementById('sidebar').classList.toggle('collapsed');
                    document.getElementById('content').classList.toggle('collapsed');
                });

            });
        </script>
    </footer>
</body>
</html>
