<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? '', ENT_QUOTES, 'UTF-8') ?></title>
    <?= base() . head() ?>
</head>
<body>
    <?= $content ?? '' ?>
    <?= footer() ?>
</body>
</html>
