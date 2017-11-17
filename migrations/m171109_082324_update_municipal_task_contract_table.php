<?php

use yii\db\Migration;

class m171109_082324_update_municipal_task_contract_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('municipal_task_contract', 'pdf', $this->string() . ' AFTER `group_id`');
        $this->addColumn('municipal_task_contract', 'number', $this->string() . ' AFTER `group_id`');
    }

    public function safeDown()
    {
        $this->dropColumn('municipal_task_contract', 'pdf');
        $this->dropColumn('municipal_task_contract', 'number');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171109_082324_update_municipal_task_contract_table cannot be reverted.\n";

        return false;
    }
    */
}
