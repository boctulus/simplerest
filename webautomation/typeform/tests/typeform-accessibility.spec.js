const { test, expect } = require('@playwright/test');

test.describe('Typeform Accessibility Tests', () => {
  const TYPEFORM_URL = '/typeform';
  
  test.beforeEach(async ({ page }) => {
    await page.goto(TYPEFORM_URL);
  });

  test('should have proper heading hierarchy', async ({ page }) => {
    // Check h1 exists on welcome step
    await expect(page.locator('h1')).toBeVisible();
    
    // Navigate to step 2
    await page.click('button:has-text("Comenzar")');
    
    // Check h2 exists on step 2
    await expect(page.locator('h2')).toBeVisible();
    
    // Check heading hierarchy (h1 should be followed by h2, not h3)
    const headings = await page.locator('h1, h2, h3, h4, h5, h6').all();
    let previousLevel = 0;
    
    for (const heading of headings) {
      const tagName = await heading.evaluate(el => el.tagName);
      const level = parseInt(tagName.charAt(1));
      
      // Level should not skip more than 1 (e.g., h1 followed by h3 is bad)
      if (previousLevel > 0) {
        expect(level - previousLevel).toBeLessThanOrEqual(1);
      }
      
      previousLevel = level;
    }
  });

  test('should have proper form labels', async ({ page }) => {
    // Navigate to business info step
    await page.click('button:has-text("Comenzar")');
    await page.check('#invoices');
    await page.click('button:has-text("Continuar")');
    
    // Check that all form inputs have associated labels
    const inputs = page.locator('input[type="text"], input[type="email"], input[type="tel"], select');
    const inputCount = await inputs.count();
    
    for (let i = 0; i < inputCount; i++) {
      const input = inputs.nth(i);
      const inputId = await input.getAttribute('id');
      
      if (inputId) {
        // Check if there's a label with for attribute pointing to this input
        const label = page.locator(`label[for="${inputId}"]`);
        await expect(label).toBeVisible();
      }
    }
  });

  test('should be keyboard navigable', async ({ page }) => {
    // Test keyboard navigation through steps
    await page.keyboard.press('Tab'); // Should focus the "Comenzar" button
    await page.keyboard.press('Enter'); // Should click the button
    
    // Should now be on step 2
    await expect(page.locator('[data-step="2"]')).toBeVisible();
    
    // Navigate through checkboxes using keyboard
    await page.keyboard.press('Tab');
    await page.keyboard.press('Space'); // Should check the first option
    
    await expect(page.locator('#invoices')).toBeChecked();
  });

  test('should have proper ARIA attributes', async ({ page }) => {
    // Check if progress bar has proper ARIA attributes
    const progressBar = page.locator('#progressFill');
    const progressContainer = page.locator('.progress-bar');
    
    // Progress elements should have proper roles
    await expect(progressContainer).toHaveAttribute('role', 'progressbar');
    
    // Check if form has proper ARIA labels
    const form = page.locator('#typeform');
    await expect(form).toHaveAttribute('role', 'form');
  });

  test('should have sufficient color contrast', async ({ page }) => {
    // This test would typically use axe-core for automated accessibility testing
    // For now, we'll check that text is visible against backgrounds
    
    // Check that main text is visible
    await expect(page.locator('h1')).toBeVisible();
    await expect(page.locator('.subtitle')).toBeVisible();
    
    // Check button text visibility
    await expect(page.locator('button:has-text("Comenzar")')).toBeVisible();
    
    // Navigate to step with form fields
    await page.click('button:has-text("Comenzar")');
    await page.check('#invoices');
    await page.click('button:has-text("Continuar")');
    
    // Check form label visibility
    await expect(page.locator('label[for="business_name"]')).toBeVisible();
    await expect(page.locator('input#business_name')).toBeVisible();
  });

  test('should handle screen reader friendly content', async ({ page }) => {
    // Check for skip links or similar accessibility features
    // In a real implementation, you might have skip navigation links
    
    // Check that error messages are properly associated with form fields
    await page.click('button:has-text("Comenzar")');
    await page.check('#invoices');
    await page.click('button:has-text("Continuar")');
    
    // Try to continue without filling required fields
    await page.click('button:has-text("Continuar")');
    
    // Check if error styling is applied
    const requiredInputs = page.locator('input[required]');
    const firstInput = requiredInputs.first();
    
    // Input should have error class after validation failure
    await expect(firstInput).toHaveClass(/error/);
  });

  test('should support high contrast mode', async ({ page }) => {
    // Simulate high contrast mode by checking if elements are still visible
    // and have proper styling
    
    // Test with forced colors media query
    await page.emulateMedia({ colorScheme: 'dark', forcedColors: 'active' });
    
    // Elements should still be visible
    await expect(page.locator('h1')).toBeVisible();
    await expect(page.locator('button:has-text("Comenzar")')).toBeVisible();
    
    // Navigate through form
    await page.click('button:has-text("Comenzar")');
    await expect(page.locator('[data-step="2"]')).toBeVisible();
    await expect(page.locator('.option-card')).toBeVisible();
  });

  test('should handle focus management', async ({ page }) => {
    // When navigating between steps, focus should be managed properly
    await page.click('button:has-text("Comenzar")');
    
    // After step change, focus should be on a meaningful element
    const focusedElement = await page.evaluate(() => document.activeElement.tagName);
    expect(['H1', 'H2', 'BUTTON', 'INPUT']).toContain(focusedElement);
  });

  test('should provide alternative text for images/icons', async ({ page }) => {
    // Check if emoji icons have proper labels or are marked as decorative
    const cardIcons = page.locator('.card-icon');
    const iconCount = await cardIcons.count();
    
    for (let i = 0; i < iconCount; i++) {
      const icon = cardIcons.nth(i);
      const ariaLabel = await icon.getAttribute('aria-label');
      const title = await icon.getAttribute('title');
      
      // Icons should either have aria-label, title, or be marked as decorative
      if (!ariaLabel && !title) {
        const ariaHidden = await icon.getAttribute('aria-hidden');
        expect(ariaHidden).toBe('true');
      }
    }
  });

  test('should support zoom up to 200%', async ({ page }) => {
    // Test that form remains usable when zoomed
    await page.setViewportSize({ width: 1920, height: 1080 });
    
    // Zoom to 200%
    await page.evaluate(() => {
      document.body.style.zoom = '200%';
    });
    
    // Form should still be usable
    await expect(page.locator('h1')).toBeVisible();
    await expect(page.locator('button:has-text("Comenzar")')).toBeVisible();
    
    // Navigate through form
    await page.click('button:has-text("Comenzar")');
    await expect(page.locator('[data-step="2"]')).toBeVisible();
    
    // Options should still be clickable
    await page.click('#invoices');
    await expect(page.locator('#invoices')).toBeChecked();
  });
});