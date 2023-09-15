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
              <select   class="form-control    ">
                <option label="96&quot;" value="object:244">96"</option>
                <option label="120&quot;" value="object:245">120"</option>
                <option label="144&quot;" value="object:243" selected="selected">144"</option>
                <option label="192&quot;" value="object:246">192"</option>
                <option label="240&quot;" value="object:247">240"</option>
              </select>
              <i class="fa fa-angle-down"></i>
            </div>
          </div>
          <div class="form-group clearfix">
            <label class="control-label col-sm-4">
              <span >Upright Depth</span>: </label>
            <div class="col-sm-8 select-wrapper -secondary">
              <select   class="form-control    ">
                <option label="36&quot;" value="object:248" selected="selected">36"</option>
                <option label="42&quot;" value="object:249">42"</option>
                <option label="48&quot;" value="object:250">48"</option>
              </select>
              <i class="fa fa-angle-down"></i>
            </div>
          </div>
          <div class="form-group clearfix">
            <label class="control-label col-sm-4">
              <span >Beam Length</span>: </label>
            <div class="col-sm-8 select-wrapper">
              <select   class="form-control    ">
                <option label="96&quot; Long" value="object:251" selected="selected">96" Long</option>
                <option label="108&quot; Long" value="object:252">108" Long</option>
                <option label="120&quot; Long" value="object:253">120" Long</option>
                <option label="144&quot; Long" value="object:254">144" Long</option>
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
                <!---->
                <label class="check-default line">
                  <input type="radio" name="selected-level" value="2">
                  <span >2</span>
                </label>
                <!---->
                <label class="check-default line" >
                  <input type="radio" name="selected-level" value="3">
                  <span >3</span>
                </label>
                <!---->
                <label class="check-default line" >
                  <input type="radio" name="selected-level" value="4">
                  <span >4</span>
                </label>
                <!---->
                <label class="check-default line" >
                  <input type="radio" name="selected-level" value="5">
                  <span >5</span>
                </label>
                <!---->
                <label class="check-default line" >
                  <input type="radio" name="selected-level" value="6">
                  <span >6</span>
                </label>
                <!---->
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
                  <input type="radio" name="wireDecking" id="wireDeckingY" value="true">
                  <span >Yes</span>
                </label>
                <label for="wireDeckingN" class="check-default">
                  <input type="radio" name="wireDecking" id="wireDeckingN" value="false">
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
                  <input type="radio" name="palletSupports" id="palletSupportsY" value="true">
                  <span >Yes</span>
                </label>
                <label for="palletSupportsN" class="check-default">
                  <input type="radio" name="palletSupports" id="palletSupportsN" value="false">
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
        <form role="form" name="areaForm" class="form-material  -maxlength  -required" style="margin-top: 50px;">
          <div class="clearfix">
            <div class="form-inline">
              <div class="form-group -custom validation-group">
                <label for="length" class="-primary" >Length</label>
                <input type="text" id="length" name="length" class="form-control   -maxlength   -required"  valid-number=""   placeholder="feet">
                <div class="text-right " >
                  <!---->
                </div>
              </div>
              <!---->
              <div  class="form-group -custom validation-group">
                <label for="width" class="-secondary" >Width</label>
                <input type="text" id="width" name="width" class="form-control   -maxlength   -required"  valid-number=""   placeholder="feet">
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
      <span   class="d-none">View Drawing</span>
      <i class="fa fa-angle-right"></i>
    </button>
  </div>
  <!---->
</div>