<?php

use yii\db\Migration;

/**
 * Handles the creation of table `help_user_assignment`.
 */
class m170829_094858_create_help_user_assignment_table extends Migration
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
        $this->createTable('help_user_assignment', [
            'help_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'status' => $this->smallInteger()->defaultValue(10),
        ], $tableOptions);

        $this->addPrimaryKey('pk-user-help', 'help_user_assignment', ['help_id', 'user_id']);
        $this->addForeignKey('fk-help-user-assignment-help', 'help_user_assignment', 'help_id', 'help', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fk-help-user-assignment-user', 'help_user_assignment', 'user_id', 'user', 'id', 'cascade', 'cascade');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('help_user_assignment');
    }
}
