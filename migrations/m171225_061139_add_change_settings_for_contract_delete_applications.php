<?php

use yii\db\Migration;

class m171225_061139_add_change_settings_for_contract_delete_applications extends Migration
{
    public function safeUp()
    {
        $this->update('settings_search_filters',
            [
                'table_columns' => 'contract_number,contract_date,fileUrl,certificate_number,created_at,confirmed_at,reason',
                'inaccessible_columns' => 'contract_number,contract_date,certificate_number,created_at',
                'is_active' => 1,
                'role' => 'organizations',
                'type' => 'confirmed',
            ],
            [
                'table_name' => 'contract_delete_application',
                'is_active' => 1,
                'role' => 'organizations',
                'type' => 'confirmed',
            ]);

    }

    public function safeDown()
    {
        $this->update('settings_search_filters',
            [
                'table_columns' => 'contractNumber,contractDate,fileUrl,certificateNumber,created_at,confirmed_at,reason',
                'inaccessible_columns' => 'contractNumber,contractDate,certificateNumber,created_at',
                'is_active' => 1,
                'role' => 'organizations',
                'type' => 'confirmed',
            ],
            [
                'table_name' => 'contract_delete_application',
                'is_active' => 1,
                'role' => 'organizations',
                'type' => 'confirmed',
            ]);
    }
}
