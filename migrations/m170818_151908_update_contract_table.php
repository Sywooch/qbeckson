<?php

use yii\db\Migration;

class m170818_151908_update_contract_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('contracts', 'balance', 'DECIMAL(19,2) DEFAULT NULL');
        $this->addColumn('contracts', 'period', 'INTEGER(2) DEFAULT 1');
    }

    public function safeDown()
    {
        $this->dropColumn('contracts', 'balance');
        $this->dropColumn('contracts', 'period');
    }
}
