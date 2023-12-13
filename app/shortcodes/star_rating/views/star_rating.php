<?php

$cfg  = include __DIR__ . '/../config/config.php';


/*
  Credits
  
  https://codepen.io/vcoppolecchia/pen/oaBVEE

*/

?>

<style>

.widget-title {
  margin-left: 2rem;
  margin-top: 1rem;
}

.uk-section {
  margin: 0;
  padding: 0;
}

.owl-carousel {
  position: relative;
  margin-top: 30px;
}

.owl-nav {
  position: absolute;
  top: -60px;
  left: 40px;
}

.uk-card-primary {
  border-radius: 8px;
}

h3 {
  margin-top: 10px
}

.uk-card > :last-child {
  margin-top:0;
  margin-bottom: 10px
}

p {
  margin-top: 30px;
  margin-bottom: 0;
}

.owl-next {
  background: #3286f0;
}

.owl-theme .owl-nav [class*='owl-'] {
  background: #383838;
}

.owl-dots {
  margin-top: -10px !important;
}

.uk-card-title {
  padding-bottom: 20px
}

.owl-dot {
    border: none;  /* Esto elimina el borde de los botones */
    background-color: transparent;  /* Esto asegura que el fondo del botón sea transparente */
    margin: 5px;  /* Esto elimina cualquier margen alrededor del botón */
    padding: 0  px;  /* Esto elimina cualquier relleno dentro del botón */
    width: 10px; /* Ajusta el ancho del botón según tus necesidades */
    height: 10px; /* Ajusta la altura del botón según tus necesidades */
}

/*
  Ratings  
*/

/* Variables */
:root {
  --grey: #bfbfbf;
  --black: #000000;
  --radius-value: 3px;
  --box-max-width: 500px;
  --system-fonts: -apple-system, BlinkMacSystemFont, "Segoe UI",
    Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
}

/* General (not mandatory) rules */
body {
  font-family: var(--system-fonts);
}

/* Review component */
.review { 
  display: inline-block;
  margin: 2rem;
  padding: 1.1rem;
  max-width: var(--box-max-width);
  border-radius: var(--radius-value);
  border: 1px solid var(--grey);
  background-color: #545454;
  box-shadow: 0px 0px 15px rgba(var(--black), 0.25);
  font-family: var(--system-fonts);
}

.review__title {
  margin: 0 0 1rem 0;
  font-size: 1.5rem;
}

.review__score {
  display: flex;
  align-items: center;
  margin-bottom: 0.8rem;
}

.review .score {
  font-size: 2rem;
}

.review .score-stars {
  margin-left: 0.8rem;
}

.review__text {
  line-height: 1.45;
}

/*
  WP reviews
*/

/* Estilos para la lista de calificaciones */
.ratings-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.counter-container {
    margin: 10px 0;
    overflow: hidden; /* Para que la barra no se extienda fuera del contenedor */
}

.counter-label {
    display: inline-block;
    width: 80px;
}

.counter-back {
    display: inline-block;
    background-color: #e0e0e0;
    height: 17px; /* Altura de la barra */
    width: 100px; /* Ancho de la barra */
    margin-left: 0px;
}

.counter-bar {
    display: block;
    height: 100%;
    background-color: yellow;
}

.counter-count {
    margin-left: 5px;
}

.review-ratings {
  margin-left: 2rem;
  margin-top: 1rem;
  padding-left: 1.1rem;
}

.ratings-list a {
    text-decoration: none !important;
}

.owl-carousel {
  padding-top:0;
  margin-top:0;
}
</style>


