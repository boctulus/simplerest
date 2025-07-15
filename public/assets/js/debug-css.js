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
    Shows if an element is supposed to be visible or not
*/
function isVisibleDeep(selector) {
  const $el = $(selector);

  if ($el.length === 0) return false;

  let current = $el[0];

  while (current && current.nodeType === 1) {
    const $current = $(current);
    const style = window.getComputedStyle(current);

    if (
      style.display === 'none' ||
      style.visibility === 'hidden' ||
      style.opacity === '0'
    ) {
      return false;
    }

    current = current.parentElement;
  }

  return true;
}

