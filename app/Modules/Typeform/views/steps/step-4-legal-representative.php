<!-- Step: Legal Representative -->
<div class="step" data-step="5" data-step-alias="legal-representative">
    <div class="step-content">
        <h2>Representante Legal</h2>
        <div class="form-group">
            <label for="legal_rep_name">Nombre completo del representante legal</label>
            <input type="text" id="legal_rep_name" name="legal_rep_name" required>
        </div>
        <div class="form-group">
            <label for="legal_rep_rut">RUT del representante legal</label>
            <input type="text" id="legal_rep_rut" name="legal_rep_rut" placeholder="18280886-5" required>
        </div>
        <div class="form-group">
            <label for="legal_rep_email">Email del representante legal</label>
            <input type="email" id="legal_rep_email" name="legal_rep_email" required>
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