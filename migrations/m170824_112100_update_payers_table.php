<?php

use yii\db\Migration;

class m170824_112100_update_payers_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('payers', 'certificate_can_use_future_balance', $this->boolean()->defaultValue(1));
    }

    public function safeDown()
    {
        $this->dropColumn('payers', 'certificate_can_use_future_balance');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170824_112100_update_payers_table cannot be reverted.\n";

        return false;
    }
    */
}
