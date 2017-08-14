<?php

use yii\db\Migration;

class m170808_122659_update_cooperate_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('cooperate', 'document_type', 'INTEGER(3) DEFAULT NULL');
        $this->addColumn('cooperate', 'document_path', 'VARCHAR(255) DEFAULT NULL');
        $this->addColumn('cooperate', 'document_base_url', 'VARCHAR(255) DEFAULT NULL');
    }

    public function safeDown()
    {
        $this->dropColumn('cooperate', 'document_type');
        $this->dropColumn('cooperate', 'document_path');
        $this->dropColumn('cooperate', 'document_base_url');
    }
}
