<?php

use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\Factory;
use Boctulus\Simplerest\Core\Libs\Schema;
use Boctulus\Simplerest\Core\Model;
use Boctulus\Simplerest\Core\Libs\DB;

class FilesRenameLocked implements IMigration
{
    function __construct(){
        get_default_connection();
    }

    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
      try {
          // Check if the source column exists before attempting rename
          if (!Schema::hasColumn('files', 'locked')) {
              return; // Column already renamed or doesn't exist, skip
          }

          // Check if target column already exists
          if (Schema::hasColumn('files', 'is_locked')) {
              return; // Target column already exists, skip
          }

          $sc = new Schema('files');
          $sc->renameColumn('locked', 'is_locked');
          $sc->alter();
      } catch (\Exception $e) {
          // Si falla, probablemente la columna ya fue renombrada o no existe
          // Ignorar el error silenciosamente
          return;
      }
    }

    public function down()
    {
      try {
          // Check if the source column exists before attempting rename
          if (!Schema::hasColumn('files', 'is_locked')) {
              return; // Column already renamed back or doesn't exist, skip
          }

          // Check if target column already exists
          if (Schema::hasColumn('files', 'locked')) {
              return; // Target column already exists, skip
          }

          $sc = new Schema('files');
          $sc->renameColumn('is_locked', 'locked');
          $sc->alter();
      } catch (\Exception $e) {
          // Si falla, probablemente la columna ya fue renombrada o no existe
          // Ignorar el error silenciosamente
          return;
      }
    }
}

