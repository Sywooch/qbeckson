<?php

use app\models\UserIdentity;
use yii\db\Migration;

class m170821_183558_add_certificate_search_filters extends Migration
{
    public function safeUp()
    {
        $this->insert('settings_search_filters', [
            'table_name' => 'programs',
            'table_columns' => 'zab,year,hours,directivity,age_group_min,rating,mun,price,normativePrice',
            'inaccessible_columns' => 'name',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_CERTIFICATE,
        ]);

        $this->insert('settings_search_filters', [
            'table_name' => 'organization',
            'table_columns' => 'programs,children,max_child,raiting,actual',
            'inaccessible_columns' => 'name,type',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_CERTIFICATE,
        ]);
    }

    /**
     * @return bool
     */
    public function safeDown()
    {
        $this->execute('
            DELETE FROM settings_search_filters 
                WHERE role = "' . UserIdentity::ROLE_CERTIFICATE . '"
        ');
    }
}