<h2 class="widget-title">Ratings</h2>		

  <div class="uk-section">
    <div class="owl-carousel owl-theme">

    <div class="item">
        <div class="review uk-card uk-card-primary uk-card-hover uk-card-body uk-light">
            <h3 class="review__title uk-card-title">Look ma, a review title!</h4>
            <div class="review__score">
              <span class="score">4.9</span>
              <span>&nbsp;/&nbsp;5&nbsp;</span>
              <span class="score-stars">⭐⭐⭐⭐⭐</span>
            </div>
            <div class="review__text">"<i>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nemo quam eveniet harum perferendis facere blanditiis molestias sit omnis, fugit, amet enim error eius aperiam dolorum autem nam voluptatibus velit. Inventore!</i>" – <small>John Green</small></div>
        </div>        
    </div>

    <div class="item">
        <div class="review uk-card uk-card-primary uk-card-hover uk-card-body uk-light">
            <h3 class="review__title uk-card-title">Look ma, a review title!</h4>
            <div class="review__score">
              <span class="score">4.9</span>
              <span>&nbsp;/&nbsp;5&nbsp;</span>
              <span class="score-stars">⭐⭐⭐⭐⭐</span>
            </div>
            <div class="review__text">"<i>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nemo quam eveniet harum perferendis facere blanditiis molestias sit omnis, fugit, amet enim error eius aperiam dolorum autem nam voluptatibus velit. Inventore!</i>" – <small>John Green</small></div>
        </div>        
    </div>

    <div class="item">
        <div class="review uk-card uk-card-primary uk-card-hover uk-card-body uk-light">
            <h3 class="review__title uk-card-title">Look ma, a review title!</h4>
            <div class="review__score">
              <span class="score">4.9</span>
              <span>&nbsp;/&nbsp;5&nbsp;</span>
              <span class="score-stars">⭐⭐⭐⭐⭐</span>
            </div>
            <div class="review__text">"<i>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nemo quam eveniet harum perferendis facere blanditiis molestias sit omnis, fugit, amet enim error eius aperiam dolorum autem nam voluptatibus velit. Inventore!</i>" – <small>John Green</small></div>
        </div>        
    </div>


    <div class="item">
        <div class="review uk-card uk-card-primary uk-card-hover uk-card-body uk-light">
            <h3 class="review__title uk-card-title">Look ma, a review title!</h4>
            <div class="review__score">
              <span class="score">4.9</span>
              <span>&nbsp;/&nbsp;5&nbsp;</span>
              <span class="score-stars">⭐⭐⭐⭐⭐</span>
            </div>
            <div class="review__text">"<i>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nemo quam eveniet harum perferendis facere blanditiis molestias sit omnis, fugit, amet enim error eius aperiam dolorum autem nam voluptatibus velit. Inventore!</i>" – <small>John Green</small></div>
        </div>        
    </div>
    
  </div>


    <div class="review-ratings">
      <a class="reviews-link" href="https://wordpress.org/support/plugin/ultimate-reviews/reviews/">See all</a>

            <div class="rating">
              <div class="wporg-ratings" aria-label="4 out of 5 stars" data-title-template="%s out of 5 stars" data-rating="4" style="color: rgb(255, 185, 0); --darkreader-inline-color: #ffcd16;" data-darkreader-inline-color=""><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-empty"></span></div>			</div>

            <ul class="ratings-list">
                        <li class="counter-container">
                  <a href="https://wordpress.org/support/plugin/ultimate-reviews/reviews/?filter=5">
                    <span class="counter-label">5 stars</span>
                <span class="counter-back">
                  <span class="counter-bar" style="width: 65.51724137931%;"></span>
                </span>
                    <span class="counter-count">38</span>
                  </a>
                </li>
                        <li class="counter-container">
                  <a href="https://wordpress.org/support/plugin/ultimate-reviews/reviews/?filter=4">
                    <span class="counter-label">4 stars</span>
                <span class="counter-back">
                  <span class="counter-bar" style="width: 10.344827586207%;"></span>
                </span>
                    <span class="counter-count">6</span>
                  </a>
                </li>
                        <li class="counter-container">
                  <a href="https://wordpress.org/support/plugin/ultimate-reviews/reviews/?filter=3">
                    <span class="counter-label">3 stars</span>
                <span class="counter-back">
                  <span class="counter-bar" style="width: 3.448275862069%;"></span>
                </span>
                    <span class="counter-count">2</span>
                  </a>
                </li>
                        <li class="counter-container">
                  <a href="https://wordpress.org/support/plugin/ultimate-reviews/reviews/?filter=2">
                    <span class="counter-label">2 stars</span>
                <span class="counter-back">
                  <span class="counter-bar" style="width: 1.7241379310345%;"></span>
                </span>
                    <span class="counter-count">1</span>
                  </a>
                </li>
                        <li class="counter-container">
                  <a href="https://wordpress.org/support/plugin/ultimate-reviews/reviews/?filter=1">
                    <span class="counter-label">1 star</span>
                <span class="counter-back">
                  <span class="counter-bar" style="width: 18.965517241379%;"></span>
                </span>
                    <span class="counter-count">11</span>
                  </a>
                </li>
              </ul>          
    </div>



</div>

