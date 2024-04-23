<style>
    #pre_error { 
        white-space: pre-wrap; 
        word-break: break-word;
        background-color: transparent;
    }
</style>

<h1 style="font-size:450%; font-weight: bold; color: red;"><?= $status ?></h1>

<br/>
<img src="<?= asset('img/warning.svg.png') ?>" class="mt-3" style="width:15%;" />


<div class="mt-5">
    Error type: <?= $type ?><br/>
    Location: <?= $location ?? '' ?><br/>
    Code: <?= $code ?><br/>
    Message: <?= $message ?? '' ?><br/>
    <?php
        if ($detail != null){
            if (is_array($detail)){
                dd($detail, 'Detail');
            } else{
                echo "Detail: ". p(). pre($detail, 'pre_error');
            }
        }
    ?>
</div>

