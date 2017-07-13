<?php

use app\models\UserIdentity;
use yii\db\Migration;

class m170713_184938_update_settings_search_filters extends Migration
{
    public function safeUp()
    {
        $this->addColumn(
            'settings_search_filters',
            'role',
            'string(20) NOT NULL DEFAULT "' . UserIdentity::ROLE_PAYER . '"'
        );

        $this->addForeignKey(
            'fk-settings_search_filters-role-auth_item-name',
            'settings_search_filters',
            'role',
            'auth_item',
            'name'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-settings_search_filters-role-auth_item-name', 'settings_search_filters');
        $this->dropColumn('settings_search_filters', 'role');
    }
}
