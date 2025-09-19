class JSAssets 
{
    /*
        Wait for dependencies
        
        Uso:

        await this.waitForDependencies(['TypeformStepManager', 'GlobalFormDataProvider']);    
    */
    async waitForDependencies(requiredDeps) {
        const maxRetries = 50;
        let retries = 0;

        return new Promise((resolve, reject) => {
            const checkDeps = () => {
                const missing = requiredDeps.filter(dep => !window[dep]);
                
                if (missing.length === 0) {
                    resolve();
                    return;
                }

                retries++;
                if (retries >= maxRetries) {
                    reject(new Error(`Dependencies not found: ${missing.join(', ')}`));
                    return;
                }

                setTimeout(checkDeps, 50);
            };
            
            checkDeps();
        });
    }
}
