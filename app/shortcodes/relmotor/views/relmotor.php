<div class="form-container">
  <h2>CÓDIGO, APLICACIÓN O DESCRIPCIÓN</h2>
  <form>
    <input type="text" id="codigo" placeholder="ALT9010104">
    
    <div class="field">
      <label for="producto">Producto <span class="info-icon">i</span></label>
      <input type="text" id="producto" placeholder="Producto">
    </div>
    
    <div class="field">
      <label for="sistema">Sistema Eléctrico <span class="info-icon">i</span></label>
      <input type="text" id="sistema" placeholder="Sistema Eléctrico">
    </div>
    
    <div class="field">
      <label for="marca">Marca <span class="info-icon">i</span></label>
      <input type="text" id="marca" placeholder="Marca">
    </div>
    
    <input type="text" id="buscar-codigo" placeholder="BUSCAR POR CODIGO">
    <p class="help-text">Para buscar más de un SKU, separarlo por comas.</p>
    
    <div class="checkbox-group">
      <input type="checkbox" id="oferta" checked>
      <label for="oferta">En oferta</label>
    </div>
    
    <div class="checkbox-group">
      <input type="checkbox" id="stock">
      <label for="stock">En stock</label>
    </div>
    
    <div class="button-group">
      <button type="button" class="btn-limpiar">Limpiar</button>
      <button type="submit" class="btn-buscar">Buscar</button>
    </div>
  </form>
</div>