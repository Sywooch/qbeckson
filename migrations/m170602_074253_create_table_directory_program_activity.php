<?php

use yii\db\Migration;

class m170602_074253_create_table_directory_program_activity extends Migration
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

        $this->createTable('directory_program_activity', [
            'id' => $this->primaryKey(),
            'direction_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11),
            'name' => $this->string(255)->notNull(),
            'status' => $this->integer(2)->defaultValue(10),
        ], $tableOptions);

        $this->addForeignKey(
            'fk-directory_program_activity-directory_program_direction',
            'directory_program_activity',
            'direction_id',
            'directory_program_direction',
            'id',
            'cascade',
            'cascade'
        );

        $this->addForeignKey(
            'fk-directory_program_activity-user',
            'directory_program_activity',
            'user_id',
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
        $this->dropForeignKey('fk-directory_program_activity-directory_program_direction', 'directory_program_activity');
        $this->dropForeignKey('fk-directory_program_activity-user', 'directory_program_activity');
        $this->dropTable('directory_program_activity');
    }
}
