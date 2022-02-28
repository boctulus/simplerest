
<div class="container-fluid">
    <div class="row">
        
        <div class="col-12">
             <h3>Campos tabla dinámica</h3>
             <div class="mt-3">
                Seleccionar los campos para agregar al informe:
             </div>
        </div>
        
    </div>
    <div class="row">
        <div class="col-5">
          
          <div class="input-group mt-3">
              <input type="text" class="form-control" placeholder="Buscar" id="input_busca">

              <div class="input-group-append">
              <button class="btn btn-secondary" type="button" id="buscar">
                  <i class="fa fa-search"></i>
              </button>
              </div>
          </div>
   

          <!-- checkboxes -->
          <div class="y-scrollable" id="campos_todos">      
              <?php 
              if (isset($campos) && !empty($campos)):        
                foreach ($campos as $campo): ?>
                    <div class="form-check list" data-name="<?= $campo ?>" id="campo-<?= $campo ?>" style="width: max-content;">
                        <div class="item draggable" id="draggable-campo-<?= $campo ?>">
                            <!--  <input class="form-check-input" type="checkbox" disabled value="" id="flexCheckDefault"> -->
                            <label class="form-check-label list-group-item-action" for="flexCheckDefault" style="width: max-content;"><?= $campo ?></label>
                        </div>
                    </div>
              <?php
                endforeach; 
              endif;
              ?>
          </div>
            
        </div>
        <div class="col-7">
          <div class="row">
            <div class="col-12">
              <div class="row">
                <div class="col-6 droppable list mt-3" aria-label="Filtros" id="filtros">
                    <span><i class="fa fa-filter"></i> Filtros</span>
                    <select name="filtros" class="form-select mt-2" id="input_filt" size="5">
                    </select>
                  <!--  <input type="text" class="form-control mt-2" id="input_filt"> -->
                </div>
            
                <div class="col-6 droppable list mt-3" aria-label="Columnas" id="columnas">
                    <span><i class="fa fa-columns" aria-hidden="true"></i> Columnas</span>
                    <select name="columnas" class="form-select mt-2" id="input_coln" size="5">
                    </select>
                    <!--<input type="text" class="form-control mt-2" id="input_coln"> -->
                </div>
            </div>
        
            <div class="row">            
                <div class="col-6 droppable list mt-3" aria-label="Filas" id="filas">
                    <span><i class="fa fa-table"></i> Filas</span>
                    <select class="form-select mt-2" id="input_fila">
                        
                    </select>
                </div>
                
                <div class="col-6 droppable list mt-3" aria-label="Sumatoria" id="valores">
                    <span><i class="fa fa-sum"></i> Valores</span>
                    <input type="text" class="form-control mt-2" id="input_vals">
                </div>
            </div>
          </div>
        </div>
            
        </div>
        
        
    </div>
    
    
<div class="row mt-4">
    <div class="offset-md-5 col-md-7 text-center">
        <button id="btn_reset" class="btn btn-outline-primary btn-large col-12">Restablecer</button>
    </div>    
</div>
    
    <div id="contenedor-tabla" class="mt-4">
        
        <table id="table">
  <thead>
    <tr>
      <th data-field="id">ID</th>
      <th data-field="periodo">Periodo</th>
      <th data-field="cedente">CEDENTE</th>
    </tr>
  </thead>
</table>

<script>
  var $table = $('#table')

  $(function() {
    var data = [
      {
        'id': 0,
        'name': 'Item 0',
        'price': '$0'
      },
      {
        'id': 1,
        'name': 'Item 1',
        'price': '$1'
      },
      {
        'id': 2,
        'name': 'Item 2',
        'price': '$2'
      },
      {
        'id': 3,
        'name': 'Item 3',
        'price': '$3'
      },
      {
        'id': 4,
        'name': 'Item 4',
        'price': '$4'
      },
      {
        'id': 5,
        'name': 'Item 5',
        'price': '$5'
      }
    ]
    $table.bootstrapTable({data: data})
  })
</script>
        
    </div>
    
</div>
<link href="https://unpkg.com/bootstrap-table@1.18.3/dist/bootstrap-table.min.css" rel="stylesheet">

<script src="https://unpkg.com/bootstrap-table@1.18.3/dist/bootstrap-table.min.js"></script>


