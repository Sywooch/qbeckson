<?php

use yii\db\Migration;

class m170814_112744_update_program_module_address_table extends Migration
{
    public function safeUp()
    {
        $this->dropForeignKey(
            'fk-program_address-address_id-organization_address-id',
            'program_address_assignment'
        );

        $this->addForeignKey(
            'fk-program_address-address_id-organization_address-id',
            'program_address_assignment',
            'organization_address_id',
            'organization_address',
            'id',
            'cascade',
            'cascade'
        );

        $this->dropForeignKey(
            'fk-module_address-program_address_id-program_address-id',
            'program_module_address_assignment'
        );

        $this->addForeignKey(
            'fk-module_address-program_address_id-program_address-id',
            'program_module_address_assignment',
            'program_address_assignment_id',
            'program_address_assignment',
            'id',
            'cascade',
            'cascade'
        );
    }

    public function safeDown()
    {
        return true;
    }
}
