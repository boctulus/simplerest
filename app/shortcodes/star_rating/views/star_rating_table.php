<?php

use simplerest\core\libs\BootstrapPaginator;

?>

<style>
    .pagination .active a {
        background-color: #007bff;
        color: #fff;
        border-color: #007bff;
    }
</style>


<div class="container mt-5">
    <!-- <h2>Reviews</h2> -->

    <table class="table">
        <thead>
            <tr>
                <th scope="col">Commento</th>
                <th scope="col" style="width: 110px;">Punteggio</th>
                <th scope="col">Cliente</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['rows'] as $row) : ?>
                <tr>
                    <td><?= $row['comment']; ?></td>
                    <td><?= str_repeat('⭐', $row['score']) ?></td>
                    <td><?= $row['author']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?= BootstrapPaginator::render($data, 5, true) ?>
</div>
