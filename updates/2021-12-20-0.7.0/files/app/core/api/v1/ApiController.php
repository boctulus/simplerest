<?php

namespace simplerest\core\api\v1;

use PDO;
use simplerest\libs\DB;
use simplerest\libs\Url;
use simplerest\libs\Debug;
use simplerest\libs\Arrays;
use simplerest\libs\Factory;
use simplerest\libs\Strings;
use simplerest\libs\Time;  
use simplerest\core\Acl;  
use simplerest\libs\Validator;
use simplerest\core\interfaces\IApi;
use simplerest\core\interfaces\IAuth;
use simplerest\core\FoldersAclExtension;
use simplerest\core\exceptions\SqlException;
use simplerest\core\api\v1\ResourceController;
use simplerest\core\exceptions\InvalidValidationException;
use simplerest\core\interfaces\ISubResources;

abstract class ApiController extends ResourceController implements IApi, ISubResources
{
    public $model_name;
    public $table_name;

    static protected $folder_field;
    static protected $soft_delete = true;
    static protected $connect_to = [];

    protected $is_listable;
    protected $is_retrievable;
    protected $callable = [];
    protected $config;
    protected $impersonated_by;
    protected $conn;
    protected $instance; // main
    protected $tenantid;

    protected $id;
    protected $folder;

    protected $show_deleted;
    protected $ask_for_deleted;


    function __construct($auth = null) 
    {  
        parent::__construct($auth);

        $res = response()->encoded();

        $this->tenantid = Factory::request()->getTenantId();
        if ($this->tenantid !== null){           
            $this->conn = DB::getConnection($this->tenantid);
        }

        if ($this->model_name != null){
            $this->table_name = Strings::camelToSnake(Strings::rTrim('Model', $this->model_name));
        }else {
            if ($this->table_name != null){            
                $this->model_name = implode(array_map('ucfirst',explode('_', $this->table_name))) . 'Model';
            } elseif (preg_match('/([A-Z][a-z0-9_]*[A-Z]*[a-z0-9_]*[A-Z]*[a-z0-9_]*[A-Z]*[a-z0-9_]*)/', get_called_class(), $matchs)){
                $this->model_name = $matchs[1] . 'Model';
                $this->table_name = Strings::camelToSnake($matchs[1]);
            } else {
                $res->sendError("ApiController with undefined Model", 500);
            }  
        }
    

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                if ($this->acl->hasSpecialPermission('read_all')){
                    $this->addCallable('get');
                    $this->is_listable    = true;
                    $this->is_retrievable = true;
                } else {
                    if ($this->acl->hasResourcePermission('show', $this->table_name) || 
                        $this->acl->hasResourcePermission('show_all', $this->table_name)){
                        $this->addCallable('get');
                        $this->is_retrievable = true;
                    }

                    if ($this->acl->hasResourcePermission('list', $this->table_name) ||
                        $this->acl->hasResourcePermission('list_all', $this->table_name)){
                        $this->addCallable('get');
                        $this->is_listable    = true;
                    }
                }  
            break;
            
            case 'POST':
                if ($this->acl->hasSpecialPermission('write_all')){
                    $this->addCallable('post');
                } else {
                    if ($this->acl->hasResourcePermission('create', $this->table_name)){
                        $this->addCallable('post');
                    }
                }  
            break;    

            case 'PUT':
                if ($this->acl->hasSpecialPermission('write_all')){
                    $this->addCallable('put');
                } else {
                    if ($this->acl->hasResourcePermission('update', $this->table_name)){
                        $this->addCallable('put');
                    }
                }  
            break;

            case 'PATCH':
                if ($this->acl->hasSpecialPermission('write_all')){
                    $this->addCallable('patch');
                } else {
                    if ($this->acl->hasResourcePermission('update', $this->table_name)){
                        $this->addCallable('patch');
                    }
                }  
            break;    

            case 'DELETE':
                if ($this->acl->hasSpecialPermission('write_all')){
                    $this->addCallable('delete');
                } else {
                    if ($this->acl->hasResourcePermission('delete', $this->table_name)){
                        $this->addCallable('delete');
                    }
                }  
            break;
        } 

        $perms = $this->acl->getTbPermissions($this->table_name, false);
        
        //dd($perms, 'perms'); /////
        //dd($this->acl->hasSpecialPermission('read_all'));
                    
        
        if ($perms !== NULL)
        {
            // individual permissions *replaces* role permissions
            switch ($_SERVER['REQUEST_METHOD']) {
                /*
                    list_all        64
                    show_all        32 
                    list            16
                    show            8
                    post            4
                    put / patch     2
                    delete          1
                */

                case 'GET': 
                    // sería más eficiente chequear read_all directamente si existe.
                    // usar isri()

                    if ($this->acl->hasResourcePermission('list_all', $this->table_name)){
                        $this->is_listable    = true;
                    } else {
                        $this->is_listable     = (($perms & 16) AND 1) || (($perms & 64) AND 1);
                    }

                    if ($this->acl->hasResourcePermission('show_all', $this->table_name)){
                        $this->is_retrievable    = true;
                    } else {
                        $this->is_retrievable  = (($perms & 8 ) AND 1) || (($perms & 32) AND 1);
                    } 

                    if ($this->is_listable || $this->is_retrievable){
                        $this->addCallable('get');
                    }
                break;
                
                case 'POST': 
                    if (($perms & 4 ) AND 1){
                        $this->addCallable('get');
                    }
                break;    

                case 'PUT':
                    if (($perms & 2 ) AND 1){
                        $this->addCallable('put');
                    }
				break;
                      
                case 'PATCH':
                    if (($perms & 2 ) AND 1){
                        $this->addCallable('putch');
                    }                      
                break;    

                case 'DELETE': 
                    if (($perms & 1 ) AND 1){
                        $this->addCallable('delete');
                    }                    
                break;
            } 
 
        }
        
        $this->impersonated_by = $this->auth->impersonated_by ?? null;

    
        // dd(Acl::getCurrentUid(), 'uid');
        // dd($perms, 'permissions');
        // dd($this->is_listable, 'is_listable?');
        // dd($this->is_retrievable, 'is_retrievable?');
        // dd($this->callable, 'callables');
        // dd($this->impersonated_by, 'impersonated_by');
        //exit;
        
        /*
        if (empty($this->callable)){
            Factory::response()->sendError("Forbidden", 403, "Operation is not permited");
        }
        */
            
        $this->callable = array_merge($this->callable,['head','options']);
        
