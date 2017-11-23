<?php

use yii\db\Migration;

class m171025_092722_update_programs_table extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('programs', 'municipal_task_section');
        $this->addColumn('programs', 'municipal_task_matrix_id', $this->integer());
        $this->addForeignKey('fk-programs-matrix-1', 'programs', 'municipal_task_matrix_id', 'municipal_task_matrix', 'id', 'cascade', 'cascade');
    }

    public function safeDown()
    {
        $this->dropColumn('programs', 'municipal_task_matrix_id');
        $this->addColumn('programs', 'municipal_task_section', $this->integer()->defaultValue(10));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171025_092722_update_programs_table cannot be reverted.\n";

        return false;
    }
    */
}
