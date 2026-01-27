<?php

use Boctulus\Simplerest\Core\Libs\HtmlBuilder\Bt5Form;
use Boctulus\Simplerest\Core\Libs\HtmlBuilder\Tag;


Tag::registerBuilder(\Boctulus\Simplerest\Core\Libs\HtmlBuilder\Html::class);

Bt5Form::setIdAsName();

/*
    Bootstrap 5 validation

    https://getbootstrap.com/docs/5.0/forms/validation/
*/

js_file('js/bootstrap/bt_validation.js');

?>

<h3>Bt5 Form validation</h3>

<div class="row mt-5">
    <div class="col-6 offset-3">

    <form class="row g-3 needs-validation" novalidate>
        <div class="col-md-4 position-relative">
            <label for="validationTooltip01" class="form-label">First name</label>
            <input type="text" class="form-control" id="validationTooltip01" value="Mark" required>
            <div class="valid-tooltip">
            Looks good!
            </div>
        </div>
        <div class="col-md-4 position-relative">
            <label for="validationTooltip02" class="form-label">Last name</label>
            <input type="text" class="form-control" id="validationTooltip02" value="Otto" required>
            <div class="valid-tooltip">
            Looks good!
            </div>
        </div>
        <div class="col-md-4 position-relative">
            <label for="validationTooltipUsername" class="form-label">Username</label>
            <div class="input-group has-validation">
            <span class="input-group-text" id="validationTooltipUsernamePrepend">@</span>
            <input type="text" class="form-control" id="validationTooltipUsername" aria-describedby="validationTooltipUsernamePrepend" required>
            <div class="invalid-tooltip">
                Please choose a unique and valid username.
            </div>
            </div>
        </div>
        <div class="col-md-6 position-relative">
            <label for="validationTooltip03" class="form-label">City</label>
            <input type="text" class="form-control" id="validationTooltip03" required>
            <div class="invalid-tooltip">
            Please provide a valid city.
            </div>
        </div>
        <div class="col-md-3 position-relative">
            <label for="validationTooltip04" class="form-label">State</label>
            <select class="form-select" id="validationTooltip04" required>
            <option selected disabled value="">Choose...</option>
            <option>...</option>
            </select>
            <div class="invalid-tooltip">
            Please select a valid state.
            </div>
        </div>
        <div class="col-md-3 position-relative">
            <label for="validationTooltip05" class="form-label">Zip</label>
            <input type="text" class="form-control" id="validationTooltip05" required>
            <div class="invalid-tooltip">
            Please provide a valid zip.
            </div>
        </div>
        <div class="col-12">
            <button class="btn btn-primary" type="submit">Submit form</button>
        </div>
    </form>

    </div>
</div>
