function disableOfertaCheckbox(){
    document.getElementById('oferta').disabled = true;
 }
 
 function enableOfertaCheckbox(){
     document.getElementById('oferta').disabled = false;
 }
 
 function clear_se_filters(stock = true, promotions = true, order = true, category = true, attributes = true){
     // console.log('INIT clear_se_filters');
 
     if (category){
         $('#woo_cat_prd').val(null).trigger('change');
     }
     
     if (attributes){
         $('.woo_att').val(null).trigger('change'); 
     }       
     
     if (stock){
         $('#stock').prop('checked', false);
     }
     
     if (promotions){
         $('#oferta').prop('checked', false);
     }   
 
    //  if (order){
    //      $('#orderBy').val(cfg.order_default); // Valor por defecto
    //  }
 }
 
 function clear_se_form(){
     // console.log('INIT clear_se_form');
 
     $('#anything').val('');
     $('#buscar-codigo').val('');    
 
     enableOfertaCheckbox();
 }
 
 function clear_se_results(hide = false, clear_count = false, update_hash = false){   
     // console.log('INIT clear_se_results');
 
     if (hide){
        //  hideResultsContainer();
         $('.pagination-container').hide();
     }
     
     $('.results-container tbody').empty();
 
    //  if (update_hash){
    //      removeHash();
    //  }
 
    //  if (clear_count){
    //      updateResultCount();
    //  }
     
 }
 
 // Función para mostrar el spinner
 function showSpinner() {
     $('#spinner-container').show();
 }
 
 // Función para ocultar el spinner
 function hideSpinner() {
     $('#spinner-container').hide();
 }
 
 jQuery(function () {
     if (typeof $ == 'undefined') { $ = jQuery };
 
     // Evento de limpieza
     $('.clearSearchForm').on('click', function () {
         form_clear_in_progress = true;
         clear_se_form();
         clear_se_filters(true, true, true, true, true);
         clear_se_results(true, true, true);
         form_clear_in_progress = false;
     });
 
     /*
         Dado que no soporto la combinacion de atributos con busqueda por stock
     */
         
     const buscarCodigoInput = $('#buscar-codigo'); // Asegúrate de usar '#' para seleccionar por ID
 
     if (buscarCodigoInput.val().trim() !== '') {
         disableOfertaCheckbox();
     }
 
     buscarCodigoInput.on('input', function() {
         // Verifica si el contenido es distinto de ''
         if (buscarCodigoInput.val().trim() !== '') {
             disableOfertaCheckbox();
         } else {
             enableOfertaCheckbox();
         }
     });
 
     /*
         Form Expansion
     */
 
     
 });
 
 
 