<?php

use yii\db\Migration;

class m170628_125619_insert_data_into_settings_search_filters_table extends Migration
{
    public function safeUp()
    {
        $this->insert('{{%settings_search_filters}}', [
            'table_name' => 'certificates',
            'table_columns' => 'number, fio_child, nominal, rezerv, balance, contractCount, cert_group, actual',
            'inaccessible_columns' => 'number, fio_child',
            'is_active' => 1,
        ]);
    }

    public function safeDown()
    {
        echo "m170628_125619_insert_data_into_settings_search_filters_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170628_125619_insert_data_into_settings_search_filters_table cannot be reverted.\n";

        return false;
    }
    */
}
