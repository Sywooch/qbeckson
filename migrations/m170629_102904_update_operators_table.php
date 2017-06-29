<?php

use yii\db\Migration;

class m170629_102904_update_operators_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('operators', 'region', $this->integer() . ' AFTER `user_id`');
    }

    public function safeDown()
    {
        echo "m170629_102904_update_operators_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170629_102904_update_operators_table cannot be reverted.\n";

        return false;
    }
    */
}
