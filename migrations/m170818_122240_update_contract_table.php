<?php

use yii\db\Migration;

class m170818_122240_update_contract_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('contracts', 'url', 'VARCHAR(255) DEFAULT NULL');
    }

    public function safeDown()
    {
        $this->dropColumn('contracts', 'url');
    }
}
