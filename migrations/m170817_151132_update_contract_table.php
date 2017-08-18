<?php

use yii\db\Migration;

class m170817_151132_update_contract_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('contracts', 'payment_order', 'INTEGER(2) DEFAULT NULL');
    }

    public function safeDown()
    {
        $this->dropColumn('contracts', 'payment_order');
    }
}
