<?php

use yii\db\Migration;

class m171222_075201_add_columns_to_contract_delete_application_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('contract_delete_application', 'certificate_number',
            $this->integer(11)->null());
        $this->addColumn('contract_delete_application', 'contract_number',
            $this->string(11)->null()->defaultValue(null));
        $this->addColumn('contract_delete_application', 'contract_date',
            $this->date()->null()->defaultValue(null));
    }

    public function safeDown()
    {
        $this->dropColumn('contract_delete_application', 'contract_date');
        $this->dropColumn('contract_delete_application', 'contract_number');
        $this->dropColumn('contract_delete_application', 'certificate_number');
    }
}
