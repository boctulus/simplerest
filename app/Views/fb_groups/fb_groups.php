<div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <h1 class="text-center mt-4 mb-4">Grupos de Programación</h1>

                <?php foreach ($sections as $title => $links): ?>
                    <hr>

                    <h2 class="mt-4"><?php echo htmlspecialchars($title); ?></h2>
                    <ul class="list-group mb-4">
                        <?php foreach ($links as $link): ?>
                            <li class="list-group-item">
                                <a href="<?php echo htmlspecialchars($link); ?>" target="_blank" class="text-decoration-none">
                                    <?php echo htmlspecialchars($link); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endforeach; ?>
            </div>
        </div>
    </div>