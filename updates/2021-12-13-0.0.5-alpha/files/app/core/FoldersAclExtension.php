<?php

namespace simplerest\core;

use simplerest\core\Model;
use simplerest\libs\DB;
use simplerest\libs\Factory;
use simplerest\libs\Debug;

// mover a ServiceProvider

/*
    Debe encapsular todas las referencias a las tablas:

    folders
    folder_permissions
    folder_other_permissions

*/
class FoldersAclExtension 
{   
    public function __construct() { }

    /**
     * hasFolderPermission
     *
     * @param  int    $folder
     * @param  string $operation
     *
     * @return bool
     */
    static public function hasFolderPermission(int $folder, string $operation)
    {
        if ($operation != 'r' && $operation != 'w')
            throw new \InvalidArgumentException("Invalid operation '$operation'. It should be 'r' or 'w'.");

        $o = DB::table('folder_other_permissions')->assoc();

        $rows = $o->where(['folder_id', $folder])->get();

        $r = $rows[0]['r'] ?? null;
        $w = $rows[0]['w'] ?? null;

        $guest = Factory::acl()->isGuest();

        if ($guest){
            $guest_role = $guest;
            $r = $r && $rows[0][$guest_role];
            $w = $w && $rows[0][$guest_role];
        }

        if (($operation == 'r' && $r) || ($operation == 'w' && $w)) {
            return true;
        }
        
        $g = DB::table('folder_permissions')->assoc();
        $rows = $g->where([
                                    ['folder_id', $folder], 
                                    ['access_to', Factory::auth()->uid]
        ])->get();

        $r = $rows[0]['r'] ?? null;
        $w = $rows[0]['w'] ?? null;

        if (($operation == 'r' && $r) || ($operation == 'w' && $w)) {
            return true;
        }

        return false;
    } 

}

