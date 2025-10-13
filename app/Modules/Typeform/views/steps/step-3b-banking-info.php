<!-- Step: Banking Info -->
<div class="step" data-step="4" data-step-alias="banking-info">
    <div class="step-content">
        <h2>Datos Bancarios</h2>
        <p class="subtitle">Información necesaria para procesar los pagos</p>
        
        <div class="form-group">
            <label for="bank_name">Banco</label>
            <select id="bank_name" name="bank_name" required>
                <option value="">Seleccione su banco</option>
                <option value="banco_chile">Banco de Chile</option>
                <option value="banco_bci">Banco BCI</option>
                <option value="banco_santander">Banco Santander</option>
                <option value="banco_estado">Banco Estado</option>
                <option value="banco_falabella">Banco Falabella</option>
                <option value="banco_itau">Banco Itaú</option>
                <option value="banco_security">Banco Security</option>
                <option value="scotiabank">Scotiabank</option>
                <option value="banco_consorcio">Banco Consorcio</option>
                <option value="otro">Otro</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="account_holder_name">Nombre Titular *</label>
            <input type="text" id="account_holder_name" name="account_holder_name" required>
        </div>
        
        <div class="form-group">
            <label for="account_type">Tipo de cuenta *</label>
            <select id="account_type" name="account_type" required>
                <option value="">Seleccione tipo de cuenta</option>
                <option value="cuenta_corriente">Cuenta Corriente</option>
                <option value="cuenta_ahorro">Cuenta de Ahorro</option>
                <option value="cuenta_vista">Cuenta Vista</option>
                <option value="cuenta_rut">Cuenta RUT</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="account_number">Número de cuenta *</label>
            <input type="text" id="account_number" name="account_number" placeholder="Ingrese el número de cuenta" required>
        </div>

        <div class="form-group">
            <label for="sales_rep_name">Nombre Vendedor (opcional)</label>
            <input type="text" id="sales_rep_name" name="sales_rep_name" placeholder="Solo si fue atendido por uno">
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