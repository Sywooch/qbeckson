<?php

use yii\db\Migration;

class m171227_093945_change_column_type extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('contract_delete_application', 'certificate_number', $this->string(45)->null());
    }

    public function safeDown()
    {
        $this->alterColumn('contract_delete_application', 'certificate_number', $this->integer(11)->null());
    }
}
