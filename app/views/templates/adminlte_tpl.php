<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $title ?? '' ?></title>

    <?= base() ?>

    <!-- AdminLTE -->
    <link rel="stylesheet"
          href="<?= asset('third_party/adminlte/dist/css/adminlte.min.css') ?>">

    <!-- Opcional: SOLO si realmente necesitas iconos -->
    <!-- Mejor usar SVG inline -->
    <!--
    <link rel="stylesheet"
          href="<?= asset('third_party/adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
    -->

    <?= umodel() . head(); ?>

    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">

    <style>
        body {
            font-family:
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
        }
    </style>
</head>

<body class="sidebar-mini layout-fixed">

<div class="wrapper">

    <!-- NAVBAR -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link"
                   data-widget="pushmenu"
                   href="#"
                   role="button">
                    ☰
                </a>
            </li>

            <li class="nav-item d-none d-sm-inline-block">
                <a href="/" class="nav-link">Home</a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a href="#" id="btn_logout" class="nav-link">
                    Logout
                </a>
            </li>
        </ul>
    </nav>

    <!-- SIDEBAR -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">

        <a href="/" class="brand-link text-decoration-none">
            <span class="brand-text">
                <?= $brand_name ?? 'Admin' ?>
            </span>
        </a>

        <div class="sidebar">

            <div class="p-3 text-white">
                <span id="username_text"></span>
            </div>

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column">

                    <?php
                    use Boctulus\Simplerest\Core\Libs\Config;

                    $pg_grps = Config::get()['admin_menu_linked_pages'] ?? null;

                    if ($pg_grps) {
                        echo tag('navItemSideMenu')
                            ->items($pg_grps);
                    }
                    ?>

                </ul>
            </nav>
        </div>
    </aside>

    <!-- CONTENT -->
    <div class="content-wrapper">

        <div class="content-header">
            <div class="container-fluid">
                <h1 class="m-0">
                    <?= $page_name ?? 'Page' ?>
                </h1>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <?= $content ?>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="main-footer">
        <?= $footer ?? '' ?>
    </footer>

</div>

<!-- AdminLTE -->
<script src="<?= asset('third_party/adminlte/plugins/jquery/jquery.min.js') ?>"></script>

<script src="<?= asset('third_party/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

<script src="<?= asset('third_party/adminlte/dist/js/adminlte.min.js') ?>"></script>

<script src="<?= asset('js/login.js') ?>"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {

    if (!isLoggedIn()) {
        window.location.replace(base_url + '/auth/login');
        return;
    }

    const usernameEl = document.querySelector('#username_text');
    if (usernameEl) {
        usernameEl.textContent = username();
    }

    const logoutBtn = document.querySelector('#btn_logout');

    if (logoutBtn) {
        logoutBtn.addEventListener("click", (e) => {
            e.preventDefault();
            logout();
        });
    }
});
</script>

<?= footer(); ?>

</body>
</html>