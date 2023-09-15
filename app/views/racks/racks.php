<?php
  
  $cfg  = config();

  $dims = $cfg['dims'];
?>

<div class="row">
  <div class="main-form">
    <!---->
    <ul class="list-step" >
      <li   class="active">
        <a aria-controls="step-01"  >Rack Dimensions</a>
      </li>
      <li>
        <a aria-controls="step-02"  >Decking Options</a>
      </li>
      <!---->
      <li>
        <a aria-controls="step-03"  >Space Availability</a>
      </li>
      <!---->
      <!---->
      <li   >
        <a aria-controls="step-04"  >Aisle Dimensions</a>
      </li>
      <!---->
      <!---->
      <!---->
    </ul>
    <!---->
    <div class="tab-content">
      <div id="step-01" class="active tab-pane clearfix">
        <div class="-item">
          <div class="form-group clearfix">
            <label class="control-label col-sm-4">
              <span >Upright Height</span>: </label>
            <div class="col-sm-8 select-wrapper -secondary">
              <select class="form-control ">
                <?php foreach ($dims['h'] as $e): ?>
                  <option label="<?= $e ?>&quot;" value="<?= $e ?>"><?= $e ?>"</option>
                <?php endforeach; ?>
              </select>
              <i class="fa fa-angle-down"></i>
            </div>
          </div>
          <div class="form-group clearfix">
            <label class="control-label col-sm-4">
              <span >Upright Depth</span>: </label>
            <div class="col-sm-8 select-wrapper -secondary">
              <select class="form-control ">
                <?php foreach ($dims['d'] as $e): ?>
                  <option label="<?= $e ?>&quot;" value="<?= $e ?>"><?= $e ?>"</option>
                <?php endforeach; ?>
              </select>
              <i class="fa fa-angle-down"></i>
            </div>
          </div>
          <div class="form-group clearfix">
            <label class="control-label col-sm-4">
              <span >Beam Length</span>: </label>
            <div class="col-sm-8 select-wrapper">
              <select class="form-control ">
              <?php foreach ($dims['l'] as $e): ?>
                  <option label="<?= $e ?>&quot;" value="<?= $e ?>"><?= $e ?>" Long</option>
                <?php endforeach; ?>
              </select>
              <i class="fa fa-angle-down"></i>
            </div>
          </div>
          <div class="form-group clearfix">
            <label class="control-label col-sm-4">
              <span>Beam Levels: <br>
                <span style="font-size: small" >(not including floor)</span>
              </span>
            </label>
            <div class="col-sm-8">
              <div class="check-wrapper">
                <?php foreach (range(2,$dims['max_levels']) as $level): ?>
                <!---->
                <label class="check-default line">
                  <input type="radio" name="selected-level" value="<?= $level ?>"  class="ng-pristine ">
                  <span ><?= $level ?></span>
                </label>
                <!---->
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
        <div class="-item -img">
          <!---->
          <img src="<?= asset('racks/images/NewPalletRack.png') ?>">
          <!---->
          <p class="-img-caption" >4 Beam Levels in diagram</p>
        </div>
      </div>
      <div id="step-02" class="active tab-pane clearfix d-none">
        <div class="clearfix row">
          <div class="-item-tab-half col-md-6">
            <div class="-caption" >
              <h4 >Do you want wire decking?</h4>
              <div class="check-wrapper">
                <label for="wireDeckingY" class="check-default">
                  <input type="radio" name="wireDecking" id="wireDeckingY"   class="ng-pristine " value="true">
                  <span >Yes</span>
                </label>
                <label for="wireDeckingN" class="check-default">
                  <input type="radio" name="wireDecking" id="wireDeckingN"   class="ng-pristine " value="false">
                  <span >No</span>
                </label>
              </div>
            </div>
            <div class="-img" >
              <img src="<?= asset('racks/images/tab2-img01.png') ?>">
            </div>
          </div>
          <div class="-item-tab-half col-md-6">
            <div class="-caption d-none">
              <h4 >Do you want pallet supports?</h4>
              <div class="radio-wrapper">
                <label for="palletSupportsY" class="check-default">
                  <input type="radio" name="palletSupports" id="palletSupportsY"   class="ng-pristine " value="true">
                  <span >Yes</span>
                </label>
                <label for="palletSupportsN" class="check-default">
                  <input type="radio" name="palletSupports" id="palletSupportsN"   class="ng-pristine " value="false">
                  <span >No</span>
                </label>
              </div>
            </div>
            <div class="-img d-none">
              <img src="<?= asset('racks/images/tab2-img02.png') ?>">
            </div>
          </div>
        </div>
      </div>
      <div id="step-03" class="active clearfix tab-pane text-center d-none">
        <!---->
        <!---->
        <!---->
        <h4  >What is the length and width of the space where you want pallet rack?</h4>
        <!---->
        <div class="-img centered">
          <!---->
          <img src="<?= asset('racks/images/multiple.png') ?>" >
          <!---->
          <!---->
          <!---->
        </div>
        <form role="form" name="areaForm" class="form-material ng-valid-maxlength ng-valid-required" style="margin-top: 50px;">
          <div class="clearfix">
            <div class="form-inline">
              <div class="form-group -custom validation-group">
                <label for="length" class="-primary" >Length</label>
                <input type="text" id="length" name="length" class="form-control ng-valid-maxlength ng-valid-required"  valid-number=""   placeholder="feet">
                <div class="text-right " >
                  <!---->
                </div>
              </div>
              <!---->
              <div  class="form-group -custom validation-group">
                <label for="width" class="-secondary" >Width</label>
                <input type="text" id="width" name="width" class="form-control ng-valid-maxlength ng-valid-required"  valid-number=""   placeholder="feet">
                <div class="text-right " >
                  <!---->
                </div>
              </div>
              <!---->
            </div>
          </div>
        </form>
      </div>
      <!---->
    </div>
  </div>
  <div class="main-form-btn-group">
    <button id="tabPrev" class="btn btn-primary -prev no-animate d-none">
      <i class="fa fa-angle-left"></i>
      <span >Back</span>
    </button>
    <button id="tabNext" class="btn btn-primary -next"  >
      <span>Next</span>
      <span class="d-none">View Drawing</span>
      <i class="fa fa-angle-right"></i>
    </button>
  </div>
  <!---->
