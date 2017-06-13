<?php

use yii\db\Migration;

class m170613_073234_update_cert_group_table extends Migration
{
    public function up()
    {
        $this->update('cert_group', ['is_special' => 1, 'group' => 'Сертификат учета'], ['like', 'group', '9 группа']);
    }

    public function down()
    {
        echo "m170613_073234_update_cert_group_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