        //var_export($this->callable);
    }
    
    /**
     * head
     * discard conentent (body)
     * 
     * @param  mixed $id
     *
     * @return void
     */
    function head(int $id = null) {
        if (method_exists($this,'get')){
            ob_start();
            $this->get($id);
            ob_end_clean();
        }
    }

    /**
     * options
     *
     * @return void
     */
    function options() {
        
    }
  
    protected function getModelInstance($fetch_mode = 'ASSOC', bool $reuse = false){
        static $instance;

        if ($reuse && !empty($instance)){
            return $instance;
        }

        $model    = get_model_namespace() . $this->model_name;

        $instance = (new $model(true))->setFetchMode($fetch_mode);
        DB::setModelInstance($instance);

        return $instance;
    }

    static function getConnectable(){
        return static::$connect_to;
    }

    /**
     * get
     *
     * @param  mixed $id
     *
     * @return void
     */
    function get($id = null) {
        global $api_version;

        $_schema  = Factory::request()->shiftQuery('_schema');
        $_rules   = Factory::request()->shiftQuery('_rules');

        if (!empty($_schema)){
            $schema = get_schema_name($this->table_name);
            $res = $schema::get();
            return $res;
        }

        if (!empty($_rules)){
            $schema = get_schema_name($this->table_name);
            $res = $schema::get();
            return [ 'rules' => $res['rules'] ];
        }

        $_related = Factory::request()->shiftQuery('_related');
        $include  = Factory::request()->shiftQuery('include');        
        $_get     = Factory::request()->getQuery();

        $include  = explode(',', $include);


        $this->id     = $id;
        $this->folder = Arrays::shift($_get,'folder');
    
        // event hook
        $this->onGettingBeforeCheck($id);

        // Si el rol no le permite a un usuario ver un recurso aunque se le comparta un folder tampoco podrá listarlo
        
        if ($id == null && !$this->is_listable)
            Factory::response()->sendError('Unauthorized', 403, "You are not allowed to list!!!");    

        if ($id != null && !$this->is_retrievable)
            Factory::response()->sendError('Unauthorized', 401, "You are not allowed to retrieve");  

        try {            
            $this->instance = $this->getModelInstance();
                        
            $data    = []; 
            
            // event hook
            $this->onGettingAfterCheck($id);

            if ($this->ask_for_deleted && !$this->acl->hasSpecialPermission('read_all_trashcan')){
                if ($this->instance->inSchema([$this->instance->belongsTo()])){
                    $_get[$this->instance->belongsTo()] = Acl::getCurrentUid();
                }
            } 

            $id_name = $this->instance->getIdName();

            if ($id == null) { 
                $fs = [
                    $this->instance->createdBy(), 
                    $this->instance->updatedBy(), 
                    $this->instance->deletedBy(), 
                    $this->instance->belongsTo(), 
                    'user_id'
                ];

                foreach ($fs as $f){
                    if (isset($_get[$f])){
                        if ($_get[$f] == 'me')
                            $_get[$f] = Acl::getCurrentUid();
                        elseif (is_array($_get[$f])){
                            foreach ($_get[$f] as $op => $idx){                            
                                if ($idx == 'me'){
                                    $_get[$f][$op] = Acl::getCurrentUid();
                                }else{      
                                    $p = explode(',',$idx);
                                    if (count($p)>1){
                                    foreach ($p as $ix => $idy){
                                        if ($idy == 'me')
                                            $p[$ix] = Acl::getCurrentUid();
                                        }
                                    }
                                    $_get[$f][$op] = implode(',',$p);
                                }
                            }
                        }else{
                            $p = explode(',',$_get[$f]);
                            if (count($p)>1){
                            foreach ($p as $ix => $idx){
                                if ($idx == 'me')
                                    $p[$ix] = Acl::getCurrentUid();
                                }
                            }
                            $_get[$f] = implode(',',$p);
                        }
                    }
                }
        
                //var_export($_get);
                //exit; ////

                if (isset($_get[$this->instance->createdBy()]) && $_get[$this->instance->createdBy()] == 'me')
                    $_get[$this->instance->createdBy()] = Acl::getCurrentUid();

                foreach ($_get as $f => $v){
                    if (!is_array($v) && strpos($v, ',')=== false)
                        $data[$f] = $v;
                } 
            }

            //var_export($_get);
            //exit;
                
            $owned = $this->instance->inSchema([$this->instance->belongsTo()]);

            $_q      = Arrays::shift($_get,'q'); /* search */
            
            
            $fields  = Arrays::shift($_get,'fields');
            $fields  = $fields != NULL ? explode(',',$fields) : NULL;

            $attributes = $this->instance->getAttr();
            
            foreach ((array) $fields as $field){
                if (!in_array($field,$attributes)){
                    Factory::response()->sendError("Unknown field '$field'", 400);
                }
            }

            $exclude = Arrays::shift($_get,'exclude');
            $exclude = $exclude != NULL ? explode(',',$exclude) : NULL;

            foreach ((array) $exclude as $field){
                if (!in_array($field,$attributes)){
                    Factory::response()->sendError("Unknown field '$field' in exclude", 400);
                }
            }

            $ignored = [];

            if ($exclude != null)
                $this->instance->hide($exclude);
                       
            $pretty  = Arrays::shift($_get,'pretty');

            foreach ($_get as $key => $val){
                if ($val == 'NULL' || $val == 'null'){
                    $_get[$key] = NULL;
                }               
            }

            //var_export($_get);
            //exit;

            if ($this->folder !== null)
            {
                // event hook
                $this->onGettingFolderBeforeCheck($id, $this->folder);  

                $f = DB::table('folders')->assoc();
                $f_rows = $f->where(['id' => $this->folder])->get();
        
                if (count($f_rows) == 0 || $f_rows[0]['tb'] != $this->table_name)
                    Factory::response()->sendError('Folder not found', 404);  
        
                $this->folder_access = $this->acl->hasSpecialPermission('read_all_folders') || $f_rows[0]['belongs_to'] == Acl::getCurrentUid()  || FoldersAclExtension::hasFolderPermission($this->folder, 'r');   

                if (!$this->folder_access)
                    Factory::response()->sendError("Forbidden", 403, "You don't have permission for the folder $this->folder");
            }

            if ($id != null)
            {
                $_get = [
                    [$id_name, $id]
                ];                 

                if (empty($this->folder)){               
                    // root, by id          
                         
                    if ($this->acl->isGuest()){                        
                        if ($this->instance->inSchema(['guest_access'])){
                            $_get[] = ['guest_access', 1];
                        } elseif (!empty(static::$folder_field)) {
                            $_get[] = [static::$folder_field, NULL, 'IS'];
                        } 
                                                
                    } else {
                        // avoid guests can see everything with just 'read' permission
                        if ($owned && !$this->acl->hasSpecialPermission('read_all') && 
                            !$this->acl->hasResourcePermission('show_all', $this->table_name))
                        {                              
                            $_get[] = [$this->instance->belongsTo(), Acl::getCurrentUid()];
                        }                            
                    }
                       
                    
                }else{
                    // folder, by id
                    if (empty(static::$folder_field))
                        Factory::response()->sendError("Forbidden", 403, "folder_field is undefined");    
                                           
                    $_get[] = [static::$folder_field, $f_rows[0]['name']];
                    $_get[] = [$this->instance->belongsTo(), $f_rows[0][$this->instance->belongsTo()]];
                }


                // avoid guests can see everything with just 'read' permission
                if ($this->acl->isGuest()){
                    if ($owned){             
                        if (!$this->acl->hasSpecialPermission('read_all') && 
                            (!$this->acl->hasResourcePermission('show_all', $this->table_name))
                        ){
                            $_get[] = [$this->instance->belongsTo(), NULL, 'IS'];
                        }
                    }
                }   

                //var_export($_get);

                $rows = $this->instance->where($_get)->get($fields); 
                if (empty($rows))
                    Factory::response()->sendError('Not found', 404, $id != null ? "Register with id=$id in table '{$this->table_name}' was not found" : '');
                else{
                    // event hook
                    $this->onGot($id, 1);
                    $this->webhook('show', $rows[0], $id);


                    /*
                         HATEOAS
                    */
                    if (!empty($_related) || !empty($include))
                    {
                        $res = $this->getSubResources($this->table_name, static::$connect_to, $this->instance, $this->tenantid);
                        $res = $res[0];
                    } else {
                        $res = $rows[0];
                    }

                    Factory::response()->send($res);
                }
            }else{    
                // "list    
                
                $props    = Arrays::shift($_get,'props');
                $group_by = Arrays::shift($_get,'groupBy');
                $having   = Arrays::shift($_get,'having');
             
                $get_limit = function(&$limit){
                    if ($limit == NULL)
                        $limit = min($this->config['paginator']['max_limit'], $this->config['paginator']['default_limit']);
                    else{
                        if ($limit !=0)
                            $limit = min($limit, $this->config['paginator']['max_limit']);
                        else    
                            $limit = $this->config['paginator']['max_limit'];
                    } 
                };
   
                $page  = Arrays::shift($_get,'page');
                $page_size = Arrays::shift($_get,'pageSize');

                if ($page != null)
                    $page = (int) $page;

                if ($page_size  != null)
                    $page_size  = (int) $page_size;

                if ($page != NULL || $page_size != NULL){
                    $get_limit($page_size);

                    if ($page == NULL)
                        $page = 1;

                    $limit  = $page_size;
                    $offset = $page_size * ($page -1);

                    //var_export(['limit' =>$limit, 'offset' => $offset]);
                }else{
                    $limit  = Arrays::shift($_get,'limit');
                    $offset = Arrays::shift($_get,'offset',0);                    

                    $get_limit($limit);
                    $page_size = $limit;
                }

                $order  = Arrays::shift($_get,'orderBy');

                
                // event hook
                $this->onGettingAfterCheck2($id);
                
                #var_export($_get);

                // Importante:
                $_get = Arrays::nonassoc($_get);

                $allops = ['eq', 'gt', 'gteq', 'lteq', 'lt', 'neq'];
                $eqops  = ['=',  '>' , '>=',   '<=',   '<',  '!=' ];

                foreach ($_get as $key => $val){
                    if (is_array($val)){

                        $campo = $val[0];                       

                        if (is_array($val[1])){                             

                            #var_export($val[1]); ///

                            foreach ($val[1] as $op => $v){

                                #var_export([$op, $v]); ///
                                 
                                switch ($op) {
                                    case 'contains':
                                        $_get[$key] = [$campo, '%'.$v.'%', 'like'];
                                        $ignored[] = $campo;
                                        $data[$campo][] = $v;
                                    break;
                                    case 'notContains':
                                        $_get[$key] = [$campo, '%'.$v.'%', 'not like'];
                                        $ignored[] = $campo;
                                        $data[$campo][] = $v;
                                    break;
                                    case 'startsWith':
                                        $_get[$key] = [$campo, $v.'%', 'like'];
                                        $ignored[] = $campo;
                                        $data[$campo][] = $v;
                                    break;
                                    case 'notStartsWith':
                                        $_get[$key] = [$campo, $v.'%', 'not like'];
                                        $ignored[] = $campo;
                                        $data[$campo][] = $v;
                                    break;
                                    case 'endsWith':
                                        $_get[$key] = [$campo, '%'.$v, 'like'];
                                        $ignored[] = $campo;
                                        $data[$campo][] = $v;
                                    break;
                                    case 'notEndsWith':
                                        $_get[$key] = [$campo, '%'.$v, 'not like'];
                                        $ignored[] = $campo;
                                        $data[$campo][] = $v;
                                    break;
                                    case 'in':                                    
                                        if (strpos($v, ',')!== false){    
                                            $vals = explode(',', $v);
                                            $_get[$key] = [$campo, $vals, 'IN']; 

                                            foreach ($vals as $_v){
                                                $data[$campo][] = $_v;
                                            }
                                        }                                         
                                    break;
                                    case 'notIn':
                                        if (strpos($v, ',')!== false){    
                                            $vals = explode(',', $v);
                                            $_get[$key] = [$campo, $vals, 'NOT IN'];

                                            foreach ($vals as $_v){
                                                $data[$campo][] = $_v;
                                            }
                                        }                                         
                                    break;
                                    case 'between':
                                        if (substr_count($v, ',') == 1){    
                                            $vals = explode(',', $v);
                                            unset($_get[$key]);

                                            $min = min($vals[0],$vals[1]);
                                            $max = max($vals[0],$vals[1]);

                                            $_get[] = [$campo, $min, '>='];
                                            $_get[] = [$campo, $max, '<='];

                                            $data[$campo][] = $min;
                                            $data[$campo][] = $max;
                                        }                                         
                                    break;
                                    case 'notBetween':
                                        throw new \Exception("Operator notBetween is not implemented");  
                                    break;
                                    default:
                                        // 'eq', 'gt', ...

                                        $found = false;
                                        foreach ($allops as $ko => $oo){
                                            if ($op == $oo){
                                                $op = $eqops[$ko];
                                                unset($_get[$key]);
                                                $_get[] = [$campo, $v, $op];
                                                $data[$campo][] = $v; // 
                                                $found = true;                            
                                                break;                                    
                                            }                                    
                                        }

                                        if (!$found)
                                            Factory::response()->sendError("Unknown operator '$op'", 400);
                                    break;
                                }
                            }
                            
                        }else{
                            
                            /*
                                null! tiene un funcionamiento muy limitado porque la validación hace que
                                no funcione si el campo no es un string o si la lontitud es inferior a 5 o 
                                sea a la de "null!"

                            */
                            if (count($val) == 2){
                                if ($val[1] == 'null!'){
                                    unset($_get[$key]); 
                                    
                                    $_get[$key] = [$val[0],  NULL, 'IS'];
                                }
                            }

                            /*
                                Cuando no se especifica valor como en ?description= debería buscar por un
                                string vacio y de hecho al debuguear el SQL se lee por ejemplo:  

                                SELECT * FROM networks WHERE (description = '') AND deleted_at IS NULL LIMIT 10;

                                Sin embargo....... no arroja registros!!!! <-- BUG
                            */
                            

                            // IN
                            $v = $val[1];
                            if (strpos($v, ',')!== false){    
                                $vals = explode(',', $v);
                                $_get[$key] = [$campo, $vals];    
                                
                                foreach ($vals as $_v){
                                   $data[$campo][] = $_v;
                                }
                            } 
                        }   
                        
                    }                      
                }

                // avoid guests can see everything with just 'read' permission
                if ($this->acl->isGuest()){
                    if ($owned){             
                        if (!$this->acl->hasSpecialPermission('read_all') && 
                            (!$this->acl->hasResourcePermission('list_all', $this->table_name))
                        ){
                            $_get[] = [$this->instance->belongsTo(), NULL, 'IS'];
                        }
                    }
                }   


                /*
                    Query a sub-recursos (parte I)
                */
                
                $joins = [];
                foreach ($_get as $k => $arr){
                    $f = $arr[0];
                    if (!in_array($f,$attributes)){
                        if (preg_match('/([a-z0-9_-]+)\.([a-z0-9_-]+)/i', $f, $matches)){
                            $_tb = $matches[1];
                            $_f  = $matches[2];

                            if (empty(static::$connect_to) || !in_array($_tb, static::$connect_to)){
                                response()->sendError("Entity '$_tb' is not available as subresource", 400);
                            }

                            // Chequeo que el campo SI exista en la tabla del sub-recurso
                            $sub_sc = get_schema($_tb);
                            $sub_at = array_keys($sub_sc['attr_types']);

                            if (!in_array($_f, $sub_at)){
                                response()->sendError("Entity '$_tb' does not have a field named '$_f'", 400);
                            }

                            /*
                                Cuando hago el JOIN le pongo un nombre al alias que es "__{tabla}__"
                            */

                            $this->instance->doQualify();
                            $joins[] = "$_tb as __{$_tb}__";

                            $_get[$k][0] ="__{$_tb}__.$_f";
                        } else {
                            // Si se pide algo que involucra un campo no está en el attr_types lanzar error
                            response()->sendError("Unknown field '$arr[0]'", 400);
                        }
                    }
                }

                if (!empty($joins)){
                    foreach ($joins as $join){
                        $this->instance->join($join);
                    }
                }

                if (empty($this->folder)){
                    // root, sin especificar folder ni id (lista)   // *             
                    if (!$this->acl->isGuest() && $owned && 
                        !$this->acl->hasSpecialPermission('read_all') &&
                        !$this->acl->hasResourcePermission('list_all', $this->table_name) ){
                        $_get[] = [$this->instance->belongsTo(), Acl::getCurrentUid()];     
                    }       
                }else{
                    // folder, sin id

                    if (empty(static::$folder_field)){
                        Factory::response()->sendError("Forbidden", 403, "'folder_field' is undefined");   
                    }    

                    $_get[] = [static::$folder_field, $f_rows[0]['name']];
                    $_get[] = [$this->instance->belongsTo(), $f_rows[0][$this->instance->belongsTo()]];
                }
                
                if ($id === null){
                    $validation = (new Validator())->setRequired(false)->ignoreFields($ignored)->validate($this->instance->getRules(),$data);
                    
                    if ($validation !== true){
                        throw new InvalidValidationException(json_encode($validation));
                    }                        
                }      

                if (!empty($this->folder)) {
                    // event hook
                    $this->onGettingFolderAfterCheck($id, $this->folder);
                }
           
                $pretty = (!empty($pretty) && !in_array($pretty, ['false', 0, 'off']));      

                //dd($_get); ////
                //exit;

                $query = Factory::request()->getQuery();
                
                if (isset($query['offset'])) 
                    unset($query['offset']);

                if (isset($query['limit'])) 
                    unset($query['limit']);

                if (isset($query['page'])) 
                    unset($query['page']);

                if (!isset($query['pageSize'])) 
                    $query['pageSize'] = $page_size;

                
                //////////
    
                // MIN, MAX, SUM, COUNT, AVG
                if (preg_match('/(min|max|sum|avg|count)\(([a-z\*]+)\)( as [a-z]+)?/i', $props, $matches)){
                    $ag_fn = strtolower($matches[1]);
                    $ag_ff = $matches[2];
                
                    if (preg_match('/[a-z]+\([a-z\*]+\) as ([a-z]+)/i', $props, $matches)){
                        $ag_alias = $matches[1];
                    }else
                        $ag_alias = NULL;
                }

                // WHERE
                $this->instance->where($_get);
                //dd($_get);

                // GROUP BY
                if ($group_by != NULL){
                    $group_by = explode(',', $group_by);
                    $this->instance->groupBy($group_by);                   
                }                    

                // HAVING
                if (preg_match('/([a-z]+)\(([a-z\*]+)\)([><=]+)([0-9\.]+)/i', $having, $matches)){
                    $hv_fn = strtoupper($matches[1]);
                    $hv_ff = $matches[2];
                    $hv_op = $matches[3];
                    $hv_vv = $matches[4];                   

                    //var_export($matches);                    
                    $this->instance->having(["$hv_fn($hv_ff)", $hv_vv, $hv_op]);
                }elseif (preg_match('/([a-z]+)([><=]+)([0-9\.]+)/i', $having, $matches)){
                    $hv_fn_alias = $matches[1];
                    $hv_op = $matches[2];
                    $hv_vv = $matches[3]; 

                    $this->instance->having([$hv_fn_alias, $hv_vv, $hv_op]);
                }

                // ORDER BY
                if ($order !=  NULL)
                    $this->instance->orderBy($order);
                
                // LIMIT
                if ($limit != NULL)
                    $this->instance->limit($limit);

                // OFFSET
                if ($offset != NULL)
                    $this->instance->offset($offset);

                /*
                    Debe incluir los alias 
                */
                if (!empty($fields))
                    $this->instance->select($fields);


                if (isset($ag_fn)){
                    $rows = $this->instance->$ag_fn($ag_ff, $ag_alias);
                }else {
                    /*
                        HATEOAS
                    */

                    $_related = ($_related != "0" && $_related != "false");
                    $id_name  = $this->instance->getSchema()['id_name'];
                    
                    if ( ($_related || !empty($include[0])) && (empty($fields) || in_array($id_name, $fields) ))
                    {   
                        if (!empty($include) && !empty($include[0])){
                            static::$connect_to = array_intersect(static::$connect_to, $include);
                        }

                        $rows = $this->getSubResources($this->table_name, static::$connect_to, $this->instance, $this->tenantid);
                    } else {
                        //
                        $rows = $this->instance->get();
                    }
                }                             
                    
                
                //dd($this->instance->dd(), 'SQL');
                //dd($rows);
                
                $res = Factory::response()->setPretty($pretty);

                /*
                    Falta paginar cuando hay groupBy & having
                */

                $total = null;

                //  pagino solo sino hay funciones agregativas
                if (!isset($ag_fn))
                {
                    $total = (int) (
                        DB::table($this->table_name)
                        ->column()

                        // Query a sub-recursos (parte II)                
                        ->when(!empty($joins), function($q) use ($joins) {
                            $q->doQualify();
                            foreach ($joins as $join){
                                $q->join($join);
                            }
                        })
                        ->where($_get)
                        ->count());
    
                    $page_count = ceil($total / $limit);

                    if ($page == NULL)
                        $page = ceil($offset / $limit) +1;
                    
                    if ($page +1 <= $page_count){
                        $query['page'] = ($page +1);

                        if (isset($_GET['tenantid'])){
                            $query['tenantid'] = $_GET['tenantid'];
                        }

                        if (isset($_GET['_related'])){
                            $query['_related'] = $_GET['_related'];
                        }

                        $api_slug = $this->config['REMOVE_API_SLUG'] ? '' : '/api' ;
                        $next =  http_protocol() . '://' . $_SERVER['HTTP_HOST'] . $api_slug . '/' . $api_version . '/'. $this->table_name . '?' . $query = str_replace(['%5B', '%5D', '%2C'], ['[', ']', ','], http_build_query($query));
                    }else{
                        $next = 'null';
                    }        

                    $pg = [ 
                        'total' => $total,
                        'count' => count($rows),
                        'currentPage' => $page,
                        'totalPages' => $page_count, 
                        'pageSize' => $page_size,
                        'nextUrl' => $next                                              
                    ];  

                    $res->setPaginator($pg);
                }
                
                               
                // event hooks
                if ($this->folder){
                    $this->onGotFolder($id, $total, $this->folder);
                }

                // event hook
                $this->onGot($id, $total);
                $this->webhook('list', $rows);
                
                
                /*
                        HATEOAS
                */

                // if (!empty($props)){    
                //     $res = $rows;
                // }
                
                if ($this->config['include_enity_name']){
                    $res = [$this->table_name => $rows];
                }

                response()->send($res);

            }
        
        } catch (InvalidValidationException $e) { 
            response()->sendError('Validation Error', 400, json_decode($e->getMessage()));
        } catch (SqlException $e) { 
            response()->sendError('SQL Exception', 500, json_decode($e->getMessage())); 
        } catch (\PDOException $e) {    
            $db = DB::getCurrentDB();
            response()->sendError('PDO Exception', 500, $e->getMessage(). ' - '. $this->instance->getLog() . " - database: '{$db}' - table: '{$this->instance->getTableName()}'"); 
        } catch (\Exception $e) {   
            response()->sendError($e->getMessage());
        }	    
    } // 


    /**
     * post
     *
     * @return void
     */
    function post() {
        $data = Factory::request()->getBody(false);

        if (empty($data))
            Factory::response()->sendError('Invalid JSON',400);

        $this->instance = $this->getModelInstance();

        $id = $data[$this->instance->getIdName()] ?? null;
        $this->folder = $this->folder = $data['folder'] ?? null;

        try {
            $this->instance->connect();

            // event hook             
            $this->onPostingBeforeCheck($id, $data);
           
            if (!$this->acl->hasSpecialPermission('fill_all')){          
                if ($this->instance->inSchema([$this->instance->createdBy()])){
                    if (isset($data[$this->instance->createdBy()])){
                        Factory::response()->sendError("'{$this->instance->createdBy()}' is not fillable!", 400);
                    }
                }  

                if ($this->instance->inSchema([$this->instance->createdAt()])){
                    if (isset($data[$this->instance->createdAt()])){  
                        Factory::response()->sendError("'{$this->instance->createdAt()}' is not fillable!", 400);
                    } 
                }  
            }else{
                $this->instance->fillAll();
            }

            if ($this->instance->inSchema([$this->instance->createdBy()])){
                $data[$this->instance->createdBy()] = $this->impersonated_by != null ? $this->impersonated_by : Acl::getCurrentUid();
            }  

            if (!isset($data[$this->instance->createdAt()]) && $this->instance->inSchema([$this->instance->createdAt()])){
                $data[$this->instance->createdAt()] = at();
            }

            /*
                SI (	
                    $updatedBy está en el schema &&
                    $updatedBy NO es nullable (&&
                    $updatedBy NO tiene valor por defecto)
                ) =>

                Actualizar con el valor del $uid del usuario
            */
            if ($this->instance->inSchema([$this->instance->updatedBy()])){
                if (!in_array($this->instance->updatedBy(), $this->instance->getNullables())){
                    $data[$this->instance->updatedBy()] = $this->impersonated_by != null ? $this->impersonated_by : Acl::getCurrentUid();
                }
            }
    
            if (!$this->acl->hasSpecialPermission('transfer')){    
                if ($this->instance->inSchema([$this->instance->belongsTo()])){
                    $data[$this->instance->belongsTo()] = Acl::getCurrentUid();
                }
            }   
            
            if ($this->folder !== null)
            {
                if (empty(static::$folder_field))
                    Factory::response()->sendError("Forbidden", 403, "'folder_field' is undefined");

                // event hook    
                $this->onPostingFolderBeforeCheck($id, $data, $this->folder);

                $f = DB::table('folders');
                $f_rows = $f->where(['id' => $this->folder])->get();
                      
                if (count($f_rows) == 0 || $f_rows[0]['tb'] != $this->table_name)
                    Factory::response()->sendError('Folder not found', 404); 
        
                if ($f_rows[0][$this->instance->belongsTo()] != Acl::getCurrentUid()  && !FoldersAclExtension::hasFolderPermission($this->folder, 'w'))
                    Factory::response()->sendError("Forbidden", 403, "You have not permission for the folder $this->folder");

                unset($data['folder']);    
                $data[static::$folder_field] = $f_rows[0]['name'];
                $data[$this->instance->belongsTo()] = $f_rows[0][$this->instance->belongsTo()];    
            }    

            $validado = (new Validator)->validate($this->instance->getRules(), $data);
            if ($validado !== true){
                Factory::response()->sendError(_('Data validation   error'), 400, $validado);
            }  

            if (!empty($this->folder)) {
                // event hook    
                $this->onPostingFolderAfterCheck($id, $data, $this->folder);
            }

            // event hook             
            $this->onPostingAfterCheck($id, $data);

            try {
                /*
                    HATEOAS

                */

                if (!empty(static::$connect_to)){
                    DB::beginTransaction(); ///

                    $dependents = [];
                    $pivot_tables = [];
                    $pivot_table_data = [];

                    $unset = [];
                    foreach ($data as $key => $dato){

                        // Si hay relaciones con otras tablas,....
                        if (is_array($dato)){
                            $related_table = $key;

                            $unset[] = $related_table;

                            if (!in_array($related_table, static::$connect_to)){
                                response()->sendError("Table $related_table is not connected to ". $this->table_name, 400);
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

                                    $schema = $this->instance->getSchema();
                                    $rs = $schema['relationships'];


                                    foreach (static::$connect_to as $tb){                                
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
                                                //response()->sendError("PRIMARY KEY is needed for related table behind a bridge one", 400);

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
                                                    response()->sendError("Row not found in $related_table", 400);
                                                }
                                                
                                                if ($cnt_rel > 1){
                                                    response()->sendError("There are more than one rows in $related_table matching with sent data", 400);
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
                                                            response()->sendError("Not found", 404, "`$related_table`.`$key` = $rel_tb_val doesn't exist");
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
                                                response()->sendError("Something is wrong trying to link to {$related_table}");
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
                

                // Debería acá comenzar transacción

                $last_inserted_id = DB::table($this->table_name)
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
                                $data[$ix][$rel_tb_created_by] = $this->impersonated_by != null ? $this->impersonated_by : Acl::getCurrentUid();
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
                                    $data[$ix][$rel_tb_updated_by] = $this->impersonated_by != null ? $this->impersonated_by : Acl::getCurrentUid();
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
                Factory::response()->sendError("Error: creation on `{$db}`.`{$tb}` of resource fails: ". $e->getMessage(), 500, 
                        $this->instance->getLog());
            }

            if ($last_inserted_id !==false){
                // event hooks
                $this->onPostFolder($last_inserted_id, $data, $this->folder);                
                $this->onPost($last_inserted_id, $data);
                $this->webhook('create', $data, $last_inserted_id);

                Factory::response()->send([
                    $this->table_name => $data,
                    $this->instance->getKeyName() => $last_inserted_id
                ], 201);
            }	
            else
                Factory::response()->sendError("Error: creation of resource fails!");

        } catch (InvalidValidationException $e) { 
            Factory::response()->sendError('Validation Error', 400, json_decode($e->getMessage()));
        } catch (\Exception $e) {
            Factory::response()->sendError($e->getMessage());
        }	

    } // 
    

    protected function modify($id = NULL, bool $put_mode = false)
    {
        if ($id == null)
            response()->sendError("Missing id",400);

        $data = request()->getBody(false);

        if (empty($data))
            response()->sendError('Invalid JSON',400);
        
        $append_mode = request()->shiftQuery('_append', false);
        if ($append_mode === 'false' || $append_mode === 0){
            $append_mode = false;
        }

        $this->id = $id;    
        $this->folder = $this->folder = $data['folder'] ?? null;
        
        try {                       
            // event hook
            $this->onPuttingBeforeCheck($id, $data);

			if (!$this->acl->hasSpecialPermission('lock')){
                $instance0 = $this->getModelInstance();
                $row = $instance0->where([$instance0->getIdName(), $id])->first();

                if (isset($row['is_locked']) && $row['is_locked'] == 1)
                    Factory::response()->sendError("Forbidden", 403, "Locked by Admin");
            }

            // Creo una instancia
            $this->instance = $this->getModelInstance();
            
            $id_name = $this->instance->getIdName();

            if (!$this->acl->hasSpecialPermission('fill_all')){
                $unfill = [ 
                            $this->instance->deletedAt(),
                            $this->instance->deletedBy(),
                            $this->instance->createdAt(),
                            $this->instance->createdBy()
                ];    

                $this->instance->unfill($unfill);

                foreach ($unfill as $uf){
                    if ($this->instance->inSchema([$uf])){
                        if (isset($data[$uf])){
                            Factory::response()->sendError("$uf is not fillable", 400);
                        }
                    }  
                }

            }else{
                $this->instance->fillAll();                
            }

            if ($this->instance->inSchema([$this->instance->updatedBy()])){
                $data[$this->instance->updatedBy()] = $this->impersonated_by != null ? $this->impersonated_by : Acl::getCurrentUid();
            }  

            $owned = $this->instance->inSchema([$this->instance->belongsTo()]);            

            if ($this->folder !== null)
            {
                if (empty(static::$folder_field))
                    Factory::response()->sendError("'folder_field' is undefined", 403);

                // event hook    
                $this->onPuttingFolderBeforeCheck($id, $data, $this->folder);

                $f = DB::table('folders')->assoc();
                $f_rows = $f->where(['id' => $this->folder])->get();
                      
                if (count($f_rows) == 0 || $f_rows[0]['tb'] != $this->table_name)
                    Factory::response()->sendError('Folder not found', 404); 
        
                if ($f_rows[0][$this->instance->belongsTo()] != Acl::getCurrentUid()  && !FoldersAclExtension::hasFolderPermission($this->folder, 'w') && !$this->acl->hasSpecialPermission('write_all_folders'))
                    Factory::response()->sendError("You have not permission for the folder $this->folder", 403);

                $this->folder_name = $f_rows[0]['name'];

                // Creo otra nueva instancia
                $instance2 = $this->getModelInstance();

                if (count($instance2->where([$id_name => $id, static::$folder_field => $this->folder_name])->get()) == 0)
                    Factory::response()->code(404)->sendError("Register for id=$id doesn't exist");

                unset($data['folder']);    
                $data[static::$folder_field] = $f_rows[0]['name'];
                $data[$this->instance->belongsTo()] = $f_rows[0][$this->instance->belongsTo()];    
                
            } else {

                $this->instance2 = $this->getModelInstance(); 

                // event hook    
                $this->onPuttingBeforeCheck2($id, $data);

                $rows = $this->instance2->where([$id_name => $id])->get();

                if (count($rows) == 0){
                    Factory::response()->code(404)->sendError("Register for id=$id doesn't exist!");
                }

                if  ($owned && !$this->acl->hasSpecialPermission('write_all') && $rows[0][$this->instance->belongsTo()] != Acl::getCurrentUid()){
                    Factory::response()->sendError('Forbidden', 403, 'You are not the owner!');
                } 
                    
            }                 

            /* 
                HATEOAS 
            */
            if (!empty(static::$connect_to)){                
              
                // Me quedo solo con los subrecursos
                $sub_res = array_filter($data, function($v, $k){
                    if (is_array($k)){
                        return;
                    }
                    
                    if (in_array($k, static::$connect_to)){
                        return $v;
                    };
                }, ARRAY_FILTER_USE_BOTH);

                foreach ($sub_res as $tb => $dati){
                    unset($data[$tb]);

                    $rel_type = get_rel_type($this->table_name, $tb);
                    // d($rel_type, 'table '. $tb);
                    //d($dati, 'DATI');

                    switch ($rel_type){
                        case '1:1':
                        case 'n:1':
                            $fks = get_fks($this->table_name, $tb);

                            // d($dati, 'dati');   
                            if (is_array($dati)){
                                $keys = array_keys($dati);

                                $fks = get_fks($this->table_name, $tb);                                

                                /*
                                    Asumo una sola FK de momento entre dos tablas --- es falso !

                                    El nombre de la FK en tales casos podría darse de la siguiente manera:  

                                        "users": [
                                            {
                                                "created_by": 173
                                            }
                                        ]
                                
                                    y también claro,    
                                    
                                        "users": [
                                            {
                                                "created_by": 173,
                                                "belongs_to": 142
                                            }
                                        ]
                                */

                                //d($fks, 'fks');

                                // Si no es asociativo..... me quedo con el array interno
                                if (isset($dati[0])){
                                    $dati = $dati[0];
                                }

                                //d($dati, 'dati'); 
                      
                                foreach ($fks as $_fk){                                                       
                                    if (in_array($_fk, $keys, true)){
                                        // me están enviando la pri key (casos A y C)

                                        // d($_fk, '$_fk');
                                        // d($keys, 'keys');

                                        if (count($keys) === 1){
                                            // caso A
                                            $fk   = $_fk; 
                                            $dati = $dati[$fk];
                                        } else {
                                            // caso C
                                        }
                                    } else {
                                        // caso B
                                    }
                                }

                            } else {
                                // Si es un escalar:
                                $fk = $fks[0];
                            }
                            
                            $data[$fk] = $dati;
                        break;

                        case '1:n':
                            $fks = get_fks($tb, $this->table_name);

                            //d($dati, 'dati');
                            // d($fks, 'FKs');
                                           
                            // Limitación a remover
                            if (count($fks)>1){
                                response()->sendError("At this moment it's possible to update tables with only one relationship. Detail: $tb -> {$this->table_name}");
                            }

                            if (is_array($dati[0])){
                                $pri_rel = get_primary_key($tb);
                                d($pri_rel);

                                $keys = [];
                                foreach ($dati as $ix => $dato){
                                    $keys = array_keys($dato);         
                                    //d($keys, 'keys');

                                    if (in_array($pri_rel, $keys, true)){
                                        if (count($keys) === 1){
                                            // caso A
                                            d("caso A");
                                           
                                            $fk   = $fks[0]; 
                                            d($fk, 'fk');
                                           
                                        } else {
                                            // caso C
                                            d("caso C");
                                        }
                                    } else {
                                        // caso B
                                        d("caso B");
                                    }
                                    
                                }

                                //exit; ////////    

                            } else {
                                // Es un escalar (o literal)
                                $fk = $fks[0];
                            }   
                  

                            if (!$append_mode){
                                $prev = DB::table($tb)->where([$fk => $id])->pluck(get_primary_key($tb));
                                
                                if ($prev === false){
                                    $prev = [];
                                }
                            }

                            foreach ($dati as $dato){
                                $m  = DB::table($tb);
                                $ok = $m->find($dato)
                                //->dontExec()
                                ->update([$fk => $id]);
                                
                                //d($ok, $m->getLog());
                            }

                            d($append_mode, 'append mode');

                            if ($append_mode == false){
                                // borro registros previos

                                // d($prev, 'prev');
                                // d($dati, 'dati');
                                $diff = array_diff($prev, array_column($dati, 'id_tag'));
                                d($diff, 'diff');
                                
                                if (!empty($diff)){
                                    $m = DB::table($tb);

                                    $ok = $m->whereIn(get_primary_key($tb), $diff)
                                    //->dontExec()
                                    ->delete();

                                    //d($ok, $m->getLog());
                                }
                            }   

                        break;
                        
                        case 'n:m':
                            $pivot_ay = get_pivot([$this->table_name, $tb]);
                            $bridge = $pivot_ay['bridge'] ?? null;

                            if ($bridge === null){
                                throw new \Exception("Bridge table not found for $this->table_name and $tb");
                            }

                            $fks_bridge = $pivot_ay['fks'];

                            // Limitación a remover
                            if (count($fks_bridge)>2){
                                response()->sendError("At this moment it's possible to update bridge tables with only one relationship to each side. Detail: $tb -> {$this->table_name}");
                            }
                            

                            $pri_table = $fks_bridge[$this->table_name];
                            $fk_tb = $fks_bridge[$tb];

                            // Elimino la FK hacia la tabla "principal" de las FKs del puente
                            unset($fks_bridge[$this->table_name]);
                            $fks = array_values($fks_bridge);

                            // d($bridge, 'BRIDGE');
                            // d($fks, 'FKS');

                            d($fk_tb, 'fk_tb');
                            // d($dati, 'dati');

                            foreach ($fks as $_fk){     
                                if (is_array($dati[0])){
                                    $keys = [];

                                    foreach ($dati as $ix => $dato){
                                        $keys = array_keys($dato);         
                                        
                                        // d($keys, 'keys');
                                        // d($dato, 'dato');

                                        if (in_array($fk_tb, $keys, true)){                        
                                            if (count($keys) === 1){
                                                // caso A
                                                d("caso A");
                                            
                                                // $fk   = $fks[0]; 
                                                // d($fk, 'fk');
                                            
                                            } else {
                                                // caso C
                                                d("caso C");
                                            }
                                        } else {
                                            // caso B
                                            d("caso B");
                                        }
                                        
                                    }

                                    //exit; ////////    

                                } else {
                                    // Es un escalar (o literal)
                                    //$fk = $fks[0];
                                }   
                            }    

                            exit; ///////
                        
                  
                            // Ids a de la otra tabla actualmente apuntados por la puente
                            $prev = DB::table($bridge)->where([$pri_table => $id])->pluck($fk_tb) ?? [];
                
                            //d($prev, 'PREV');
                  
                            if (!$append_mode){
                                // borro registros previos

                                $diff = array_diff($prev, $dati);
                                // d($diff, 'diff');
                                
                                if (!empty($diff)){
                                    $m = DB::table($bridge);

                                    $ok = $m->whereIn($fk_tb, $diff)
                                    //->dontExec()
                                    ->delete();

                                    //d($ok, $m->getLog());
                                }
                            }

                            // apppend mode
                            if (isset($diff)){                                
                                $dati = array_diff($dati, $prev);
                            } else {
                                // d($dati, 'VALS');
                                $intersect = array_intersect($dati, $prev);
                                // d($intersect, 'INTERSECT');
                                
                                $dati = array_diff($dati, $intersect);
                                // d("Substracting ...");
                                // d($dati, 'DATI');
                            }

                            // d($prev, 'PREV');
                            // d($dati, 'TO INSERT');


                            // exit; /////////


                            $ins = [];
                            foreach ($dati as $dato){
                                $ins[] = [
                                  $pri_table => $id,
                                  $fk_tb => $dato
                                ];
                            }

                            if (!empty($ins)){
                                $m = DB::table($bridge);
                                $m->insert($ins);
                            }
                  
                        break;
                    }
                }
            }

            /*
                 Si hubiera un array de arrays.... acomodo:   

                 "users": [
                    {
                        "created_by": 173,
                        "belongs_to": 142
                    }
                ],
            */            
            foreach ($data as $k => $v){                
                if (is_array($v)){
                    foreach ($v as $h => $vi){
                        $data[$h] = $vi;
                    }

                    unset($data[$k]);
                } 
            }   

            // This is not 100% right but....
            foreach ($data as $k => $v){
                if (strtoupper($v) == 'NULL' && $this->instance->isNullable($k)){
                    $data[$k] = NULL;
                } 
            }   

            $validado = (new Validator())->setRequired($put_mode)->validate($this->instance->getRules(), $data);
            if ($validado !== true){
                Factory::response()->sendError(_('Data validation error'), 400, $validado);
            }

            // agregado dic-3
            $this->instance->fill([$this->instance->updatedBy()]);

            if (!empty($this->folder)) {
                // event hook 
                $this->onPuttingFolderAfterCheck($id, $data, $this->folder);
            }

            // event hook
            $this->onPuttingAfterCheck($id, $data);

            if (!$owned && $this->show_deleted && !$this->acl->hasSpecialPermission('write_all_trashcan')){
                if ($this->instance->inSchema([$this->instance->belongsTo()])){
                    $data[$this->instance->belongsTo()] = Acl::getCurrentUid();
                } 
            } 
                     
            if (!$this->acl->hasSpecialPermission('fill_all')){
                $spp = $this->instance->getAutoFields();

                foreach ($spp as $sp){
                    if (isset($data[$sp])){
                        unset($data[$sp]); 
                    }
                }
            }

            try {
                $affected = $this->instance->where([$id_name => $id])->update($data);
                //var_dump($this->instance->getLog());
            } catch (\Exception $e){
                //$affected = $this->instance->where([$id_name => $id])->dontExec()->update($data);
                //dd($this->instance->getLog());
                response()->sendError($e->getMessage());
            }

            if ($affected !== false) {

                // even hooks        	    
                $this->onPutFolder($id, $data, $affected, $this->folder);                
                $this->onPut($id, $data, $affected);
                $this->webhook('update', $data, $id);
                
                Factory::response()->send($data);
            } else {
                Factory::response()->sendError("Error in PATCH",404);
            }	

        } catch (InvalidValidationException $e) { 
            Factory::response()->sendError('Validation Error', 400, json_decode($e->getMessage()));
        } catch (\PDOException $e){
            // solo para debug !
            Factory::response()->sendError("Error: creation of resource fails: ". $e->getMessage(), 500);
        } catch (\Exception $e) {
            Factory::response()->sendError("Error during PATCH for id=$id with message: {$e->getMessage()}");
        }
    } //

        
    /**
     * put
     *
     * @param  int $id
     *
     * @return void
     */
    function put($id = null) {
        $this->modify($id, true);
    } // 
    

    /**
     * patch
     *
     * @param  mixed $id
     *
     * @return void
     */
    function patch($id = NULL) { 
        $this->modify($id);
    } //

        
    /**
     * delete
     *
     * @param  mixed $id
     *
     * @return void
     */
    function delete($id = NULL) {
        if($id == NULL)
            Factory::response()->sendError("Missing id", 400);

        $data = Factory::request()->getBody(false);        

        $this->id = $id;
        $this->folder = $this->folder = $data['folder'] ?? null;

        try {
            $this->instance = $this->getModelInstance();

            $this->instance
            ->assoc()
            ->fill([$this->instance->deletedBy()]); //

            $id_name = $this->instance->getIdName();
            $owned   = $this->instance->inSchema([$this->instance->belongsTo()]);

            $rows  = $this->instance->where([$id_name, $id]);
        
            // event hook
            $this->onDeletingBeforeCheck($id);

            $rows = $this->instance->get();
            //dd($this->instance->getLastPrecompiledQuery(), 'SQL');
            
            if (count($rows) == 0){
                Factory::response()->code(404)->sendError("Register for $id_name=$id doesn't exist");
            }

            if ($this->folder !== null)
            {
                if (empty(static::$folder_field))
                    Factory::response()->sendError("Undefined folder field", 403);

                // event hook    
                $this->onDeletingFolderBeforeCheck($id, $this->folder);

                $f = DB::table('folders')->assoc();
                $f_rows = $f->where([$id_name => $this->folder])->get();
                      
                if (count($f_rows) == 0 || $f_rows[0]['tb'] != $this->table_name)
                    Factory::response()->sendError('Folder not found', 404); 
        
                if ($f_rows[0][$this->instance->belongsTo()] != Acl::getCurrentUid()  && !FoldersAclExtension::hasFolderPermission($this->folder, 'w'))
                    Factory::response()->sendError("You have not permission for the folder $this->folder", 403);

                $this->folder_name = $f_rows[0]['name'];

                // Creo otra nueva instancia
                $instance2 = $this->getModelInstance();

                if (count($instance2->where([$id_name => $id, static::$folder_field => $this->folder_name])->get()) == 0)
                    Factory::response()->code(404)->sendError("Register for $id_name=$id doesn't exist");

                unset($data['folder']);    
                $data[static::$folder_field] = $f_rows[0]['name'];
                $data['belongs_to'] = $f_rows[0][$this->instance->belongsTo()];    
            } else {
                if ($owned && !$this->acl->hasSpecialPermission('write_all') && $rows[0]['belongs_to'] != Acl::getCurrentUid()){
                    Factory::response()->sendError('Forbidden', 403, 'You are not the owner');
                }
            }  

            $extra = [];

            if ($this->acl->hasSpecialPermission('lock')){
                if ($this->instance->inSchema([$this->instance->isLocked()])){
                    $extra = array_merge($extra, [$this->instance->isLocked() => 1]);
                }   
            }else {
                if (isset($rows[0][$this->instance->isLocked()]) && $rows[0][$this->instance->isLocked()] == 1){
                    Factory::response()->sendError("Locked by Admin", 403);
                }
            }

            $soft_is_supported   = $this->instance->inSchema([$this->instance->deletedAt()]);
            $soft_del_has_author = $this->instance->inSchema([$this->instance->deletedBy()]);
            
            if ($soft_is_supported && $soft_del_has_author){
                $extra = array_merge($extra, [$this->instance->deletedBy() => $this->impersonated_by != null ? $this->impersonated_by : Acl::getCurrentUid()]);
            }               
       
            if (!empty($this->folder)) {
                // event hook    
                $this->onDeletingFolderAfterCheck($id, $this->folder);
            }

            $this->instance->setSoftDelete($soft_is_supported && static::$soft_delete);
            
            // event hook
            $this->onDeletingAfterCheck($id);

            $affected = $this->instance->delete($extra);
            
            if($affected){
                
                // event hooks
                if ($this->folder !==  null){
                    $this->onDeletedFolder($id, $affected, $this->folder);
                }

                $this->onDeleted($id, $affected);
                $this->webhook('delete', [ ], $id);
                
                Factory::response()->sendJson("OK");
            }	
            else
                Factory::response()->sendError("Record not found", 404);

        } catch (\Exception $e) {
            Factory::response()->sendError("Error during DELETE for $id_name=$id with message: {$e->getMessage()}");
        }

    } // 


    // It does Not check if 'deleted_at' is in attr_types.
    public static function hasSoftDelete(){
        return static::$soft_delete;
    }


    /*
        API event hooks
    */    

    protected function onGettingBeforeCheck($id) { }
    protected function onGettingAfterCheck($id) { }
    protected function onGettingAfterCheck2($id) { }  ///

    protected function onGot($id, ?int $count){ }
    protected function onDeletingBeforeCheck($id){ }
    protected function onDeletingAfterCheck($id){ }    
    protected function onDeleted($id, ?int $affected){ }

    protected function onPostingBeforeCheck($id, Array &$data){ }
    protected function onPuttingBeforeCheck2($id, Array &$data){ }  ///
    protected function onPostingAfterCheck($id, Array &$data){ }
    protected function onPost($id, Array &$data){ }

    protected function onPuttingBeforeCheck($id, Array &$data){ }
    protected function onPuttingAfterCheck($id, Array &$data){ }
    protected function onPut($id, Array &$data, ?int $affected){ }


    /*
        WebHooks     
    */
    protected function webhook(string $op, $data, $id = null){        
        if (!in_array($op, ['show', 'list', 'create', 'update', 'delete'])){
            throw new \InvalidArgumentException("Invalid webhook operation for $op");
        }

        DB::getDefaultConnection();

        $webhooks = DB::table('webhooks')
        ->where(['op' => $op, 'entity' => $this->table_name])
        ->get();

        $body = [       
            'webhook_id' => null,     
            'event_type' => $op,
            'entity' => $this->table_name,
            'id' => $id,
            'data' => $data,
            'user_id' => Acl::getCurrentUid(),
            'at' => date("Y-m-d H:i:s", time())
        ];

        $old_data = null;

        foreach($webhooks as $hook){
            if (!empty($hook['conditions'])){
                parse_str($hook['conditions'], $conditions);
            }

            $body['webhook_id'] = $hook['id'];

            if ($op == 'update' || $op == 'delete' || ($op == 'show' && !empty(Factory::request()->getQuery('fields')))){
                
                if ($op == 'update' && !empty($hook['conditions'])){                    
                    $cond_fields = array_keys($conditions);
                    $cond_fields = array_unique($cond_fields);
                    $row_fields  = array_keys($body['data']);

                    if (count(array_diff($cond_fields,$row_fields)) == 0)
                    {
                        if (Strings::filter($body['data'], $conditions)){
                            
                            if ($old_data === null){
                                //dd('RETRIVE');
                                $old_data = DB::table($this->table_name)
                                ->assoc()->find($id)->showDeleted()->first();
                                $body['data'] = array_merge($old_data, $body['data']);
                            }
                            
                            //dd('--> callback');
                            Url::consume_api($hook['callback'], 'POST', $body);
                        }
                    }  
                    continue;
                }

                if ($old_data === null){
                    //dd('RETRIVE');
                    $old_data = DB::table($this->table_name)
                    ->assoc()->find($id)->showDeleted()->first();
                    $body['data'] = array_merge($old_data, $body['data']);
                }

                $body['data'] = array_merge($old_data, $body['data']);
            }

            if (empty($hook['conditions'])){
                //dd('--> callback');
                Url::consume_api($hook['callback'], 'POST', $body);
            } else {
                if ($op != 'list'){                   
                    if (Strings::filter($body['data'], $conditions)){
                        //dd('--> callback');
                        Url::consume_api($hook['callback'], 'POST', $body);
                    }
                }
            }
        }      
    }


    /*
        API event hooks for folder access
    */  

    protected function onGettingFolderBeforeCheck($id, $folder){ } 
    protected function onGettingFolderAfterCheck($id, $folder){ }
    protected function onGotFolder($id, ?int $count, $folder){ }

    protected function onDeletingFolderBeforeCheck($id, $folder){ }
    protected function onDeletingFolderAfterCheck($id, $folder){ }
    protected function onDeletedFolder($id, ?int $affected, $folder){ }

    protected function onPostingFolderBeforeCheck($id, Array &$data, $folder){ }
    protected function onPostingFolderAfterCheck($id, Array &$data, $folder){ }
    protected function onPostFolder($id, Array $data, $folder){ }

    protected function onPuttingFolderBeforeCheck($id, Array &$data, $folder){ }
    protected function onPuttingFolderAfterCheck($id, Array &$data, $folder){ }
    protected function onPutFolder($id, Array $data, ?int $affected, $folder){ }

    
}  