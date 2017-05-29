<?php

use yii\db\Migration;

class m170529_104403_update_cert_group_table extends Migration
{
    public function up()
    {
        $this->addColumn('cert_group', 'is_special', $this->boolean());
        $this->update('cert_group', ['is_special' => 1, 'group' => 'Сертификат учета'], ['like', 'group', '9 Группа']);
    }

    public function down()
    {
        $this->dropColumn('cert_group', 'is_special');
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
