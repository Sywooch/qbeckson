<?php

use app\models\UserIdentity;
use yii\db\Migration;

class m170807_092037_update_search_filter extends Migration
{
    public function safeUp()
    {
        $this->execute('
            DELETE FROM settings_search_filters 
                WHERE role = "' . UserIdentity::ROLE_ORGANIZATION . '" AND table_name = "payers"
        ');

        $this->insert('settings_search_filters', [
            'table_name' => 'payers',
            'table_columns' => 'name,mun,phone,email,fio,directionality,certificates,cooperates',
            'inaccessible_columns' => 'name,mun',
            'is_active' => 1,
            'role' => UserIdentity::ROLE_ORGANIZATION,
        ]);
    }

    public function safeDown()
    {
        $this->execute('
            DELETE FROM settings_search_filters 
                WHERE role = "' . UserIdentity::ROLE_ORGANIZATION . '" AND table_name = "payers"
        ');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170807_092037_update_search_filter cannot be reverted.\n";

        return false;
    }
    */
}
