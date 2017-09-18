<?php

use yii\db\Migration;

class m170918_082818_indexes extends Migration
{
    public function safeUp()
    {
        $this->createIndex('idx_programs_verificateion', 'programs', ['verification']);
        $this->createIndex('idx_organization_status', 'organization', ['status']);

        $this->createIndex('idx_contracts_status', 'contracts', ['status']);
        $this->createIndex('idx_organization_document_type', 'organization_document', ['type']);
    }

    public function safeDown()
    {
        $this->dropIndex('idx_programs_verificateion', 'programs');
        $this->dropIndex('idx_organization_status', 'organization');
        $this->dropIndex('idx_contracts_status', 'contracts');
        $this->dropIndex('idx_organization_document_type', 'organization_document');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170918_082818_programms_indexes cannot be reverted.\n";

        return false;
    }
    */
}
