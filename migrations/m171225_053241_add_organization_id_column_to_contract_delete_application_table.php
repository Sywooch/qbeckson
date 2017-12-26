<?php

use yii\db\Migration;

/**
 * Handles adding organization_id to table `contract_delete_application`.
 */
class m171225_053241_add_organization_id_column_to_contract_delete_application_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('contract_delete_application', 'organization_id', $this->integer()->null());

        // creates index for column `organization_id`
        $this->createIndex(
            'idx-contract_delete_application-organization_id',
            'contract_delete_application',
            'organization_id'
        );

        // add foreign key for table `contracts`
        $this->addForeignKey(
            'fk-contract_delete_application-organization_id',
            'contract_delete_application',
            'organization_id',
            'organization',
            'id',
            'SET NULL'
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-contract_delete_application-organization_id', 'contract_delete_application');
        $this->dropIndex('idx-contract_delete_application-organization_id', 'contract_delete_application');
        $this->dropColumn('contract_delete_application', 'organization_id');
    }
}
