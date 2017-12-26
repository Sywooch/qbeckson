<?php

use yii\db\Migration;

class m171222_073154_change_fk_in_contract_delete_application_table extends Migration
{
    public function safeUp()
    {
        $this->dropForeignKey('fk-contract_delete_application-contract_id', 'contract_delete_application');

        // add foreign key for table `contracts`
        $this->addForeignKey(
            'fk-contract_delete_application-contract_id',
            'contract_delete_application',
            'contract_id',
            'contracts',
            'id',
            'SET NULL'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-contract_delete_application-contract_id', 'contract_delete_application');

        // add foreign key for table `contracts`
        $this->addForeignKey(
            'fk-contract_delete_application-contract_id',
            'contract_delete_application',
            'contract_id',
            'contracts',
            'id',
            'CASCADE'
        );
    }
}
