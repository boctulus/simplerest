<?php

namespace Boctulus\Simplerest\Core\Api;

use Boctulus\Simplerest\Core\Api\ApiController; 
use Boctulus\Simplerest\Core\Libs\FileUploader;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\DB;

class Files extends ApiController
{ 
    static protected $soft_delete = false;

    function __construct()
    {   
        parent::__construct();    
        
        $this->tenantid = Factory::request()->getTenantId();

        if ($this->tenantid !== null){
            $this->conn = DB::getConnection($this->tenantid);
        }
    }

    function post() {
        $data = $_POST;

        /*
            ENTIDAD VIRTUAL (opcional, extensión no disruptiva).

            Si el upload trae `entity` (p. ej. documents, bills) el archivo se sube SIEMPRE
            a la tabla física `files`, pero además se crea la fila de metadata de esa entity
            (que referencia files.id por UUID). Así un mismo endpoint sube y "ancla" el archivo
            a cualquier entity que implemente attachUploadedFile($uuid, $data).

            La tabla siempre es `files`; la entity es "lógica" (mismo espíritu que Folders).
            Cuando `entity` no viene, el comportamiento es idéntico al histórico.
        */
        $entity        = $data['entity'] ?? null;
        $entityHandler = null;

        if ($entity !== null && $entity !== '') {
            $cls = "Boctulus\\Simplerest\\Controllers\\Api\\" . ucfirst(strtolower($entity));

            if (!class_exists($cls)) {
                error("Unknown entity '$entity'", 400);
            }

            $entityHandler = new $cls();

            if (!method_exists($entityHandler, 'attachUploadedFile')) {
                error("Entity '$entity' does not support file attachment", 400);
            }

            // Autorización de creación sobre la entity (independiente del permiso de `files`)
            $entityTable = strtolower($entity);
            if (!Factory::acl()->hasSpecialPermission('write_all')
                && !Factory::acl()->hasResourcePermission('create', $entityTable)) {
                error('Forbidden', 403, "You don't have permission to create '$entity'");
            }
        }

        $uploader = (new FileUploader())
        ->setFileHandler(function($uid) {
            $prefix = ($uid ?? '0').'-';
            return uniqid($prefix, true);
         }, auth()->uid());


        $files    = $uploader->doUpload()->getFileNames();   
        $failures = $uploader->getErrors();     

        if (count($files) == 0){
            error('No files or file upload failed', 400);
        }        
        
        $instance = DB::table($this->table_name)->fill(['filename_as_stored']);

        $belongs_to = null;

        $uploaded = [];
        foreach ($files as ['ori_name' => $filename_ori, 'as_stored' => $filename_as_stored]){
            if (Factory::acl()->hasSpecialPermission('transfer')){ 
                if (isset($data['belongs_to']))
                    $belongs_to = $data['belongs_to'];    
            } else 
                $belongs_to = !auth()->isGuest() ? auth()->uid() : null;    

            $file_ext = pathinfo($filename_ori, PATHINFO_EXTENSION);

            $id = $instance
            ->create([
                        'filename' => $filename_ori,  
                        'file_ext' => $file_ext,
                        'filename_as_stored' => $filename_as_stored,
                        'belongs_to' => $belongs_to ?? NULL,
                        'guest_access' => $data['guest_access'] ?? 0
            ]);

            $item = [
                        'filename' => $filename_ori,
                        'uuid' => $id,
                        'link' => base_url() . '/get/' . $id
            ];

            // Si vino `entity`, crear la fila de metadata enlazada. Si falla, limpiar
            // el archivo recién subido (consistencia 1 file ↔ 1 record) y abortar.
            if ($entityHandler !== null) {
                try {
                    $item['entity'] = strtolower($entity);
                    $item['record'] = $entityHandler->attachUploadedFile($id, $data);
                } catch (\Throwable $e) {
                    $this->cleanupUploadedFile($id, $filename_as_stored);

                    $code = ($e instanceof \DomainException && $e->getCode() >= 400) ? $e->getCode() : 422;
                    error($e->getMessage(), $code);
                }
            }

            $uploaded[] = $item;

            $this->webhook('create', $data, $id);
        }
  
        Factory::response()->send([
            'uploaded' => $uploaded,
            'failures' => $failures
        ], 201);
        
    }

