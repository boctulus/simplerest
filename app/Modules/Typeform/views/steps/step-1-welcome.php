<!-- Step 1: Welcome with Split Layout -->
<div class="step active step-1-split" data-step="1" data-step-alias="welcome">
    <div class="split-container">
        <!-- Panel izquierdo con imagen -->
        <div class="left-panel" style="background-image: url('<?= $background_image ?>')">
            <div class="left-panel-content">
                <div class="brand-content">
                    <h2 class="brand-title"><?= htmlspecialchars($brand_title) ?></h2>
                    <p class="brand-subtitle"><?= htmlspecialchars($brand_subtitle) ?></p>
                </div>
            </div>
        </div>
        
        <!-- 
            Panel derecho con formulario 
        -->
        <div class="right-panel">
            <div class="step-content">
                <h1>¡Activa documentos electrónicos y tu máquina fácil y rápido!</h1>
                <p class="subtitle">Te ayudaremos con la activación paso a paso</p>
                <div class="step-actions">
                    <button type="button" class="btn-primary" onclick="nextStep()">
                        Comenzar
                        <span class="btn-arrow">→</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>