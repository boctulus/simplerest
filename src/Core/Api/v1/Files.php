<?php

namespace Boctulus\Simplerest\Core\Api\v1;

use Boctulus\Simplerest\Controllers\MyApiController; 
use Boctulus\Simplerest\Core\Libs\FileUploader;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Acl;

class Files extends MyApiController
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
        foreach ($files as list($filename_ori, $filename_as_stored)){           
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

            $uploaded[] = [ 
                            'filename' => $filename_ori,
                            'uuid' => $id,
                            'link' => base_url() . 'get' . DIRECTORY_SEPARATOR . $id
            ];

            $this->webhook('create', $data, $id);
        }
  
        Factory::response()->send([
            'uploaded' => $uploaded,
            'failures' => $failures
        ], 201);
        
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

        $data = Factory::request()->getBody();        

        try {    
            $instance = DB::table($this->table_name)
            ->setFetchMode('ASSOC')
            ->fill(['deleted_at']); 

            $owned = $instance->inSchema(['belongs_to']);
            $row   = $instance->where(['uuid', $id])->first();
            
            if (empty($row)){
                Factory::response()->code(404)->error("File with id=$id does not exist");
            }
            
            $acl = Factory::acl();

            if ($owned && !$acl->hasSpecialPermission('write_all') && $row['belongs_to'] != auth()->uid()){
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
        
} // end class