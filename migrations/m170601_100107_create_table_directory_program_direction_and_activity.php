<?php

use yii\db\Migration;

class m170601_100107_create_table_directory_program_direction_and_activity extends Migration
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

        $this->createTable('directory_program_direction', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
        ], $tableOptions);

        $this->createTable('directory_program_activity', [
            'id' => $this->primaryKey(),
            'direction_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11),
            'name' => $this->string(255)->notNull(),
            'status' => $this->integer(2)->defaultValue(1),
        ], $tableOptions);

        $this->createTable('program_activity', [
            'id' => $this->primaryKey(),
            'program_id' => $this->integer(11)->notNull(),
            'activity_id' => $this->integer(11)->notNull(),
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

        $this->addForeignKey(
            'fk-program_activity-programs',
            'program_activity',
            'program_id',
            'programs',
            'id',
            'cascade',
            'cascade'
        );

        $this->addForeignKey(
            'fk-program_activity-directory_program_activity',
            'program_activity',
            'activity_id',
            'directory_program_activity',
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
        $this->dropForeignKey('fk-program_activity-programs', 'program_activity');
        $this->dropForeignKey('fk-program_activity-directory_program_activity', 'program_activity');
        $this->dropTable('directory_program_direction');
        $this->dropTable('directory_program_activity');
        $this->dropTable('program_activity');
    }
}
