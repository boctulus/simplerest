<!-- Step: Document Types -->
<div class="step" data-step="2" data-step-alias="document-types">
    <div class="step-content">
        <h2>¿Qué tipos de documentos tributarios necesitas habilitar?</h2>
        <div class="options-grid">
            <label class="option-card" for="invoices">
                <input type="checkbox" id="invoices" name="document_types[]" value="invoices">
                <div class="card-content">
                    <div class="card-icon">📄</div>
                    <h3>Facturas</h3>
                    <p>Facturas electrónicas para ventas</p>
                </div>
            </label>
            <label class="option-card" for="receipts">
                <input type="checkbox" id="receipts" name="document_types[]" value="receipts">
                <div class="card-content">
                    <div class="card-icon">🧾</div>
                    <h3>Boletas</h3>
                    <p>Boletas electrónicas para consumidores finales</p>
                </div>
            </label>
            <label class="option-card" for="cards">
                <input type="checkbox" id="cards" name="document_types[]" value="cards">
                <div class="card-content">
                    <div class="card-icon">📄</div>
                    <h3>Tarjetas solamente</h3>
                    <p>Solo tarjetas</p>
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