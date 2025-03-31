<?php

namespace Boctulus\Simplerest\Core\traits;

use Boctulus\Simplerest\Core\exceptions\InvalidValidationException;
use Boctulus\Simplerest\Core\Libs\Arrays;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Libs\Strings;
use Boctulus\Simplerest\Core\Libs\Validator;
use Boctulus\Simplerest\Core\Model;

/*
    Trait a integrarse en Model de Query Builder    

    Insertar registros con subrecursos (relaciones)
    
    TO-DO

    - Donde se usa response() deberia obviarse. La unica salida debe ser return de tipo int o null o throw

    - Considerar usar el ACL a nivel de Model para "securitizar" las operaciones

    - Cambiar los HOOKs porque ya no serian los de la API sino los del Model

    - Considerar crear metodos helper como createOrUpdateRelated, handleSubresource, etc
*/
trait InsertWithSubResourcesTrait
{
    const EXECUTION_MODE_NORMAL   = 0;   // Ejecución normal
	const EXECUTION_MODE_SIMULATE = 1;   // Simular operación (no realiza cambios en BD)
	const EXECUTION_MODE_PREVIEW  = 2;   // Obtener SQL y valores que se ejecutarían

    protected $connect_to = [];
    protected $executionMode = self::EXECUTION_MODE_NORMAL;


    function insertStruct($data) 
    {   
        $tables   = array_keys($data);                
        $tables   = array_intersect($tables, $this->connect_to);

        // Tablas en orden de insercion de tablas dependientes
        $order    = $this->getInsertionOrder($tables);

        $own_data = [];

        foreach ($data as $key => $datum){
            if (!in_array($key, $tables)){
                $own_data[] = $datum;
            }
        }

        dd($order, 'ORDER related tables'); // ok
        dd($own_data, 'OWN DATA');          // ok

        
        /*
            La tabla principal $this->table_name es la ultima en la que debe insertarse

            Leer la DOC de SimpleRest y con calma re-escribir
        */

        // ...
    }


    /**
     * Obtiene una instancia del modelo para la tabla especificada
     * 
     * @param string $table_name Nombre de la tabla
     * @return Model Instancia del modelo
     */
    protected function getModelInstance($table_name)
    {
        // Si ya existe una conexión específica para esta tabla
        if (isset($this->connect_to[$table_name])) {
            return table($table_name, $this->connect_to[$table_name]);
        }
        
        // Caso predeterminado
        return table($table_name);
    }

    /**
     * Configura conexiones específicas por tabla
     * 
     * @param array $connections Mapa de tabla => conexión
     * @return $this
     */
    public function setTableConnections(array $connections)
    {
        $this->connect_to = $connections;
        return $this;
    }
    
}
