SELECT
   id,
   created_at,
   (
      SELECT
         IF( COUNT(__factura_detalle.id) = 0, JSON_ARRAY(), JSON_ARRAYAGG( JSON_OBJECT( 

          'id', __factura_detalle.id, 
          'factura_id', __factura_detalle.factura_id, 
          'product_id', __factura_detalle.product_id, 
          'quantity', __factura_detalle.quantity, 
          'created_at', __factura_detalle.created_at,

          'products', (SELECT
               JSON_ARRAYAGG( JSON_OBJECT(
               'prod_id', products.id,
               'prod_created_at', products.created_at
               )
          )           

          FROM
         products
          WHERE products.id = __factura_detalle.product_id)


          ) ) ) 
      FROM
         factura_detalle as __factura_detalle 
      WHERE
         facturas.id = __factura_detalle.factura_id 
   )
   as factura_detalle    
    
 
FROM
   facturas
