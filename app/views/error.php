<h1 style="font-size:650%; font-weight: bold; color: red;"><?= $status ?></h1>

<br/>
<img src="<?= assets('img/warning.svg.png') ?>" style="width:25%" />


<div style="margin-top: 30px; font-size:150%;">
    Error type: <?= $type ?><br/>
    Code: <?= $code ?><br/>
    Message: <?= $message ?? '' ?><br/>
    Detail: <?= $detail ?? '' ?>
</div>
