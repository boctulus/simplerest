<?php

namespace simplerest\core\traits;

use simplerest\core\exceptions\InvalidValidationException;
use simplerest\core\libs\Arrays;
use simplerest\core\libs\DB;
use simplerest\core\libs\Strings;
use simplerest\core\libs\Validator;
use simplerest\core\Model;

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
        dd($data, 'DATA');

        if (is_string($data)){
            $data = json_decode($data, true);
        }

        if (empty($data)){
            throw new \InvalidArgumentException("Invalid data");
        }

        // Step 1: Extract table names from structured data
        $tables = array_keys($data);

        dd($tables, 'TABLES to be ordered');

        // Step 2: Get the correct insertion order
        $tables_in_order = $this->getInsertionOrder($tables);

        dd($tables_in_order, 'TABLES in order');

        // Step 3: re-arrange array $data

        $data = Arrays::reorderArray($data, $tables_in_order);

        dd($data, 'DATA'); exit; /// 

        // $id = $data[$this->getIdName()] ?? null;

        try {
            // event hook             
            $this->onCreating($data);

            // Aplicacion de Mutator
			$data = $this->applyInputMutator($data, 'CREATE');

            $acl = acl();
           
            if (!$acl->hasSpecialPermission('fill_all')){          
                if ($this->inSchema([$this->createdBy()])){
                    if (isset($data[$this->createdBy()])){
                        error("'{$this->createdBy()}' is not fillable!", 400);
                    }
                }  

                if ($this->inSchema([$this->createdAt()])){
                    if (isset($data[$this->createdAt()])){  
                        error("'{$this->createdAt()}' is not fillable!", 400);
                    } 
                }  
            }else{
                $this->fillAll();
            }

            if ($this->inSchema([$this->createdBy()])){
                $this->fill([ $this->createdAt() ]);
                $data[$this->createdBy()] = $this->impersonated_by != null ? $this->impersonated_by : auth()->uid();
            }  

            if (!isset($data[$this->createdAt()]) && $this->inSchema([$this->createdAt()])){
                // dd($this->getFillables(),    'FILLABLES');
                // dd($this->getNotFillables(), 'NOT FILLABLES');

                $this->fill([ $this->createdAt() ]);
                $data[$this->createdAt()] = at();  /// <<-------------- *

                // dd($this->getFillables(),    'FILLABLES');
                // dd($this->getNotFillables(), 'NOT FILLABLES');
            }

            /*
                SI (	
                    $updatedBy está en el schema &&
                    $updatedBy NO es nullable (&&
                    $updatedBy NO tiene valor por defecto)
                ) =>

                Actualizar con el valor del $uid del usuario
            */
            if ($this->inSchema([$this->updatedBy()])){
                if (!in_array($this->updatedBy(), $this->getNullables())){
                    $data[$this->updatedBy()] = $this->impersonated_by != null ? $this->impersonated_by : auth()->uid();
                }
            }
    
            if (!$acl->hasSpecialPermission('transfer')){    
                if ($this->inSchema([$this->belongsTo()])){
                    $data[$this->belongsTo()] = auth()->uid();
                }
            }   

            // dd($this->getRules(), 'RULES'); exit;
            
            $validator = new Validator;
            $ok        = $validator->validate($data, $this->getRules());
               
            if ($ok !== true){
                // error(trans('Data validation error'), 400, $validator->getErrors());
            }  

            try {
                /*
                    SUBRECURSOS

                */

                if (!empty($this->connect_to)){
                    DB::beginTransaction(); ///

                    $dependents = [];
                    $pivot_tables = [];
                    $pivot_table_data = [];

                    $unset = [];
                    foreach ($data as $key => $dato){
                        // dd($dato, $key); // 

                        // Si hay relaciones con otras tablas,....
                        if (is_array($dato)){
                            $related_table = $key;

                            $unset[] = $related_table;

                            if (!in_array($related_table, $this->connect_to)){
                                response()->error("Table $related_table is not connected to ". $this->table_name, 400);
                            }

                            /*
                                Si se recibe un solo campo y esta es una FK,....
                                O sea.. relación de a 1:1 
                            */

                            if (!is_array($dato)){
                                $column_name  = array_keys($dato)[0];
                                $column_value = array_values($dato)[0];

                                // Caso: tabla detalle le apunta al maestro (1 a muchos)
                                if (get_primary_key($this->table_name) == $column_name){
                                    /* 
                                        Solo faltaría relacionar con $related_table > $column_name 

                                        pero... a qué campo? solo se podría si hubiera una sola relación
                                        con esa tabla y sino tendría que decir a través de que campo
                                    */

                                    $schema = $this->getSchema();
                                    $rs = $schema['relationships'];


                                    foreach ($this->connect_to as $tb){                                
                                        $rx = $rs[$tb] ?? null;

                                        if ($rx === null){
                                            continue;
                                        }     

                                        // determino el campo de la relación el que tiene la única FK hacia la tb relacionada
                                        if ($tb == $related_table){
                                            if (count($rx) == 1){
                                                list($tb1, $field1) = explode('.', $rx[0][1]);
                                            }
                                        }
                                    }   

                                    if (isset($field1)){
                                        $fk = $field1;
                                        $data[$fk] = $column_value; 
                                    }
                                }
                            } else {
                                // Podría ser una relación de 1:N o N:M

                                foreach ($dato as $k => $d){
                                    // dd($d, 'D');

                                    if (is_array($d)){

                                        $tb_rel_pri_key = get_primary_key($related_table);
                                        $keys = array_keys($d);

                                        /*
                                            Determino si es posible sea una relación N:M
                                        */
                                        $rel_n_m = false;
                                        
                                        if (!isset($pivot[$this->table_name .'.'. $related_table])){
                                            $pivot[$this->table_name .'.'. $related_table] = get_pivot([$this->table_name, $related_table]);
                                        }

                                        $pivot = get_pivot([$this->table_name, $related_table]);

                                        if (!is_null($pivot)){
                                            $rel_n_m = true;
                                        }                                    

                                        // Estaríamos hablando de una relación de N:M
                                        if ($rel_n_m)
                                        {
                                            if (!in_array($tb_rel_pri_key, $keys) ){
                                                //response()->error("PRIMARY KEY is needed for related table behind a bridge one", 400);

                                                /*
                                                    Verifico si existe UN (1) registro en la tabla relacionada que cumpla las condiciones
                                                */

                                                $rel_ids = DB::table($related_table)
                                                ->where($d)
                                                ->pluck($tb_rel_pri_key);

                                                if (empty($rel_ids)){
                                                    $cnt_rel = 0; 
                                                } else {
                                                    $cnt_rel = count($rel_ids);
                                                }
                
                                                if ($cnt_rel == 0){
                                                    response()->error("Row not found in $related_table. Detail: relation n:m for {$this->table_name}~{$related_table}", 400);
                                                }
                                                
                                                if ($cnt_rel > 1){
                                                    response()->error("Expecting one row for $related_table. Found: $cnt_rel rows", 400);
                                                }

                                                $rel_tb_id = $rel_ids[0];                                                
                                            }


                                            if (!isset($rel_tb_id)){
                                                foreach ($d as $key => $rel_tb_val)
                                                {
                                                    if ($key == $tb_rel_pri_key){
                                                        // Existe el registro?
                                                        if (!isset($related_table_exists[$related_table])){
                                                            $related_table_exists[$related_table] = DB::table($related_table)
                                                            ->find($rel_tb_val)
                                                            ->exists();
                                                        }
    
                                                        if (!$related_table_exists[$related_table]){
                                                            response()->error("Not found", 404, "`$related_table`.`$key` = $rel_tb_val doesn't exist");
                                                        }
                                                    }
                                                }
                                            }                                            

                                            $bridge  = $pivot['bridge'];
                                            
                                            /*
                                                Ojo: los puede que no sea un FK en cada caso sino un array
                                                (esto no es contemplado de momento)
                                            */
                                            $fk_this = $pivot['fks'][$this->table_name]; //
                                            $fk_rel  = $pivot['fks'][$related_table]; //

                                            if (isset($rel_tb_id)){
                                                $rel_tb_val = $rel_tb_id;

                                                $dependents[$related_table][] = [
                                                    $fk_this => '$id_main',
                                                    $fk_rel =>  $rel_tb_val
                                                ];

                                            } else {

                                                /*
                                                    $d es el array asociativo de cada registro en una tabla relacionada (por una puente)
                                                */
                                                foreach ($d as $key => $rel_tb_val)
                                                {
                                                    if (Strings::startsWith($bridge . '.', $key)){
                                                        $bridge_field = substr($key, strlen($bridge)+1);
                                                        //dd($rel_tb_val, $bridge_field);

                                                        $pivot_table_data[] = [$bridge_field, $rel_tb_val];
                                                        continue;                                                    
                                                    } 

                                                    $dependents[$related_table][] = [
                                                        $fk_this => '$id_main',
                                                        $fk_rel =>  $rel_tb_val
                                                    ];
                                                }  

                                            }

                                            
                                            if (!isset($pivot_tables[$related_table])){
                                                $pivot_tables[$related_table] = $bridge;
                                            }

                                        } else {
                                            // Estaríamos hablando de una relación de 1:N
                                    
                                            // ---> toca incluir la FK apuntando a ... $this->table_name

                                            $schema = get_schema_name($related_table)::get();
                                            $rs = $schema['relationships'];

                                            $rr = $rs[$this->table_name] ?? null;

                                            if (is_null($rr)){
                                                response()->error("Unknown error processing table {$related_table}");
                                            }

                                            list($_, $fk) = explode('.', $rr[0][1]);

                                            foreach ($dato as $k => $_dato){
                                                $dato[$k] = array_merge($_dato, [$fk => '$id_main']);
                                            }

                                            $dependents[$related_table] = $dato;
                                            
                                        }
                                       
                                    }
                                }
                            }                        
                            
                        }                        
                    }
                }

                // finalmente destruyo las tablas anidadas dentro de $data
                if (isset($unset)){
                    foreach ($unset as $t){
                        unset($data[$t]);
                    }
                }
                
                // dd($this->getFillables(),    'FILLABLES');
                // dd($this->getNotFillables(), 'NOT FILLABLES');

                // Debería acá comenzar transacción

                $last_inserted_id = DB::table($this->table_name)   
                                    ->when($this->exec === false, function($q){
                                        return $q->dontExec();
                                    })
                                    ->create($data);

                // Tablas dependientes

                if (isset($dependents)){

                    foreach ($dependents as $related_table => $data)
                    {
                        $rel_tb_model      = get_model_name($related_table);
                        $rel_tb_instance   = new $rel_tb_model();

                        $rel_tb_created_by = $rel_tb_instance->createdBy();
                        $rel_tb_updated_by = $rel_tb_instance->updatedBy();

                        if (!isset($pivot_tables[$related_table])){
                            $rel_tb_has_created_by = inSchema([$rel_tb_created_by], $related_table);
                            $rel_tb_has_updated_by = inSchema([$rel_tb_updated_by], $related_table);
                        } else {
                            $bridge = $pivot_tables[$related_table];

                            $rel_tb_has_created_by = inSchema([$rel_tb_created_by], $bridge);
                            $rel_tb_has_updated_by = inSchema([$rel_tb_updated_by], $bridge);
                        }
                        
                        $rel_tb_updated_by_in_nullables = in_array($rel_tb_updated_by, $rel_tb_instance->getNullables());

                        foreach ($data as $ix => $dato)
                        {
                            if ($rel_tb_has_created_by){
                                $data[$ix][$rel_tb_created_by] = $this->impersonated_by != null ? $this->impersonated_by : auth()->uid();
                            }  
                
                            /*
                                SI (	
                                    $updatedBy está en el schema &&
                                    $updatedBy NO es nullable (&&
                                    $updatedBy NO tiene valor por defecto)
                                ) =>
                
                                Actualizar con el valor del $uid del usuario
                            */
                            if ($rel_tb_has_updated_by){
                                if (!$rel_tb_updated_by_in_nullables){
                                    $data[$ix][$rel_tb_updated_by] = $this->impersonated_by != null ? $this->impersonated_by : auth()->uid();
                                }
                            }

                            //dd($dato, 'DATO');

                            foreach ($dato as $field => $val){
                                if ($val === '$' . 'id_main'){
                                    $data[$ix][$field] = $last_inserted_id;
                                }
                            }
                        }

                        #exit; //////

                        if (!isset($pivot_tables[$related_table])){
                            $rel_id = DB::table($related_table)
                            ->when($this->exec === false, function($q){
                                return $q->dontExec();
                            })
                            ->insert($data);
                        } else {
                            // Está pivoteada por una tabla puente
                            $bridge = $pivot_tables[$related_table];

                            if (isset($pivot_table_data)){
                                $cnt_data = count($data);
                                for ($ij=0; $ij<$cnt_data; $ij++){
                                    if (!isset($pivot_table_data[$ij])){
                                        continue;
                                    }
                                    $data[$ij][$pivot_table_data[$ij][0]] = $pivot_table_data[$ij][1];
                                }
                            }

                            $rel_id = DB::table($bridge)
                            ->when($this->exec === false, function($q){
                                return $q->dontExec();
                            })
                            ->insert($data);
                        }

                    }
                }
                    
                DB::commit();             
            } catch (\PDOException $e){
                DB::rollback();

                // solo debug:
                $db = DB::getCurrentDB();
                $tb = DB::getTableName();
                error("Error: creation on `{$db}`.`{$tb}` of resource fails: ". $e->getMessage(), 500, 
                        $this->getLog());
            }

            if ($last_inserted_id !==false){
                // event hook             
                $this->onCreated($data, $last_inserted_id);
        
                response()->send([
                    $this->table_name => $data,
                    $this->getKeyName() => $last_inserted_id
                ], 201);
            }	
            else
                error("Error: creation of resource fails!");

        } catch (InvalidValidationException $e) { 
            error('Validation Error', 400, json_decode($e->getMessage()));
        } catch (\Exception $e) {
            error($e->getMessage());
        }	

    } // 

    
}
