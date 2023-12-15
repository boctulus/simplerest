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
</style>


<h2 class="widget-title">Ratings</h2>		

  <div class="uk-section">
    <div class="owl-carousel owl-theme">

    <?php foreach ($reviews as $review) : ?>
        <div class="item">
            <div class="review uk-card uk-card-primary uk-card-hover uk-card-body uk-light">
                <?php if (isset($review['title'])): ?>
                  <h3 class="review__title uk-card-title"><?= $review['title'] ?></h4>
                <?php endif; ?>
                <div class="review__score">
                    <span class="score"><?= $review['score']; ?></span>
                    <span>&nbsp;/&nbsp;5&nbsp;</span>
                    <span class="score-stars"><?= str_repeat('⭐', $review['score']); ?></span>
                </div>
                <div class="review__text">"<i><?= $review['comment']; ?></i>" – <small><?= $review['author']; ?></small></div>
            </div>
        </div>
    <?php endforeach; ?>
    
  </div>

  <!-- reviews-summary -->
  <div class="reviews-summary">
      <div class="reviews-summary_main">
        <div class="reviews-dropdown js-reviews-dropdown">
          <div class="reviews-stars">
            <div class="reviews-stars_outer">
              <div class="reviews-stars_inner js-reviews-stars_inner" style="width: 90%;"></div>
            </div>
          </div>
        </div>
        <span class="reviews-count js-review-count"><?= $count ?> reviews</span>
      </div>
      <div class="reviews-summary_details js-reviews-details hide">
        <span class="reviews-summary_details_title js-reviews-details-title">4.5 out of 5</span>
        <div class="reviews-summary_details_item">
          <span class="reviews-star-id">5 star</span>
          <div class="reviews-star-bar">
            <div class="reviews-star-bar_outer">
              <div class="reviews-star-bar_inner js-reviews-star-bar_inner" data-star-id="1" style="width: 80%;"></div>
            </div>
          </div>
          <span class="reviews-star-percent js-reviews-star-percent">80%</span>
        </div>
        <div class="reviews-summary_details_item">
          <span class="reviews-star-id">4 star</span>
          <div class="reviews-star-bar">
            <div class="reviews-star-bar_outer">
              <div class="reviews-star-bar_inner js-reviews-star-bar_inner" data-star-id="2" style="width: 10%;"></div>
            </div>
          </div>
          <span class="reviews-star-percent js-reviews-star-percent">10%</span>
        </div>
        <div class="reviews-summary_details_item">
          <span class="reviews-star-id">3 star</span>
          <div class="reviews-star-bar">
            <div class="reviews-star-bar_outer">
              <div class="reviews-star-bar_inner js-reviews-star-bar_inner" data-star-id="3" style="width: 5%;"></div>
            </div>
          </div>
          <span class="reviews-star-percent js-reviews-star-percent">5%</span>
        </div>
        <div class="reviews-summary_details_item">
          <span class="reviews-star-id">2 star</span>
          <div class="reviews-star-bar">
            <div class="reviews-star-bar_outer">
              <div class="reviews-star-bar_inner js-reviews-star-bar_inner" data-star-id="4" style="width: 3%;"></div>
            </div>
          </div>
          <span class="reviews-star-percent js-reviews-star-percent">3%</span></div>
       
    
      </div>
  </div>


    <div class="review-ratings">
      <a class="reviews-link" href="#">See all</a>

            <div class="rating">
              <div class="wporg-ratings" aria-label="4 out of 5 stars" data-title-template="%s out of 5 stars" data-rating="4" style="color: rgb(255, 185, 0); --darkreader-inline-color: #ffcd16;" data-darkreader-inline-color=""><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-empty"></span></div>			</div>

              <ul class="ratings-list">
                <?php foreach ($ratings as $rating) : ?>
                    <li class="counter-container">
                        <a href="<?= $rating['url']; ?>">
                            <span class="counter-label"><?= $rating['label']; ?></span>
                            <span class="counter-back">
                                <span class="counter-bar" style="width: <?= $rating['percentage']; ?>%;"></span>
                            </span>
                            <span class="counter-count"><?= $rating['count']; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>      
    </div>


</div>

