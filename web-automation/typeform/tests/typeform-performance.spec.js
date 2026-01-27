const { test, expect } = require('@playwright/test');

test.describe('Typeform Performance Tests', () => {
  const TYPEFORM_URL = '/typeform';
  
  test('should load quickly', async ({ page }) => {
    const startTime = Date.now();
    
    await page.goto(TYPEFORM_URL);
    
    // Wait for the form to be fully loaded
    await page.waitForSelector('[data-step="1"]');
    await page.waitForSelector('#progressFill');
    
    const loadTime = Date.now() - startTime;
    
    // Page should load within 3 seconds
    expect(loadTime).toBeLessThan(3000);
  });

  test('should have good Core Web Vitals', async ({ page }) => {
    // Navigate to the typeform
    await page.goto(TYPEFORM_URL);
    
    // Measure Largest Contentful Paint (LCP)
    const lcp = await page.evaluate(() => {
      return new Promise((resolve) => {
        const observer = new PerformanceObserver((list) => {
          const entries = list.getEntries();
          const lastEntry = entries[entries.length - 1];
          resolve(lastEntry.startTime);
        });
        observer.observe({ entryTypes: ['largest-contentful-paint'] });
        
        // Fallback timeout
        setTimeout(() => resolve(0), 5000);
      });
    });
    
    // LCP should be under 2.5 seconds (good threshold)
    expect(lcp).toBeLessThan(2500);
  });

  test('should handle step transitions smoothly', async ({ page }) => {
    await page.goto(TYPEFORM_URL);
    
    const transitionTimes = [];
    
    // Measure transition time from step 1 to 2
    let startTime = Date.now();
    await page.click('button:has-text("Comenzar")');
    await page.waitForSelector('[data-step="2"].active');
    transitionTimes.push(Date.now() - startTime);
    
    // Measure transition time from step 2 to 3
    await page.check('#invoices');
    startTime = Date.now();
    await page.click('button:has-text("Continuar")');
    await page.waitForSelector('[data-step="3"].active');
    transitionTimes.push(Date.now() - startTime);
    
    // All transitions should be under 500ms
    transitionTimes.forEach(time => {
      expect(time).toBeLessThan(500);
    });
  });

  test('should not have memory leaks during navigation', async ({ page }) => {
    await page.goto(TYPEFORM_URL);
    
    // Get initial memory usage
    const initialMemory = await page.evaluate(() => {
      return performance.memory ? performance.memory.usedJSHeapSize : 0;
    });
    
    // Navigate through all steps multiple times
    for (let i = 0; i < 3; i++) {
      // Forward navigation
      await page.click('button:has-text("Comenzar")');
      await page.check('#invoices');
      await page.click('button:has-text("Continuar")');
      await page.fill('#business_name', 'Test Company');
      await page.fill('#rut', '12.345.678-9');
      await page.fill('#business_phone', '912345678');
      await page.click('button:has-text("Continuar")');
      
      // Backward navigation
      await page.click('button:has-text("Anterior")');
      await page.click('button:has-text("Anterior")');
      await page.click('button:has-text("Anterior")');
    }
    
    // Force garbage collection if available
    await page.evaluate(() => {
      if (window.gc) {
        window.gc();
      }
    });
    
    const finalMemory = await page.evaluate(() => {
      return performance.memory ? performance.memory.usedJSHeapSize : 0;
    });
    
    // Memory usage shouldn't increase dramatically (allow 50% increase)
    if (initialMemory > 0 && finalMemory > 0) {
      const memoryIncrease = (finalMemory - initialMemory) / initialMemory;
      expect(memoryIncrease).toBeLessThan(0.5);
    }
  });

  test('should have efficient CSS and JavaScript', async ({ page }) => {
    // Start performance measurement
    await page.goto(TYPEFORM_URL);
    
    // Get network timing information
    const resourceTiming = await page.evaluate(() => {
      const resources = performance.getEntriesByType('resource');
      return resources.map(resource => ({
        name: resource.name,
        duration: resource.duration,
        size: resource.transferSize || 0,
        type: resource.initiatorType
      }));
    });
    
    // Check CSS files
    const cssFiles = resourceTiming.filter(r => 
      r.name.includes('.css') || r.type === 'css'
    );
    
    cssFiles.forEach(css => {
      // CSS files should load quickly
      expect(css.duration).toBeLessThan(1000);
      // CSS files shouldn't be too large (under 100KB)
      if (css.size > 0) {
        expect(css.size).toBeLessThan(100 * 1024);
      }
    });
    
    // Check JavaScript files
    const jsFiles = resourceTiming.filter(r => 
      r.name.includes('.js') || r.type === 'script'
    );
    
    jsFiles.forEach(js => {
      // JS files should load quickly
      expect(js.duration).toBeLessThan(2000);
      // Individual JS files shouldn't be too large (under 200KB)
      if (js.size > 0) {
        expect(js.size).toBeLessThan(200 * 1024);
      }
    });
  });

  test('should handle large form data efficiently', async ({ page }) => {
    await page.goto(TYPEFORM_URL);
    
    // Fill out form with large amounts of data
    await page.click('button:has-text("Comenzar")');
    await page.check('#invoices');
    await page.check('#receipts');
    await page.click('button:has-text("Continuar")');
    
    // Fill with long strings to test data handling
    const longString = 'A'.repeat(1000);
    
    const startTime = Date.now();
    
    await page.fill('#business_name', longString);
    await page.fill('#rut', '12.345.678-9');
    await page.fill('#business_phone', '912345678');
    
    const fillTime = Date.now() - startTime;
    
    // Form filling should be responsive even with large data
    expect(fillTime).toBeLessThan(1000);
    
    // Continue to next step
    const transitionStart = Date.now();
    await page.click('button:has-text("Continuar")');
    await page.waitForSelector('[data-step="4"].active');
    const transitionTime = Date.now() - transitionStart;
    
    // Step transition should still be fast
    expect(transitionTime).toBeLessThan(500);
  });

  test('should work well on slow networks', async ({ page }) => {
    // Simulate slow 3G network
    await page.context().setExtraHTTPHeaders({});
    
    // Set network conditions
    const client = await page.context().newCDPSession(page);
    await client.send('Network.emulateNetworkConditions', {
      offline: false,
      downloadThroughput: 1.5 * 1024 * 1024 / 8, // 1.5 Mbps
      uploadThroughput: 750 * 1024 / 8, // 750 Kbps  
      latency: 40 // 40ms RTT
    });
    
    const startTime = Date.now();
    await page.goto(TYPEFORM_URL);
    
    // Wait for essential content to load
    await page.waitForSelector('[data-step="1"]');
    await page.waitForSelector('button:has-text("Comenzar")');
    
    const loadTime = Date.now() - startTime;
    
    // Should still load within reasonable time on slow network (10 seconds)
    expect(loadTime).toBeLessThan(10000);
    
    // Form should be functional
    await page.click('button:has-text("Comenzar")');
    await expect(page.locator('[data-step="2"]')).toBeVisible();
  });

  test('should optimize images and assets', async ({ page }) => {
    await page.goto(TYPEFORM_URL);
    
    // Get all image resources
    const imageResources = await page.evaluate(() => {
      const resources = performance.getEntriesByType('resource');
      return resources.filter(resource => {
        return resource.name.match(/\.(jpg|jpeg|png|gif|webp|svg)$/i) ||
               resource.initiatorType === 'img';
      }).map(img => ({
        name: img.name,
        size: img.transferSize || 0,
        duration: img.duration
      }));
    });
    
    imageResources.forEach(img => {
      // Images should load quickly
      expect(img.duration).toBeLessThan(3000);
      // Images should be optimized (under 500KB unless it's a background)
      if (img.size > 0 && !img.name.includes('background')) {
        expect(img.size).toBeLessThan(500 * 1024);
      }
    });
  });

  test('should handle concurrent user interactions', async ({ page }) => {
    await page.goto(TYPEFORM_URL);
    
    // Simulate rapid user interactions
    const interactions = [];
    
    // Start multiple interactions almost simultaneously
    interactions.push(page.click('button:has-text("Comenzar")'));
    interactions.push(page.waitForSelector('[data-step="2"]'));
    
    await Promise.all(interactions);
    
    // Form should handle concurrent interactions gracefully
    await expect(page.locator('[data-step="2"]')).toBeVisible();
    
    // Continue with rapid form filling
    const rapidFills = [];
    rapidFills.push(page.check('#invoices'));
    rapidFills.push(page.check('#receipts'));
    
    await Promise.all(rapidFills);
    
    // Both checkboxes should be checked
    await expect(page.locator('#invoices')).toBeChecked();
    await expect(page.locator('#receipts')).toBeChecked();
  });
});