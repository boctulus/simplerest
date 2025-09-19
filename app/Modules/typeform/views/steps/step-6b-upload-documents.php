<!-- Step: Upload Signature -->
<div class="step" data-step="10" data-step-alias="upload-signature" data-conditional="document_types[]:!cards && has_signature:yes">
    <div class="step-content">
        <h2>Sube tu Firma Electrónica</h2>
        <p class="subtitle">Como ya tienes firma electrónica, súbela para el proceso</p>
        
        <div class="form-group" id="signature-upload">
            <label for="signature_file">Archivo de firma electrónica</label>
            <div class="file-upload">
                <input type="file" id="signature_file" name="signature_file" accept=".p12,.pfx" required>
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
