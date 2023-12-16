<?php

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
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php for ($i = 1; $i <= $data['paginator']['last_page']; $i++) : 
                $page_link = Url::addQueryParam(Url::currentUrl(), 'page', $i);
            ?>
                <li class="page-item"><a class="page-link" href="<?= $page_link ?>"><?= $i ?></a></li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>