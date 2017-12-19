<?php

use yii\db\Migration;

class m171219_073318_alter_user_created_on_certificate_import_log_table extends Migration
{
    public function safeUp()
    {
        $this->dropForeignKey('fk_user_created_user', 'user_created_on_certificate_import_log');
    }

    public function safeDown()
    {
        $this->addForeignKey('fk_user_created_user', 'user_created_on_certificate_import_log', 'user_id', 'user', 'id');
    }
}
