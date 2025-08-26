<!-- Step 7: Review and Submit -->
<div class="step" data-step="7">
    <div class="step-content">
        <h2>Revisión Final</h2>
        <p class="subtitle">Por favor revisa la información antes de enviar</p>
        
        <div class="summary-card">
            <h3>Resumen de tu solicitud</h3>
            <div id="form-summary">
                <!-- Summary will be populated by JavaScript -->
            </div>
        </div>

        <div class="form-group">
            <label class="checkbox-label" for="terms_accept">
                <input type="checkbox" id="terms_accept" name="terms_accept" required>
                <span class="checkmark"></span>
                Acepto los <a href="#" target="_blank">términos y condiciones</a> del servicio
            </label>
        </div>

        <div class="step-actions">
            <button type="button" class="btn-secondary" onclick="prevStep()">
                ← Anterior
            </button>
            <button type="submit" class="btn-primary btn-submit">
                Enviar Solicitud
                <span class="btn-arrow">✓</span>
            </button>
        </div>
    </div>
</div>