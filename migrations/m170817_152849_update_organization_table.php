<?php

use yii\db\Migration;

class m170817_152849_update_organization_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('organization', 'contracts_count', 'integer(11) DEFAULT NULL');
    }

    public function safeDown()
    {
        $this->dropColumn('organization', 'contracts_count');
    }
}
