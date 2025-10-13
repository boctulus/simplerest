<!-- Step: Business Info -->
<div class="step" data-step="3" data-step-alias="business-info">
    <div class="step-content">
        <h2>Información de tu empresa</h2>
        <div class="form-group">
            <label for="business_name">Razón Social</label>
            <input type="text" id="business_name" name="business_name" required>
        </div>
        <div class="form-group">
            <label for="rut">RUT de la empresa</label>
            <input type="text" id="rut" name="rut" placeholder="18280886-5" required>
        </div>
        <div class="form-group">
            <label for="business_fantasy_name">Nombre de fantasía</label>
            <input type="text" id="business_fantasy_name" name="business_fantasy_name" required>
        </div>
        <div class="form-group">
            <label for="business_sector">Giro principal</label>
            <input type="text" id="business_sector" name="business_sector" required>
        </div>
        <div class="form-group">
            <label for="business_address">Dirección comercial</label>
            <input type="text" id="business_address" name="business_address">
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
        <div class="form-group">
            <label for="business_email">Email de la empresa</label>
            <input type="email" id="business_email" name="business_email" required>
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