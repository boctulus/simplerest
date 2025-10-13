<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width">
<title><?= $subject ?></title>
</head>
<body style="height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; font-family: Arial, sans-serif;">
    <header style="width: 100%; background-color: #f2f2f2; text-align: center;">
        <img src="<?= $logo_url ?>" alt="Logo" style="border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; width: 100%;">
    </header>

    <!-- Cuerpo del mensaje -->
    <section style="margin: 0;">
        <?= $content ?>
    </section>

</body>
</html>
