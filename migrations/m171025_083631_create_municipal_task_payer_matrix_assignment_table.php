<?php

use yii\db\Migration;

/**
 * Handles the creation of table `municipal_task_payer_matrix_assignment`.
 */
class m171025_083631_create_municipal_task_payer_matrix_assignment_table extends Migration
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

        $this->createTable('municipal_task_payer_matrix_assignment', [
            'payer_id' => $this->integer(11)->notNull(),
            'matrix_id' => $this->integer(11)->notNull(),
            'certificate_type' => "ENUM('1','2')",
            'can_be_chosen' => $this->boolean()->defaultValue(0),
            'number' => $this->integer(),
            'number_type' => $this->smallInteger()->defaultValue(10),
        ], $tableOptions);

        $this->addPrimaryKey('pk-payer-matrix', 'municipal_task_payer_matrix_assignment', ['payer_id', 'matrix_id', 'certificate_type']);
        $this->addForeignKey('fk-payer-matrix-1', 'municipal_task_payer_matrix_assignment', 'payer_id', 'payers', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fk-payer-matrix-2', 'municipal_task_payer_matrix_assignment', 'matrix_id', 'municipal_task_matrix', 'id', 'cascade', 'cascade');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('municipal_task_payer_matrix_assignment');
    }
}