    /*
        Borra un archivo recién subido (fila en `files` + archivo físico). Best-effort:
        se usa para revertir un upload cuando la creación de la entity virtual falla.
    */
    protected function cleanupUploadedFile($id, $filename_as_stored)
    {
        try {
            DB::table($this->table_name)->where([DB::table($this->table_name)->getIdName(), $id])->delete(false);

            $path = UPLOADS_PATH . $filename_as_stored;
            if (is_file($path)) {
                @unlink($path);
            }
        } catch (\Throwable $e) {
            \Boctulus\Simplerest\Core\Libs\Logger::log("cleanupUploadedFile failed for id=$id: " . $e->getMessage());
        }
    }

    function put ($id = null){
        error('Not implemented', 501);
    }

    /**
     * delete
     *
     * @param  mixed $id
     *
     * @return void
     */
    function delete($id = NULL) {
        if($id == NULL)
            error("Lacks id in request", 400); 

        try {    
            $instance = DB::table($this->table_name)
            ->setFetchMode('ASSOC')
            ->fill(['deleted_at']); 

            $owned = $instance->inSchema(['belongs_to']);
            $row   = $instance->where([$instance->getIdName(), $id])->first();
            
            if (empty($row)){
                Factory::response()->code(404)->error("File with id=$id does not exist");
            }
            
            $acl = Factory::acl();

            if (!$owned && !$acl->hasSpecialPermission('write_all') && $row['belongs_to'] != auth()->uid()){
                error('Forbidden', 403, 'You are not the owner');
            }
            
            $extra = [];

            if (!$acl->hasSpecialPermission('lock')){
                if ($instance->inSchema(['is_locked'])){
                    $extra = array_merge($extra, ['is_locked' => 1]);
                }   
            }else {
                if (isset($row['is_locked']) && $row['is_locked'] == 1){
                    error("Locked by an admin", 403);
                }
            }

            if ($instance->inSchema(['deleted_by'])){
                $extra = array_merge($extra, ['deleted_by' => auth()->uid()]);                
            } 
            
            $soft_delete = static::$soft_delete && $instance->inSchema(['deleted_at']);

            if (!$soft_delete) {
                $path = UPLOADS_PATH . $row['filename_as_stored'];

                if (!file_exists($path)){
                    $instance->update(['broken' => 1]);
                    error("File not found",404, $path); 
                }

                $ok = unlink($path);

                if (!$ok){
                    error("File permission error", 500);
                }
            }

            if($instance->setSoftDelete(true)->delete($soft_delete, $extra)){
                $this->webhook('delete', [ ], $id);                
                Factory::response()->sendJson("OK");
            }	
            //else
            //    error("File not found",404);

        } catch (\Exception $e) {
            error("Error during DELETE for id=$id with message: {$e->getMessage()}");
        }

    } // 

    function get($id = NULL){
        try {    
            $instance = DB::table($this->table_name);

            $acl   = Factory::acl();
            $owned = $instance->inSchema(['belongs_to']);

            if ($id !== NULL) {
                $row = $instance->where([$instance->getIdName(), $id])->first();

                if (!$owned && !$acl->hasSpecialPermission('read_all') && $row['belongs_to'] != auth()->uid()){
                    error('Forbidden', 403, 'You are not the owner');
                }

                return Factory::response()->sendJson($row);
            }

            if (!$owned && !$acl->hasSpecialPermission('read_all')){
                error('Forbidden', 403, 'You are not the owner');
            }

            $rows = $instance->get();

            if (!empty($rows)) {                
                return Factory::response()->sendJson($rows);       
            }                

            error("File not found",404);

        } catch (\Exception $e) {
            error("Error during DELETE for id=$id with message: {$e->getMessage()}");
        }

    
    }
        
} // end class