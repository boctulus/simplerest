<?php

$categos = array (
    0 => 
    array (
      'id' => 16,
      'name' => 'A',
      'count' => 1,
      'path' => '/ A',
    ),
    1 => 
    array (
      'id' => 19,
      'name' => 'A1',
      'count' => 550,
      'path' => '/ A > A1',
    ),
    2 => 
    array (
      'id' => 20,
      'name' => 'A2',
      'count' => 1,
      'path' => '/ A > A2',
    ),
    3 => 
    array (
      'id' => 21,
      'name' => 'A2-1',
      'count' => 0,
      'path' => '/ A > A2 > A2-1',
    ),
    4 => 
    array (
      'id' => 23,
      'name' => 'A2-1a',
      'count' => 0,
      'path' => '/ A > A2 > A2-1 > A2-1a',
    ),
    5 => 
    array (
      'id' => 17,
      'name' => 'B',
      'count' => 0,
      'path' => '/ B',
    ),
    6 => 
    array (
      'id' => 18,
      'name' => 'C',
      'count' => 0,
      'path' => '/ C',
    ),
    7 => 
    array (
      'id' => 22,
      'name' => 'C1',
      'count' => 0,
      'path' => '/ C > C1',
    ),
    8 => 
    array (
      'id' => 15,
      'name' => 'Uncategorized',
      'count' => 3,
      'path' => '/ Uncategorized',
    ),
  );

function round_count(int $q){
    if ($q > 100000){
        return '+100K';
    }

    if ($q > 50000){
        return '+50K';
    }

    if ($q > 20000){
        return '+20K';
    }

    if ($q > 10000){
        return '+10K';
    }

    if ($q > 1000){
        return '+1K';
    }

    if ($q > 500){
        return '+500';
    }

    if ($q > 400){
        return '+400';
    }

    if ($q > 300){
        return '+300';
    }

    if ($q > 200){
        return '+200';
    }

    if ($q > 100){
        return '+100';
    }

    if ($q > 50){
        return '+50';
    }

    return $q;
}

css('
.text-small {
    font-size: 0.9rem !important;
}

body {
    background: linear-gradient(to left, #56ab2f, #a8e063);
}

.cursor-pointer {
    cursor: pointer;
}
');
?>

<script>
    let checkboxes = [];

    function is_cat_checked(cat_id){
        return document.querySelector('#cat-' + cat_id).checked;
    }

    function set_cat_error_msg(msg){
        document.querySelector('#cat-selector-msgs').innerText = msg;
    }

    function clear_cat_error_msg(msg){
        document.querySelector('#cat-selector-msgs').innerText = "&nbsp;"
    }

    function report_cat_change(id, elem, event){
        const check = document.querySelector('#cat-'+id).checked;
        checkboxes[id] = check;

        let acc = 0;
        checkboxes.forEach((element, ix) => {
            acc += element ? 1 : 0;

            if (acc >3){
                document.querySelector('#cat-'+ id).checked = false;
                checkboxes[id] = false
            }  
        });
    }

</script>

<!-- Demo header-->
<section class="py-5 header text-center text-white">
    <div class="container pt-4">
        <header>
            <h1 class="display-4">Categorias</h1>
        </header>
    </div>
</section>


<section>
    <div class="container">
        <div class="row">
            <div class="col-lg-7 mx-auto">
                
                <div class="card shadow border-0 mb-5">
                    <div class="card-body p-5">
                        <h2 class="h4 mb-1">Categorias de productos</h2>
                        <p class="small text-muted font-italic mb-4">Hasta un maximo de 3</p>
                        <ul class="list-group">

                            <?php foreach ($categos as $cat): ?>

                            <li class="list-group-item rounded-0 d-flex align-items-center justify-content-between">
                                <div class="custom-control cat-chk-group">
                                    <input class="form-check-input cat-chk me-2" id="cat-<?= $cat['id']?>" type="checkbox" name="cat-<?= $cat['id']?>" onchange="report_cat_change(<?= $cat['id'] ?>, this, event)">
                                    <label class="custom-control-label" for="cat-<?= $cat['id']?>">
                                        <p class="mb-0"><?= $cat['name'] ?></p>
                                    </label>
                                    <p class="mb-0"><span class="small font-italic text-muted"><?= $cat['path'] ?></span></p>
                                </div>
                                <!-- label for="customRadio1"><img src="https://i.postimg.cc/Hsq4Ygss/1-ezgo0i.png" alt="" width="60"></label -->
                                <span class="badge bg-primary rounded-pill"><?= round_count($cat['count']) ?></span>
                            </li>

                            <?php endforeach; ?>
                          
                        </ul>

                        <div class="card-footer mt-2" id="cat-selector-msgs" style="background-color: transparent; color: red;">
                            &nbsp;                      
                        </div>

                    </div>
                </div>                
            </div>
        </div>
    </div>
</section>

