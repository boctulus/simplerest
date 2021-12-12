<?php

namespace simplerest\core\api\v1;

use simplerest\core\Acl;
use simplerest\core\api\v1\ResourceController;
use simplerest\libs\Factory;
use simplerest\libs\DB;


class Download extends ResourceController
{
    // caso puntual donde lo conservo:
    static protected $guest_access = true;

    public $table_name = 'files';

    function __construct()
    {
        global $api_version;
        $api_version = 'v1';

        parent::__construct();   
        
        $this->tenantid = Factory::request()->getTenantId();

        if ($this->tenantid !== null){
            $this->conn = DB::getConnection($this->tenantid);
        }
    }

    function get($id = null) {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['GET','OPTIONS']))
            Factory::response()->sendError('Incorrect verb ('.$_SERVER['REQUEST_METHOD'].'), expecting GET',405);

        if ($id == null)
            return;

        $_get = [];    
        
        if (!Factory::acl()->hasSpecialPermission('read_all')){
            if ($this->acl->isGuest()){                
                $instance = DB::table($this->table_name);
                
                if ($instance->inSchema(['guest_access'])){
                    $_get[] = ['guest_access', 1];
                }
                                         
            } else {
                $_get[] = ['belongs_to', Acl::getCurrentUid()];
            }
        }

        $_get[] =   ['uuid', $id];   

        $row = DB::table($this->table_name)->select(['filename_as_stored'])->where($_get)->first();

        if (empty($row))
            Factory::response()->sendError('File not found', 404);
      
        $file = UPLOADS_PATH . $row['filename_as_stored'];

        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        } else {
            Factory::response()->sendError('File not found', 404, "$file not found in storage");
        }
    }

    function index($id){
        return $this->get($id);
    }
}