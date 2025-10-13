<div class="typeform-container">
    <!-- Progress bar -->
    <div class="progress-container">
        <div class="progress-bar">
            <div class="progress-fill" id="progressFill"></div>
        </div>
    </div>

    <!-- Form container -->
    <div class="form-wrapper">
        <form id="typeform" method="POST">
            <input type="hidden" id="currentStep" name="step" value="1">
            
            <?= $content ?>
            
        </form>
    </div>
</div>