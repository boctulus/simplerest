<!-- Step 3: Business Info -->
<div class="step" data-step="3">
    <div class="step-content">
        <h2>Información de tu empresa</h2>
        <div class="form-group">
            <label for="business_name">Razón Social</label>
            <input type="text" id="business_name" name="business_name" required>
        </div>
        <div class="form-group">
            <label for="rut">RUT de la empresa</label>
            <input type="text" id="rut" name="rut" placeholder="12.345.678-9" required>
        </div>
        <div class="form-group">
            <label for="business_phone">Teléfono de la empresa</label>
            <div class="phone-input">
                <select name="country_code" id="country_code">
                    <option value="+56">🇨🇱 +56</option>
                    <option value="+1">🇺🇸 +1</option>
                    <option value="+54">🇦🇷 +54</option>
                </select>
                <input type="tel" id="business_phone" name="business_phone" placeholder="912345678" required>
            </div>
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