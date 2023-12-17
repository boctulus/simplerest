<?php

use simplerest\core\libs\Paginator;
use simplerest\core\libs\Url;

?>

<div class="container mt-5">
    <!-- <h2>Reviews</h2> -->

    <table class="table">
        <thead>
            <tr>
                <th scope="col">Commento</th>
                <th scope="col">Punteggio</th>
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

    <!-- Paginador -->
    <?php
        $page_key = config()['paginator']['params']['page'] ?? 'page';
    ?>

    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php if ($data['paginator']['last_page'] <= 5) : ?>
                <!-- Mostrar todos los enlaces si hay 5 o menos páginas -->
                <?php for ($i = 1; $i <= $data['paginator']['last_page']; $i++) :
                    $page_link = Url::addQueryParam(Url::currentUrl(), $page_key, $i);
                ?>
                    <li class="page-item"><a class="page-link" href="<?= $page_link ?>"><?= $i ?></a></li>
                <?php endfor; ?>
            <?php else : ?>
                <!-- Mostrar botones especiales si hay más de 5 páginas -->
                <?php
                $currentPage = $data['paginator']['current_page'];
                $lastPage = $data['paginator']['last_page'];
                ?>
                <li class="page-item"><a class="page-link" href="<?= Url::addQueryParam(Url::currentUrl(), $page_key, 1) ?>">|<</a></li>
                <li class="page-item"><a class="page-link" href="<?= Url::addQueryParam(Url::currentUrl(), $page_key, max(1, $currentPage - 1)) ?>"><</a></li>
                <?php for ($i = max(1, $currentPage - 2); $i <= min($lastPage, $currentPage + 2); $i++) :
                    $page_link = Url::addQueryParam(Url::currentUrl(), $page_key, $i);
                ?>
                    <li class="page-item"><a class="page-link" href="<?= $page_link ?>"<?= ($i == $currentPage) ? ' class="active"' : '' ?>><?= $i ?></a></li>
                <?php endfor; ?>
                <li class="page-item"><a class="page-link" href="<?= Url::addQueryParam(Url::currentUrl(), $page_key, min($lastPage, $currentPage + 1)) ?>">></a></li>
                <li class="page-item"><a class="page-link" href="<?= Url::addQueryParam(Url::currentUrl(), $page_key, $lastPage) ?>">>|</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>