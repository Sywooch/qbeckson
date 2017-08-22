<?php

use yii\db\Migration;

class m170820_163558_update_organization_table extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('organization', 'contracts_count');
        $this->addColumn('organization', 'contracts_count', 'INTEGER(5) DEFAULT 0');
    }

    public function safeDown()
    {
        return true;
    }
}
