<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_monitor_assignment`.
 */
class m170728_122659_create_user_monitor_assignment_table extends Migration
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

        $this->createTable('user_monitor_assignment', [
            'user_id' => $this->integer(11)->notNull(),
            'monitor_id' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey(
            'pk-user_monitor_assignment',
            'user_monitor_assignment',
            ['user_id', 'monitor_id']
        );

        $this->addForeignKey(
            'fk-user_monitor_assignment-user',
            'user_monitor_assignment',
            'user_id',
            'user',
            'id',
            'cascade',
            'cascade'
        );

        $this->addForeignKey(
            'fk-user_monitor_assignment-monitor',
            'user_monitor_assignment',
            'monitor_id',
            'user',
            'id',
            'cascade',
            'cascade'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user_monitor_assignment');
    }
}
