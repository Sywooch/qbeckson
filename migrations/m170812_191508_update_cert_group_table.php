<?php

use yii\db\Migration;

class m170812_191508_update_cert_group_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('cert_group', 'nominal_f', 'DECIMAL(19,2) NOT NULL DEFAULT 0');
    }

    public function safeDown()
    {
        $this->dropColumn('cert_group', 'nominal_f');
    }
}
