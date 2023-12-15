<?php

namespace simplerest\shortcodes\star_rating;

class StarRatingShortcode
{
    function __construct(){
        js_file('third_party/jquery/3.3.1/jquery.min.js');  # external
    }

    function footer()
    {
        css_file('https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.carousel.css');
        css_file('https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.theme.default.css');

        css_file('https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.40/css/uikit.min.css');
        
        #css_file(__DIR__ . '/assets/css/owl-carousel.css');    
        #css_file(__DIR__ . '/assets/css/styles.css');    

        // js_file(__DIR__ . '/assets/js/jquery-migrate.min.js', null, true);

        js_file('third_party/owl_slider/owl.carousel.min.js');          # external

        js_file('third_party/fontawesome/fontawesome_kit.js');          # external
        #css_file('third_party/fontawesome/all.min.css');                # external

        js_file(__DIR__ . '/assets/js/card-slider.js')  ;

        js_file('https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.40/js/uikit.min.js');
        js_file('https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-beta.40/js/uikit-icons.min.js');
        
        return get_view(__DIR__ . '/views/star_rating.php', null);              
    }

    /*
        La estructura de $data es 

        [
            "paginator" => [
                "total"       => {num},  // row_count 
				"count"       => {num},  // number of rows in the current page
				"last_page"   => {num},
				"total_pages" => {num},
				"page_size"   => {num}
            ],
            "rows"      => [

            ]

        ]
    */
    function table(Array $data)
    {
        css_file('third_party/bootstrap/5.x/bootstrap.min.css');
        js_file('third_party/bootstrap/5.x/bootstrap.bundle.min.js');

        ?>
        <div class="container mt-5">
            <h2>Reviews</h2>

            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Comentario</th>
                        <th scope="col">Puntaje</th>
                        <th scope="col">Cliente</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['rows'] as $row) : ?>
                        <tr>
                            <td><?php echo $row['comentario']; ?></td>
                            <td><?php echo $row['puntaje']; ?></td>
                            <td><?php echo $row['cliente']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Paginador -->
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $data['paginator']['total_pages']; $i++) : ?>
                        <li class="page-item"><a class="page-link" href="#"><?php echo $i; ?></a></li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>

    <?php
    }

}