<style>
  .ore, .minuti, .secondi {
    border: 4px solid red;
    background: #f7f7f7;
    border-radius: 100px;
    width: 100px;
    height: 100px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    box-shadow: 0px 0px 5px #b40202;
  }

  span.hours, span.minutes, span.seconds {
    font-size: 26px !important;
    font-weight: 700;
  }

  span.testo {
    font-size: 12px !important;
  }
</style>

<div class="elementor-widget-container">
   <div id="deliveryCountdown" class="text-center pt-4 mt-2 px-0">
      <span class="text-dark pb-2">Vuoi riceverlo entro <span class="nextDeliveryDate text-capitalize font-weight-bold">jueves 28 diciembre</span>?</span>
      <h6 class="h4 text-dark pb-4">
         Le Spedizioni Partiranno Con [GLS EXPRESS] Tra:
      </h6>
      <div class="container text-center pb-3  px-0">
         <div class="row px-0">
            <div class="col-4 text-center d-flex justify-content-center justify-content-sm-end px-0">
               <div class="ore">
                  <span class="hours">30</span>
                  <span class="testo">ore</span>
               </div>
            </div>
            <div class="col-4 text-center d-flex justify-content-center justify-content-sm-center px-0">
               <div class="minuti">
                  <span class="minutes">00</span>
                  <span class="testo">min</span>
               </div>
            </div>
            <div class="col-4 text-center d-flex justify-content-center justify-content-sm-start px-0">
               <div class="secondi">
                  <span class="seconds">54</span>
                  <span class="testo">sec</span>
               </div>
            </div>
         </div>
      </div>
   </div>   	
</div>