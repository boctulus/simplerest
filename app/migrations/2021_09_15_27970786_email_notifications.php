<?php

use simplerest\core\interfaces\IMigration;
use simplerest\core\libs\Factory;
use simplerest\core\libs\Schema;
use simplerest\core\Model;
use simplerest\core\libs\DB;

class EmailNotifications implements IMigration
{
    protected $table = 'email_notifications';

    /**
	* Run migration.
    *
    * @return void
    */
    public function up()
    {
        $sc = new Schema($this->table);
        $sc->id()->auto(); 
        $sc->varchar('from_addr', 320)->nullable();
        $sc->varchar('from_name', 80)->nullable();
        $sc->varchar('to_addr', 320);
        $sc->varchar('to_name', 80)->nullable();
        $sc->varchar('cc_addr', 320)->nullable();
        $sc->varchar('cc_name', 80)->nullable();
        $sc->varchar('bcc_addr', 320)->nullable();
        $sc->varchar('bcc_name', 80)->nullable();
        $sc->varchar('replyto_addr', 320)->nullable();
        $sc->varchar('subject', 80);
        $sc->text('body')->nullable();
        $sc->datetime('sent_at')->nullable();
        $sc->datetime('created_at');
        $sc->datetime('deleted_at')->nullable();
        $sc->create();
    }

    /**
	* Run undo migration.
    *
    * @return void
    */
    public function down()
    {
        ### DOWN

        Schema::dropIfExists($this->table);
    }
}

