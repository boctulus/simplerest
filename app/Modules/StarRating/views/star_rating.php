<?php

use Boctulus\Simplerest\Core\Libs\Strings;

$cfg  = include __DIR__ . '/../config/config.php';


/*
  Vistas para crear nuevos reviews que deben ser aprobados:

  https://codepen.io/ajesamann/pen/ZEYEoZm
  https://codepen.io/alinsimoc/pen/XWjjjbJ

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

.uk-card-title {
  padding-bottom: 20px;
}

.owl-next {
  background: #3286f0;
}

.owl-theme .owl-nav [class*='owl-'] {
  background: #383838;
}

.owl-dots {
  margin-top: 20px !important;
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
  --yellow: #ffc733;
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
  Review summary
*/

.review-ratings a {
  color: #545454;
}

.reviews-summary{
  display: inline-block;
  padding: 5px;
  margin: 0 auto;
  margin-left: 2rem;
}

.reviews-summary_main{
  margin: 0 auto;
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-start;
  align-items: center;
}

.reviews-dropdown{
  padding: 2px 10px;
  cursor: pointer;
}

.reviews-stars_outer {
  display: inline-block;
  position: relative;
  font-family: FontAwesome;
}

.reviews-stars_outer::before {
  content: "\f006 \f006 \f006 \f006 \f006";
}

.reviews-stars_inner {
  position: absolute;
  top: 0;
  left: 0;
  white-space: nowrap;
  overflow: hidden;
  width: 0;
}

.reviews-stars_inner::before {
  content: "\f005 \f005 \f005 \f005 \f005";
  color: var(--yellow);
}


.reviews-count{
  padding: 2px 5px;
  font-weight: bold;
}

.reviews-count.hide{
  display: none;
}

.reviews-summary_details{
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  border-radius: 5px;
  box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
  border: 1px solid #dedede;
  z-index: 1;
}

.reviews-summary_details.hide{
  display: none;
}

.reviews-summary_details.inactive{
  opacity: 0;
}

.reviews-summary_details_title,
.reviews-summary_details_footer{
  padding: 10px;
}

.reviews-summary_details_item{
  position: relative;
  margin: 0 auto;
  padding: 5px 0;
  display: grid;
  grid-template-columns: 1fr 3fr 1fr;
  width: 90%;
}

.reviews-star-bar{
  height: 100%;
  width: 100%;
}

.reviews-star-bar_outer{
  position: relative;
  width: 100%;
  height: 20px;
}

.reviews-star-bar_inner{
  position: relative;
  height: 20px;
  width: 0;
  background: #e67300;
}

.reviews-link {
  text-decoration: underline;
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
    background-color: var(--yellow);
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


.owl-carousel .item .review:hover {
    background-color: #3286f0 !important;
    color: #fff !important;
} 

/* Estilos generales */

.owl-dots {
  margin-top: 20px !important;
}

.owl-dot {
  border: none;
  background-color: transparent;
  margin: 5px;
  padding: 0;
  width: 10px;
  height: 10px;
}

/* Estilos específicos para resoluciones más pequeñas */

@media (max-width: 768px) {
  .owl-dots {
    margin-top: 10px !important;
  }
}

@media (max-width: 576px) {
  .owl-dots {
    display: none; /* Ocultar completamente los dots en resoluciones aún más pequeñas */
  }
}

/* .owl-dot:hover, .owl-dot:focus {
  outline: none !important;
} */

.review {
  background-color: transparent;
  color: #545454;
  height: 240px;
  overflow: hidden; /* Evitar que el contenido se escape */
}

.review__score {
  display: flex;
  align-items: center;
}

.score {
  font-size: 2rem;
}

.score-stars {
  display: flex;
  margin-left: 0.8rem;
}

.score-stars img {
  width: 2rem; /* Ajusta el tamaño de la imagen de la estrella según sea necesario */
}

/* Estilo predeterminado para pantallas más grandes que teléfonos móviles */
.stars-container {
  margin-top: -30px;
}

/* Estilo específico para teléfonos móviles */
@media screen and (max-width: 767px) {
  .stars-container {
    margin-top: 20px;
  }
}
</style>

<h2 class="widget-title">Recensioni</h2>		

<div class="uk-section">
    <div class="owl-carousel owl-theme">
      <?php foreach ($reviews as $review) : 
        
        $date = (new DateTime($review['created_at']))->format('d-m-Y');
      
        #$comment_prepend          = "<small>{$review['author']}</small>";
        $comment_append           = "– <small>{$review['author']}</small>";
        $comment_append_new_line  = "<small>$date</small>";        
      ?>
        
        <div class="item">
        <div class="review uk-card uk-card-hover uk-card-body uk-light"
          style="background-color: transparent; color: #545454; margin-bottom: 0px !important; margin-right: 10px; margin-left: 10px;">
          <?php if (isset($review['title'])): ?>
            <h3 class="review__title uk-card-title">
              <?= $review['title'] ?>
              </h4>
            <?php endif; ?>

            <?php if (isset($comment_prepend)): ?>
            <div class="review__date" style="text-align: center; margin-top: 0px; margin-bottom: 10px">
                <?= $comment_prepend  ?>
            </div>
            <?php endif; ?>

            <div class="review__score">
              <span class="score">
                <?= $review['score']; ?>
              </span>
              <span>&nbsp;/&nbsp;5&nbsp;</span>
              <span class="score-stars">
                <?= str_repeat('⭐', $review['score']); ?>
              </span>
            </div>
            <div class="review__text">"<i>
                <?= Strings::getUpTo($review['comment'], 22, 85); ?>
              </i>" <?= $comment_append ?? '' ?>
            </div>

            <?php if ($comment_append_new_line): ?>
              <div class="review__date" style="text-align: center; margin-top: 20px;">
                  <?= $comment_append_new_line ?>
              </div>
            <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
  </div>
</div>

  
  <div class="stars-container">
    <span style="margin-left: 45px; font-weight:600;"><?= str_repeat('⭐', $avg) . ' '. $count ?> recensioni</span>

    <div class="review-ratings">
    <a class="reviews-link" href="/rating_slider/rating_table">Vedi tutto</a>
   
    <ul class="ratings-list">
      <?php
      // Calcular el total de todas las clasificaciones
      $totalRatings = array_sum($ratings);

      $ratings = array_reverse($ratings, true);

      foreach ($ratings as $stars => $count) :
          // Normalizar la longitud de la barra en relación con el total de las clasificaciones
          $normalizedPercentage = ($count / $totalRatings) * 100;
      ?>
          <li class="counter-container">
              <span class="counter-label"><?= $stars; ?> stelle</span>
              <span class="counter-back">
                  <span class="counter-bar" style="width: <?= $normalizedPercentage; ?>%;"></span>
              </span>
              <span class="counter-count"><?= $count; ?></span>
          </li>
      <?php endforeach; ?>
  </ul>

    
</div>


  </div>


</div>


