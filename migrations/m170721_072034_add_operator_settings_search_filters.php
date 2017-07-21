<?php

use app\models\UserIdentity;
use yii\db\Migration;

/**
 * Class m170721_072034_add_operator_settings_search_filters
 */
class m170721_072034_add_operator_settings_search_filters extends Migration
{
    /**
     *
     */
    public function safeUp()
    {
        $this->addColumn('settings_search_filters', 'type', 'varchar(50) DEFAULT NULL');
        $this->dropIndex('table_name', 'settings_search_filters');
        $this->insert('settings_search_filters', [
            'table_name' => 'payers',
            'table_columns' => 'name,mun,phone,email,fio,directionality,certificates,cooperates',
            'inaccessible_columns' => 'name,mun',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_OPERATOR
        ]);

        $this->insert('settings_search_filters', [
            'table_name' => 'organization',
            'table_columns' => 'name,mun,cratedate,site,phone,type,programs,max_child,children,amount_child,raiting,actual',
            'inaccessible_columns' => 'name,mun',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_OPERATOR,
            'type' => 'register'
        ]);

        $this->insert('settings_search_filters', [
            'table_name' => 'organization',
            'table_columns' => 'name,mun,fio_contact,site,email,typeLabel',
            'inaccessible_columns' => 'name,mun',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_OPERATOR,
            'type' => 'request'
        ]);
    }

    /**
     * @return bool
     */
    public function safeDown()
    {
        $this->createIndex('table_name', 'settings_search_filters', 'table_name');
        $this->dropColumn('settings_search_filters', 'type');
        $this->execute('DELETE FROM settings_search_filters WHERE role = "' . UserIdentity::ROLE_OPERATOR . '"');
        return true;
    }
}
