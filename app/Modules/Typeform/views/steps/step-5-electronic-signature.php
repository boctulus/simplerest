<!-- Step: Electronic Signature -->
<div class="step" data-step="6" data-step-alias="electronic-signature" data-conditional="document_types[]:!cards">
    <div class="step-content">
        <h2>Firma Electrónica</h2>
        <p class="subtitle">¿Ya tienes firma electrónica avanzada?</p>
        <div class="options-grid">
            <label class="option-card" for="has_signature_yes">
                <input type="radio" id="has_signature_yes" name="has_signature" value="yes">
                <div class="card-content">
                    <div class="card-icon">✅</div>
                    <h3>Sí, tengo firma electrónica</h3>
                    <p>Ya cuento con firma electrónica avanzada</p>
                </div>
            </label>
            <label class="option-card" for="has_signature_no">
                <input type="radio" id="has_signature_no" name="has_signature" value="no">
                <div class="card-content">
                    <div class="card-icon">❌</div>
                    <h3>No. Necesito obtenerla</h3>
                    <p>Necesito ayuda para obtener firma electrónica</p>
                </div>
            </label>
        </div>
        <div class="step-actions">
            <button type="button" class="btn-secondary" onclick="prevStep()">
                ← Anterior
            </button>
            <button type="button" class="btn-primary" onclick="nextStep()">
                Continuar →
            </button>
        </div>
    </div>
</div>