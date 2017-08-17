<?php

use yii\db\Migration;

class m170726_125808_update_operators_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('operators', 'date_year_start', $this->date());
        $this->addColumn('operators', 'date_year_end', $this->date());
    }

    public function safeDown()
    {
        $this->dropColumn('operators', 'date_year_start');
        $this->dropColumn('operators', 'date_year_end');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170726_125808_update_operators_table cannot be reverted.\n";

        return false;
    }
    */
}
