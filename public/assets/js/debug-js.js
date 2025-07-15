/*
    JS debugger
*/

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