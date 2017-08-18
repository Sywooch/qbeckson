<?php

use yii\db\Migration;

class m170818_085556_update_organization_settings_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('organization_contract_settings', 'header_text', 'TEXT DEFAULT NULL');
    }

    public function safeDown()
    {
        $this->dropColumn('organization_contract_settings', 'header_text');
    }
}
