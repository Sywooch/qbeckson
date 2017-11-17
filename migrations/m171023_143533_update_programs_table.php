<?php

use yii\db\Migration;

class m171023_143533_update_programs_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('programs', 'municipal_task_section', $this->integer()->defaultValue(10));
    }

    public function safeDown()
    {
        $this->dropColumn('programs', 'municipal_task_section');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171023_143533_update_programs_table cannot be reverted.\n";

        return false;
    }
    */
}
