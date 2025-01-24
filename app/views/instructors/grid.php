

<!-- Contenedor principal de la cuadrícula -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-8 p-4">
    <?php
    foreach ($personal as $instructor) {
        echo "<div class='card-wrapper'>"; // Added wrapper
        echo "<social-profile-card            
            name='{$instructor['name']}'
            subtitle='{$instructor['position']}'
            stats='" . count($instructor['lines_families']) . " Lines/Products'
            rating='⭐ {$instructor['expertise']} Expertise'
            image='". asset('img/profile_pics/'. $instructor['img_url']) ."'
        ></social-profile-card>";
        echo "</div>";
    }
    ?>
</div>