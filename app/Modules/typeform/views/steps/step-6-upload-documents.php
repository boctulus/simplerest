<!-- Step 6: Upload Documents -->
<div class="step" data-step="6">
    <div class="step-content">
        <h2>Documentos requeridos</h2>
        <p class="subtitle">Sube los documentos necesarios para el proceso</p>
        
        <div class="form-group">
            <label for="id_document">Cédula de Identidad (ambas caras)</label>
            <div class="file-upload">
                <input type="file" id="id_document" name="id_document" accept=".pdf,.jpg,.jpeg,.png" required>
                <div class="file-upload-content">
                    <div class="file-icon">📎</div>
                    <p>Arrastra tu archivo aquí o haz clic para seleccionar</p>
                    <span class="file-types">PDF, JPG, PNG - Máximo 5MB</span>
                </div>
            </div>
        </div>

        <div class="form-group conditional-field" id="signature-upload" style="display: none;">
            <label for="signature_file">Archivo de firma electrónica</label>
            <div class="file-upload">
                <input type="file" id="signature_file" name="signature_file" accept=".p12,.pfx">
                <div class="file-upload-content">
                    <div class="file-icon">🔒</div>
                    <p>Sube tu archivo de firma (.p12 o .pfx)</p>
                    <span class="file-types">P12, PFX - Máximo 2MB</span>
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