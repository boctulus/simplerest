<!-- Step: Card Commerce Data -->
<div class="step" data-step="7" data-step-alias="commerce-data" data-conditional="has_signature:card_only">
    <div class="step-content">
        <h2>Datos del Comercio</h2>
        <p class="subtitle">Información necesaria para procesar pagos con tarjeta</p>
        
        <div class="form-section">
            <h3>Datos del Comercio</h3>
            
            <div class="form-group">
                <label for="commerce_name">Nombre del comercio</label>
                <input type="text" id="commerce_name" name="commerce_name" placeholder="Mi Tienda Online" required>
            </div>
            
            <div class="form-group">
                <label for="commerce_type">Tipo de comercio</label>
                <select id="commerce_type" name="commerce_type" required>
                    <option value="">Selecciona el tipo de comercio</option>
                    <option value="retail">Retail / Tienda</option>
                    <option value="restaurant">Restaurante</option>
                    <option value="services">Servicios</option>
                    <option value="ecommerce">E-commerce</option>
                    <option value="other">Otro</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="commerce_address">Dirección del comercio</label>
                <input type="text" id="commerce_address" name="commerce_address" placeholder="Calle Principal 123, Santiago" required>
            </div>
            
            <div class="form-group">
                <label for="commerce_phone">Teléfono del comercio</label>
                <div class="phone-input">
                    <select name="commerce_country_code" id="commerce_country_code">
                        <option value="+56">🇨🇱 +56</option>
                        <option value="+1">🇺🇸 +1</option>
                        <option value="+54">🇦🇷 +54</option>
                    </select>
                    <input type="tel" id="commerce_phone" name="commerce_phone" placeholder="912345678" required>
                </div>
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