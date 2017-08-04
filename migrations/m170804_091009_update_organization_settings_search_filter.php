<?php

use app\models\UserIdentity;
use yii\db\Migration;

/**
 * Class m170804_091009_update_otganization_search_filter
 */
class m170804_091009_update_organization_settings_search_filter extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('settings_search_filters', [
            'table_name' => 'payers',
            'table_columns' => 'name,mun,phone,email,fio,directionality,certificates,cooperates',
            'inaccessible_columns' => 'name,mun',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_ORGANIZATION,
            'type' => 'all',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->execute('
            DELETE FROM settings_search_filters 
                WHERE role = "' . UserIdentity::ROLE_ORGANIZATION . '" AND type = "all"
        ');

        return true;
    }
}