</div>

<script>
  const show = (selector) => {
    $(selector).removeClass('d-none');
  }

  const hide = (selector) => {
    $(selector).addClass('d-none');
  }

  const showPrevBtn = () => {
    show('#tabPrev');
  }

  const hidePrevBtn = () => {
    show('#tabPrev');
  }

  const disableNextBtn = () => {
    $('#tabNext').prop('disabled', true);
  }

  const enableNextBtn = () => {
    $('#tabNext').prop('disabled', false);
  }

  document.addEventListener("DOMContentLoaded", function() {
    
    // Inicializar el primer paso como activo
    $('.list-step li:first').addClass('active');
    $('.tab-pane:first').addClass('active');

    // Manejar el evento de clic en los elementos de la lista de pasos
    $('.list-step li').click(function() {
      $('.list-step li').removeClass('active');
      $(this).addClass('active');

      var target = $(this).find('a').attr('href');
      $('.tab-pane').removeClass('active');
      $(target).addClass('active');
    });

    // Manejar el evento de clic en los botones de navegación
    $('#tabPrev').click(function() {
      var activeStep = $('.list-step li.active');
      var prevStep = activeStep.prev();

      if (prevStep.length > 0) {
        activeStep.removeClass('active');
        prevStep.addClass('active');

        var target = prevStep.find('a').attr('href');
        $('.tab-pane').removeClass('active');
        $(target).addClass('active');
      }
    });

    $('#tabNext').click(function() {
      var activeStep = $('.list-step li.active');
      var nextStep = activeStep.next();

      if (nextStep.length > 0) {
        activeStep.removeClass('active');
        nextStep.addClass('active');

        var target = nextStep.find('a').attr('href');
        $('.tab-pane').removeClass('active');
        $(target).addClass('active');
      }
    });

});

</script>