function parsePrice(price, decimalSeparator, thousandSeparator) {
    // Eliminar cualquier carácter que no sea número o los separadores
    let cleanPrice = price.replace(/[^\d.,]/g, '');
    
    // Reemplazar el separador de miles por nada
    cleanPrice = cleanPrice.replace(new RegExp('\\' + thousandSeparator, 'g'), '');
    
    // Reemplazar el separador decimal por un punto
    cleanPrice = cleanPrice.replace(new RegExp('\\' + decimalSeparator, 'g'), '.');
    
    // Convertir a float y retornar
    return parseFloat(cleanPrice);
}

function formatPrice(price, decimalSeparator = ',', thousandSeparator = '.', currency_symbol = '€', decimals = 2) 
{
    // console.log(price, decimalSeparator, thousandSeparator,  currency_symbol, decimals);

    try {
        // Asegurarse de que price sea un número
        let numPrice = Number(price);

        if (isNaN(numPrice)) {
            throw new Error(`El precio [${price}] no es un número válido`);
        }

        // Convertir el precio a una cadena con los decimales especificados
        let formattedPrice = numPrice.toFixed(decimals);
        
        // Separar la parte entera y decimal
        let [integerPart, decimalPart] = formattedPrice.split('.');
        
        // Añadir separadores de miles a la parte entera
        integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, thousandSeparator);
        
        // Unir las partes con el separador decimal especificado
        formattedPrice = integerPart + decimalSeparator + decimalPart;
        
        // Añadir el símbolo de la moneda y devolver el resultado
        return formattedPrice + ' ' + currency_symbol;
    } catch (error) {
        console.error('Error al formatear el precio: ', price, error.message);
        return 'Error: Precio inválido';
    }
}

/*
    Especificas de WooCommerce
*/

function parsePriceFromWooCommerceSettings(price){
    return parsePrice(price, woocommerce_currency_settings.decimalSeparator, woocommerce_currency_settings.thousandSeparator);
}

function formatPriceFromWooCommerceSettings(price){
    return formatPrice(price, woocommerce_currency_settings.decimalSeparator, woocommerce_currency_settings.thousandSeparator, decodeHTMLEntities(woocommerce_currency_settings.symbol), woocommerce_currency_settings.decimals);
}
