<?php

namespace simplerest\core\traits;

use simplerest\core\libs\DB;
use simplerest\core\exceptions\SchemaException;

trait RelationshipTrait 
{   
    /**
     * Obtiene el modelo relacionado para una relación dada.
     *
     * @param string $relation Nombre de la relación.
     * @return Model Instancia del modelo relacionado.
     */
    protected function getRelatedModel($relation)
    {
        $relatedTable = $this->schema['relationships'][$relation]['table'];
        return DB::table($relatedTable);
    }

    /**
     * Obtiene el tipo de relación (one-to-one, one-to-many, many-to-many).
     *
     * @param string $relation Nombre de la relación.
     * @return string Tipo de relación.
     */
    protected function getRelationType($relation)
    {
        return $this->schema['relationships'][$relation]['type'];
    }    

    /**
     * Obtiene la clave foránea de una relación.
     *
     * @param string $relation Nombre de la relación.
     * @return string Nombre de la clave foránea.
     */
    protected function getForeignKey($relation)
    {
        return $this->schema['relationships'][$relation]['foreign_key'] ?? $this->table_name . '_id';
    }

    /**
     * Obtiene la clave local de una relación.
     *
     * @param string $relation Nombre de la relación.
     * @return string Nombre de la clave local.
     */
    protected function getLocalKey($relation)
    {
        return $this->schema['relationships'][$relation]['local_key'] ?? $this->schema['id_name'];
    }

    /**
     * Obtiene el nombre de la tabla pivote para relaciones muchos a muchos.
     *
     * @param string $relation Nombre de la relación.
     * @return string Nombre de la tabla pivote.
     */
    protected function getPivotTable($relation)
    {
        return $this->schema['relationships'][$relation]['pivot_table'];
    }
    
    
}