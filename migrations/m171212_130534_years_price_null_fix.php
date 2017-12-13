<?php

use yii\db\Migration;

class m171212_130534_years_price_null_fix extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('{{%years}}', 'price', $this->float()->null());
        $this->alterColumn('{{%years}}', 'rating', $this->integer()->null());
        $this->alterColumn('{{%years}}', 'limits', $this->integer()->null());
        $this->alterColumn('{{%years}}', 'open', $this->integer()->defaultValue(0));
        $this->alterColumn('{{%years}}', 'quality_control', $this->integer()->defaultValue(null));
    }

    public function safeDown()
    {
        //echo "m171212_130534_years_price_null_fix cannot be reverted.\n";

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171212_130534_years_price_null_fix cannot be reverted.\n";

        return false;
    }
    */
}
