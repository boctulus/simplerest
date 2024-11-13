/*
    CSS debugger
        
    // Ejemplos de uso:
    
    // 1. Obtener e imprimir propiedades CSS
    handleCSS('.page-container');

    // 2. Aplicar propiedades CSS
    handleCSS('.page-container', {
        position: 'absolute',
        'margin-left': '20px',
        'margin-right': '20px',
        'padding-left': '10px',
        'padding-right': '10px'
    });

    // 3.
    handleCSS($('#page-container').parent());
*/
function handleCSS(selector, cssObject = null) {
    // Si solo pasamos el selector, obtenemos e imprimimos las propiedades
    if (cssObject === null) {
        let element = $(selector);
        let positionType = element.css('position');
        let marginLeft = element.css('margin-left');
        let marginRight = element.css('margin-right');
        let paddingLeft = element.css('padding-left');
        let paddingRight = element.css('padding-right');
        
        console.log(`Position: ${positionType}`);
        console.log(`Margin Left: ${marginLeft}`);
        console.log(`Margin Right: ${marginRight}`);
        console.log(`Padding Left: ${paddingLeft}`);
        console.log(`Padding Right: ${paddingRight}`);
    } 
    // Si pasamos un objeto CSS, lo aplicamos al selector
    else {
        $(selector).css(cssObject);
    }
}

/*
    listClickEventListeners() permite activar la detección de clics
*/
function listClickEventListeners(){
    // Función para registrar información sobre el elemento que recibe un clic
    function logClickEvent(event) {
        // Evita que la propagación del evento afecte el comportamiento
        event.stopPropagation();

        // Obtiene el elemento que recibió el clic
        const targetElement = event.target;

        // Registra detalles del elemento que recibe el clic
        console.log(`Click en elemento: ${targetElement.tagName}`);
        console.log(`ID: ${targetElement.id || 'Sin ID'}`);
        console.log(`Clase(s): ${targetElement.className || 'Sin clase'}`);
        console.log(`Atributos: ${Array.from(targetElement.attributes).map(attr => `${attr.name}="${attr.value}"`).join(', ')}`);

        // Resalta temporalmente el elemento en la interfaz
        targetElement.style.outline = '2px solid red';
        setTimeout(() => {
            targetElement.style.outline = '';
        }, 500);
    }

    // Añade el event listener al documento para capturar cualquier clic
    document.addEventListener('click', logClickEvent, true);
}

/*
    Convierte un objeto {} en un string
*/
function toString(obj) {
    let result = '';
    for (let key in obj) {
        if (obj[key] && typeof obj[key] === 'object') {
            if (Array.isArray(obj[key])) {
                result += `${key}: [${obj[key].map(item => JSON.stringify(item)).join(', ')}]\n`;
            } else {
                result += `${key}: ${JSON.stringify(obj[key], null, 2)}\n`;
            }
        } else {
            result += `${key}: ${obj[key]}\n`;
        }
    }
    return result;
}