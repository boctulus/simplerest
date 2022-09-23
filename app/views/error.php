<style>
    #pre_error { 
        white-space: pre-wrap; 
        word-break: break-word;
        background-color: transparent;
    }
</style>

<h1 style="font-size:450%; font-weight: bold; color: red;"><?= $status ?></h1>

<br/>
<img src="<?= asset('img/warning.svg.png') ?>" style="width:15%; margin-top:-10px;" />


<div style="margin-top: 30px; font-size:100%;">
    Error type: <?= $type ?><br/>
    Location: <?= $location ?? '' ?><br/>
    Code: <?= $code ?><br/>
    Message: <?= $message ?? '' ?><br/>
    Detail: <?= p(). pre($detail, 'pre_error') ?? '' ?>
</div>

