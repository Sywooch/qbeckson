<?php

use yii\db\Migration;

class m170816_165359_update_contract_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('contracts', 'parents_first_month_payment', 'DECIMAL(19,2) DEFAULT 0');
        $this->addColumn('contracts', 'parents_other_month_payment', 'DECIMAL(19,2) DEFAULT 0');
        $this->addColumn('contracts', 'payer_first_month_payment', 'DECIMAL(19,2) DEFAULT 0');
        $this->addColumn('contracts', 'payer_other_month_payment', 'DECIMAL(19,2) DEFAULT 0');
    }

    public function safeDown()
    {
        $this->dropColumn('contracts', 'parents_first_month_payment');
        $this->dropColumn('contracts', 'parents_other_month_payment');
        $this->dropColumn('contracts', 'payer_first_month_payment');
        $this->dropColumn('contracts', 'payer_other_month_payment');
    }
}
