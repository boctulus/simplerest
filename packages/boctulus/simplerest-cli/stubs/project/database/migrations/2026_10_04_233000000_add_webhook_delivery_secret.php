<?php

use Boctulus\Simplerest\Core\Interfaces\IMigration;
use Boctulus\Simplerest\Core\Libs\DB;

class AddWebhookDeliverySecret implements IMigration
{
    public function up()
    {
        $secretColumn = DB::select("SHOW COLUMNS FROM `webhooks` LIKE 'secret';");
        if (empty($secretColumn)) {
            DB::statement("ALTER TABLE `webhooks` ADD COLUMN `secret` char(64) NULL AFTER `callback`;");
        }

        $subscriptions = DB::select(
            "SELECT `id` FROM `webhooks` WHERE `secret` IS NULL OR `secret` = '';"
        );
        foreach ($subscriptions as $subscription) {
            $secret = bin2hex(random_bytes(32));
            DB::statement(
                "UPDATE `webhooks` SET `secret` = ? WHERE `id` = ?;",
                [$secret, $subscription['id']]
            );
        }

        DB::statement("ALTER TABLE `webhooks` MODIFY COLUMN `secret` char(64) NOT NULL;");
    }

    public function down()
    {
        DB::statement("ALTER TABLE `webhooks` DROP COLUMN `secret`;");
    }
}
