<?php

/*
*   Usar <social-profile-card> con el array de personal
*/

foreach ($personal as $instructor) {
    echo "<social-profile-card
        name='{$instructor['name']}'
        subtitle='{$instructor['position']}'
        stats='" . count($instructor['lines_families']) . " Lines/Products'
        rating='⭐ {$instructor['expertise']} Expertise'
        image='". asset('img/profile_pics/'. $instructor['img_url']) ."'
    ></social-profile-card>";
}


?>