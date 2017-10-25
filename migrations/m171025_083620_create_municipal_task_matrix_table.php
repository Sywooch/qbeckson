<?php

use yii\db\Migration;

/**
 * Handles the creation of table `municipal_task_matrix`.
 */
class m171025_083620_create_municipal_task_matrix_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('municipal_task_matrix', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'can_choose_pf' => $this->boolean()->defaultValue(0),
            'can_choose_ac' => $this->boolean()->defaultValue(0),
            'can_set_numbers_pf' => $this->boolean()->defaultValue(0),
            'can_set_numbers_ac' => $this->boolean()->defaultValue(0),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('municipal_task_matrix');
    }
}
