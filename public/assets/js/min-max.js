/*  
  Corrige implementacion trunca de <input type="number"> donde el max=""
  solo se retringe con las flechas pero no al ingresar por teclado
*/

document.addEventListener('DOMContentLoaded', function() {

    setTimeout(function(){
      const inputElementsWithMax = jQuery('input[type="number"][max]');
    
      inputElementsWithMax.each(function() {
        const $input = jQuery(this);
        const max = parseInt($input.attr('max'));
    
        $input.on('change keyup keydown input', function() {
          const currentValue = parseInt($input.val());
          if (!isNaN(currentValue) && currentValue > max) {
            $input.val(max); 
          }
        });
      });  
    }, 500)
 
});

      
