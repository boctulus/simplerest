
<ul>
    <?php
    foreach ($agendados as $item):
    ?>
        <li><?= "{$item['nombre']} | {$item['telefono']}" ?>
    <?php
    endforeach;
    ?>
</ul>


