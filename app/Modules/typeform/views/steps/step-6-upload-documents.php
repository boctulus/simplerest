<!-- Step: Upload Documents -->
<div class="step" data-step="9" data-step-alias="upload-documents" data-conditional="document_types[]:!cards && has_signature:no">
    <div class="step-content">
        <h2>Documentos requeridos</h2>
        <p class="subtitle">Sube los documentos necesarios para el proceso</p>
        
        <div class="form-group">
            <label for="id_document_front">Cédula de Identidad (frente)</label>
            <div class="file-upload">
                <input type="file" id="id_document_front" name="id_document_front" accept=".pdf,.jpg,.jpeg,.png" required>
                <div class="file-upload-content">
                    <div class="file-icon">📎</div>
                    <p>Arrastra tu archivo aquí o haz clic para seleccionar</p>
                    <span class="file-types">PDF, JPG, PNG - Máximo 5MB</span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="id_document_reverse">Cédula de Identidad (reverso)</label>
            <div class="file-upload">
                <input type="file" id="id_document_reverse" name="id_document_reverse" accept=".pdf,.jpg,.jpeg,.png" required>
                <div class="file-upload-content">
                    <div class="file-icon">📎</div>
                    <p>Arrastra tu archivo aquí o haz clic para seleccionar</p>
                    <span class="file-types">PDF, JPG, PNG - Máximo 5MB</span>
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
