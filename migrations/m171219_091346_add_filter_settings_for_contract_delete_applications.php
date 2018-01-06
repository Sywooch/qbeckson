<?php

use yii\db\Migration;

class m171219_091346_add_filter_settings_for_contract_delete_applications extends Migration
{
    public function safeUp()
    {
        $this->insert('settings_search_filters', [
            'table_name' => 'contract_delete_application',
            'table_columns' => 'contractNumber,contractDate,fileUrl,certificateNumber,created_at,reason',
            'inaccessible_columns' => 'contractNumber,contractDate,certificateNumber,created_at',
            'is_active' => 1,
            'role' => 'organizations',
            'type' => 'waiting',
        ]);
        $this->insert('settings_search_filters', [
            'table_name' => 'contract_delete_application',
            'table_columns' => 'contractNumber,contractDate,fileUrl,certificateNumber,created_at,confirmed_at,reason',
            'inaccessible_columns' => 'contractNumber,contractDate,certificateNumber,created_at',
            'is_active' => 1,
            'role' => 'organizations',
            'type' => 'confirmed',
        ]);
        $this->insert('settings_search_filters', [
            'table_name' => 'contract_delete_application',
            'table_columns' => 'contractNumber,contractDate,fileUrl,certificateNumber,created_at,confirmed_at,reason',
            'inaccessible_columns' => 'contractNumber,contractDate,certificateNumber,created_at',
            'is_active' => 1,
            'role' => 'organizations',
            'type' => 'refused',
        ]);
    }

    public function safeDown()
    {
        $this->delete('settings_search_filters', [
            'table_name' => 'contract_delete_application',
            'is_active' => 1,
            'role' => 'organizations',
            'type' => 'waiting',
        ]);
        $this->delete('settings_search_filters', [
            'table_name' => 'contract_delete_application',
            'is_active' => 1,
            'role' => 'organizations',
            'type' => 'confirmed',
        ]);
        $this->delete('settings_search_filters', [
            'table_name' => 'contract_delete_application',
            'is_active' => 1,
            'role' => 'organizations',
            'type' => 'refused',
        ]);
    }
}
