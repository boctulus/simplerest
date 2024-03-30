<?php

function generateCard($imgSrc, $title, $text, $alt = '') {
    return '
    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card border-0">
            <img src="' . $imgSrc . '" class="card-img-top" alt="'.$alt.'" style="height:250px; object-fit: cover;">
            <div class="card-body" style="padding: 1rem 0 0 0">
                <h5 class="card-title">' . $title . '</h5>
                <p class="card-text" style="text-align: justify; text-justify: inter-word;">' . $text . '</p>
            </div>
        </div>
    </div>
    ';
}

?>

<style>
/* Define la fuente EBGaramond-SemiBold para los títulos */
h5.card-title {
    font-family: 'EBGaramond-SemiBold', serif;
}

/* Define la fuente EBGaramond-Regular para el texto */
p.card-text {
    font-family: 'EBGaramond-Regular', serif;
}
</style>

<div class="container-fluid">
    <div class="row">
        <?php
        // Card 1
        echo generateCard(
            shortcode_asset(__DIR__ . '/img/bg5.jpg'),
            "Microgreens for Health",
            "Microgreens are highly nutrient dense with up to 40 times the nutrients of mature plant leaves. Full of flavour they are miniature powerhouses of the superfood world with studies showing many of the nutrients in microgreens act as antioxidants with the power to prevent cell damage."
        );

        // Card 2
        echo generateCard(
            shortcode_asset(__DIR__ . '/img/s5-2.jpg'),
            "Edible flowers for Special Occasions",
            "Edible flowers are a great additon to any special occasions, whether it be a celebration cake or canapé, dinner party dishes or fun cocktail creations they are an easy way for the home cook to add beauty to their culinary creations."
        );

        // Card 3
        echo generateCard(
            shortcode_asset(__DIR__ . '/img/s5-3.jpg'),
            "Microgreens for Chefs",
            "Adding microgreens to your dishes awill instantly elevate your plate! Their delicate look means that colour and texture can be added to dishes whilst still allowing the key ingredients to shine. They can add complexity to dishes with their densley packed flavours as well as nutritional benefits."
        );

        // Card 4
        echo generateCard(
            shortcode_asset(__DIR__ . '/img/s5-4.jpg'),
            "Edible flowers for Mixologists",
            "Using edible flowers is a fantastic way to enhance your cocktails. as well as adding visual interest with their colour, many also have interesting flavours which can complement your cocktail creations."
        );
        ?>
    </div>
</div>