<?php

namespace simplerest\core\traits;

use simplerest\core\libs\DB;
use simplerest\core\exceptions\SchemaException;

/*
    Sin validar
*/

trait RelationshipTrait 
{   
    protected $with = []; // Relaciones a cargar
    protected $relations = []; // Datos de relaciones cargados
    
    /**
     * Especifica las relaciones a cargar
     */
    public function with($relations)
    {
        if (is_string($relations)) {
            $relations = func_get_args();
        }
        
        $this->with = array_merge($this->with, (array)$relations);
        return $this;
    }

    /**
     * Carga y ejecuta los joins necesarios para las relaciones
     */
    protected function loadRelations()
    {
        foreach ($this->with as $relation) {
            $this->loadRelation($relation);
        }
    }

    /**
     * Carga una relación específica
     */
    protected function loadRelation($relation) 
    {
        // Obtener info de la relación del schema
        $relationType = $this->getRelationType($relation);
        
        switch($relationType) {
            case '1:1':
            case '1:n':
            case 'n:1':
                $this->loadSimpleRelation($relation);
                break;
                
            case 'n:m':
                $this->loadPivotRelation($relation); 
                break;
        }
    }

    /**
     * Carga relación simple (1:1, 1:N, N:1)
     */
    protected function loadSimpleRelation($relation)
    {
        if (!isset($this->schema['expanded_relationships'][$relation])) {
            throw new SchemaException("Relation '$relation' not found in schema");
        }

        $relData = $this->schema['expanded_relationships'][$relation][0];
        
        // Obtener tabla y campo origen
        $targetTable = $relData[0][0];
        $targetKey = $relData[0][1];
        
        // Obtener tabla y campo destino
        $sourceTable = $relData[1][0];
        $sourceKey = $relData[1][1];

        // Si hay alias para la tabla relacionada
        $tableAlias = null;
        if (isset($relData[0]['alias'])) {
            $tableAlias = $relData[0]['alias'];
        }

        // Construir el join
        if ($tableAlias) {
            $this->join("$targetTable as $tableAlias", "$tableAlias.$targetKey", '=', "$sourceTable.$sourceKey");
        } else {
            $this->join($targetTable, "$targetTable.$targetKey", '=', "$sourceTable.$sourceKey");
        }

        return $this;
    }

    /**
     * Carga relación N:M usando tabla pivot
     */
    protected function loadPivotRelation($relation)
    {
        $pivot = get_pivot([$this->table_name, $relation]);
        $bridge = $pivot['bridge'];
        $fks = $pivot['fks'];
        
        // Joins a tabla pivot y tabla relacionada
        $this->join($bridge, "{$this->table_name}.id", '=', "$bridge.{$fks[$this->table_name]}")
             ->join($relation, "$relation.id", '=', "$bridge.{$fks[$relation]}");
    }

    /**
     * Determina el tipo de relación (1:1, 1:n, n:1, n:m) entre dos tablas
     */
    protected function getRelationType(string $targetTable) : ?string 
    {
        if (empty($this->table_name)) {
            throw new \Exception("Table name is not defined");
        }

        return get_rel_type($this->table_name, $targetTable, null, DB::getCurrentConnectionId());
    }
}