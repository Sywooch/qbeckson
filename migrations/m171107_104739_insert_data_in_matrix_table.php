<?php

use yii\db\Migration;

class m171107_104739_insert_data_in_matrix_table extends Migration
{
    public function safeUp()
    {
        $this->insert('municipal_task_matrix', ['name' => 'Предпрофессиональные', 'can_choose_pf' => 1, 'can_choose_ac' => 1, 'can_set_numbers_pf' => 0, 'can_set_numbers_ac' => 0]);
        $this->insert('municipal_task_matrix', ['name' => 'Муниципалитет', 'can_choose_pf' => 1, 'can_choose_ac' => 1, 'can_set_numbers_pf' => 0, 'can_set_numbers_ac' => 0]);
        $this->insert('municipal_task_matrix', ['name' => 'Родители', 'can_choose_pf' => 0, 'can_choose_ac' => 0, 'can_set_numbers_pf' => 0, 'can_set_numbers_ac' => 1]);
    }

    public function safeDown()
    {
        $this->truncateTable('municipal_task_matrix');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171107_104739_insert_data_in_matrix_table cannot be reverted.\n";

        return false;
    }
    */
}
