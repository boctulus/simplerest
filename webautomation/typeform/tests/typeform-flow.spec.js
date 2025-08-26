const { test, expect } = require('@playwright/test');

test.describe('Typeform Multi-step Flow', () => {
  const TYPEFORM_URL = '/typeform';
  
  test.beforeEach(async ({ page }) => {
    await page.goto(TYPEFORM_URL);
  });

  test('should display welcome step initially', async ({ page }) => {
    // Check if welcome step is visible
    await expect(page.locator('[data-step="1"]')).toBeVisible();
    await expect(page.locator('h1')).toContainText('¡Bienvenido!');
    
    // Check if progress bar is initialized
    await expect(page.locator('#progressFill')).toBeVisible();
    
    // Check if "Comenzar" button is present
    await expect(page.locator('button:has-text("Comenzar")')).toBeVisible();
  });

  test('should navigate through all steps sequentially', async ({ page }) => {
    // Step 1: Welcome
    await page.click('button:has-text("Comenzar")');
    
    // Step 2: Document Types
    await expect(page.locator('[data-step="2"]')).toBeVisible();
    await expect(page.locator('h2')).toContainText('¿Qué tipos de documentos tributarios');
    
    // Select document types
    await page.check('#invoices');
    await page.check('#receipts');
    await page.click('button:has-text("Continuar")');
    
    // Step 3: Business Info
    await expect(page.locator('[data-step="3"]')).toBeVisible();
    await expect(page.locator('h2')).toContainText('Información de tu empresa');
    
    // Fill business information
    await page.fill('#business_name', 'Test Company S.A.');
    await page.fill('#rut', '12.345.678-9');
    await page.fill('#business_phone', '912345678');
    await page.click('button:has-text("Continuar")');
    
    // Step 4: Legal Representative
    await expect(page.locator('[data-step="4"]')).toBeVisible();
    await expect(page.locator('h2')).toContainText('Representante Legal');
    
    // Fill legal representative info
    await page.fill('#legal_rep_name', 'Juan Pérez González');
    await page.fill('#legal_rep_rut', '98.765.432-1');
    await page.fill('#legal_rep_email', 'juan.perez@testcompany.cl');
    await page.click('button:has-text("Continuar")');
    
    // Step 5: Electronic Signature
    await expect(page.locator('[data-step="5"]')).toBeVisible();
    await expect(page.locator('h2')).toContainText('Firma Electrónica');
    
    // Select signature option
    await page.check('#has_signature_yes');
    await page.click('button:has-text("Continuar")');
    
    // Step 6: Upload Documents
    await expect(page.locator('[data-step="6"]')).toBeVisible();
    await expect(page.locator('h2')).toContainText('Documentos requeridos');
    
    // Skip file upload for now and continue
    await page.click('button:has-text("Continuar")');
    
    // Step 7: Review
    await expect(page.locator('[data-step="7"]')).toBeVisible();
    await expect(page.locator('h2')).toContainText('Revisión Final');
    
    // Check if form summary contains our data
    await expect(page.locator('#form-summary')).toContainText('Test Company S.A.');
    await expect(page.locator('#form-summary')).toContainText('Juan Pérez González');
  });

  test('should validate required fields', async ({ page }) => {
    // Go to step 2
    await page.click('button:has-text("Comenzar")');
    
    // Try to continue without selecting document types
    await page.click('button:has-text("Continuar")');
    
    // Should show error (assuming alert is used)
    page.on('dialog', async dialog => {
      expect(dialog.message()).toContain('Por favor selecciona al menos un tipo de documento');
      await dialog.accept();
    });
    
    // Should remain on step 2
    await expect(page.locator('[data-step="2"]')).toBeVisible();
  });

  test('should handle RUT formatting', async ({ page }) => {
    // Navigate to business info step
    await page.click('button:has-text("Comenzar")');
    await page.check('#invoices');
    await page.click('button:has-text("Continuar")');
    
    // Type RUT without formatting
    await page.fill('#rut', '123456789');
    await page.blur('#rut');
    
    // Check if RUT is formatted correctly
    const rutValue = await page.inputValue('#rut');
    expect(rutValue).toMatch(/^\d{1,2}\.\d{3}\.\d{3}-[0-9K]$/);
  });

  test('should handle electronic signature conditional field', async ({ page }) => {
    // Navigate to electronic signature step
    await page.click('button:has-text("Comenzar")');
    await page.check('#invoices');
    await page.click('button:has-text("Continuar")');
    await page.fill('#business_name', 'Test Company');
    await page.fill('#rut', '12.345.678-9');
    await page.fill('#business_phone', '912345678');
    await page.click('button:has-text("Continuar")');
    await page.fill('#legal_rep_name', 'John Doe');
    await page.fill('#legal_rep_rut', '98.765.432-1');
    await page.fill('#legal_rep_email', 'john@test.com');
    await page.click('button:has-text("Continuar")');
    
    // Initially signature upload should be hidden
    await expect(page.locator('#signature-upload')).not.toBeVisible();
    
    // Select "Yes" for having signature
    await page.check('#has_signature_yes');
    
    // Now signature upload should be visible
    await expect(page.locator('#signature-upload')).toBeVisible();
    
    // Select "No" for having signature
    await page.check('#has_signature_no');
    
    // Signature upload should be hidden again
    await expect(page.locator('#signature-upload')).not.toBeVisible();
  });

  test('should handle navigation between steps', async ({ page }) => {
    // Go forward to step 3
    await page.click('button:has-text("Comenzar")');
    await page.check('#invoices');
    await page.click('button:has-text("Continuar")');
    
    // Should be on step 3
    await expect(page.locator('[data-step="3"]')).toBeVisible();
    
    // Go back to step 2
    await page.click('button:has-text("Anterior")');
    
    // Should be on step 2
    await expect(page.locator('[data-step="2"]')).toBeVisible();
    
    // Check that our previous selection is still there
    await expect(page.locator('#invoices')).toBeChecked();
  });

  test('should update progress bar correctly', async ({ page }) => {
    // Initial progress should be around 14% (1/7 steps)
    const initialWidth = await page.locator('#progressFill').evaluate(el => 
      parseFloat(getComputedStyle(el).width) / parseFloat(getComputedStyle(el.parentElement).width) * 100
    );
    expect(initialWidth).toBeCloseTo(14.28, 1);
    
    // Go to step 2
    await page.click('button:has-text("Comenzar")');
    
    // Progress should be around 28% (2/7 steps)
    const step2Width = await page.locator('#progressFill').evaluate(el => 
      parseFloat(getComputedStyle(el).width) / parseFloat(getComputedStyle(el.parentElement).width) * 100
    );
    expect(step2Width).toBeCloseTo(28.57, 1);
  });

  test('should persist form data across page reload', async ({ page }) => {
    // Fill some form data
    await page.click('button:has-text("Comenzar")');
    await page.check('#invoices');
    await page.click('button:has-text("Continuar")');
    await page.fill('#business_name', 'Persistent Company');
    
    // Reload the page
    await page.reload();
    
    // Navigate to the same step
    await page.click('button:has-text("Comenzar")');
    
    // Check if invoice is still selected
    await expect(page.locator('#invoices')).toBeChecked();
    
    await page.click('button:has-text("Continuar")');
    
    // Check if business name is still there
    await expect(page.locator('#business_name')).toHaveValue('Persistent Company');
  });

  test('should be responsive on mobile', async ({ page }) => {
    // Set viewport to mobile size
    await page.setViewportSize({ width: 375, height: 667 });
    
    // Check if form is still usable
    await expect(page.locator('[data-step="1"]')).toBeVisible();
    await expect(page.locator('h1')).toBeVisible();
    
    // Navigate through a few steps
    await page.click('button:has-text("Comenzar")');
    await expect(page.locator('[data-step="2"]')).toBeVisible();
    
    // Check if option cards are stacked vertically
    const optionsGrid = page.locator('.options-grid');
    const gridColumns = await optionsGrid.evaluate(el => 
      getComputedStyle(el).gridTemplateColumns
    );
    expect(gridColumns).toContain('1fr'); // Should be single column on mobile
  });
});