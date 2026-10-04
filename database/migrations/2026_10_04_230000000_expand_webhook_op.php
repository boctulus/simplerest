<?php

use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\DB;

class ExpandWebhookOp implements IMigration
{
    public function up()
    {
        DB::statement("ALTER TABLE `webhooks` MODIFY COLUMN `op` varchar(255) NOT NULL;");
    }

    public function down()
    {
        $longOperations = DB::select(
            "SELECT `op` FROM `webhooks` WHERE CHAR_LENGTH(`op`) > 10 LIMIT 1;"
        );

        if (!empty($longOperations)) {
            throw new \RuntimeException(
                'Cannot reduce webhooks.op to 10 characters while longer operation names exist.'
            );
        }

        DB::statement("ALTER TABLE `webhooks` MODIFY COLUMN `op` varchar(10) NOT NULL;");
    }
}
