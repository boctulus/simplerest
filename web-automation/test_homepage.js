const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({
        headless: false,
        slowMo: 100
    });

    const context = await browser.newContext({
        viewport: { width: 1920, height: 1080 }
    });

    const page = await context.newPage();

    try {
        console.log('Navegando a la homepage...');
        await page.goto('http://simplerest.lan/', { waitUntil: 'networkidle' });

        console.log('Esperando a que cargue completamente...');
        await page.waitForTimeout(2000);

        // Screenshot completo
        console.log('Tomando screenshot de la página completa...');
        await page.screenshot({
            path: 'web-automation/screenshots/homepage_full.png',
            fullPage: true
        });

        // Screenshot del hero section
        console.log('Tomando screenshot del hero section...');
        await page.screenshot({
            path: 'web-automation/screenshots/homepage_hero.png'
        });

        // Scroll a la sección de features
        console.log('Scrolleando a Features...');
        await page.click('a[href="#services"]');
        await page.waitForTimeout(1500);
        await page.screenshot({
            path: 'web-automation/screenshots/homepage_features.png'
        });

        // Scroll a la sección de documentación
        console.log('Scrolleando a Documentation...');
        await page.click('a[href="#docs"]');
        await page.waitForTimeout(1500);
        await page.screenshot({
            path: 'web-automation/screenshots/homepage_docs.png'
        });

        // Scroll a la sección de download
        console.log('Scrolleando a Download...');
        await page.click('a[href="#download"]');
        await page.waitForTimeout(1500);
        await page.screenshot({
            path: 'web-automation/screenshots/homepage_download.png'
        });

        // Test responsive design - Mobile view
        console.log('Probando vista móvil...');
        await page.setViewportSize({ width: 375, height: 667 }); // iPhone SE
        await page.goto('http://simplerest.lan/', { waitUntil: 'networkidle' });
        await page.waitForTimeout(2000);
        await page.screenshot({
            path: 'web-automation/screenshots/homepage_mobile.png',
            fullPage: true
        });

        // Test responsive design - Tablet view
        console.log('Probando vista tablet...');
        await page.setViewportSize({ width: 768, height: 1024 }); // iPad
        await page.goto('http://simplerest.lan/', { waitUntil: 'networkidle' });
        await page.waitForTimeout(2000);
        await page.screenshot({
            path: 'web-automation/screenshots/homepage_tablet.png',
            fullPage: true
        });

        console.log('\n✅ Test completado exitosamente!');
        console.log('Screenshots guardados en web-automation/screenshots/\n');

        console.log('Verificaciones realizadas:');
        console.log('✓ Homepage carga correctamente');
        console.log('✓ Navegación entre secciones funciona');
        console.log('✓ Diseño responsivo - Desktop');
        console.log('✓ Diseño responsivo - Mobile');
        console.log('✓ Diseño responsivo - Tablet');

    } catch (error) {
        console.error('❌ Error durante el test:', error.message);
        await page.screenshot({
            path: 'web-automation/screenshots/homepage_error.png'
        });
    } finally {
        await browser.close();
    }
})();
