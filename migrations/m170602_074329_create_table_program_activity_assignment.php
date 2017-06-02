<?php

use yii\db\Migration;

class m170602_074329_create_table_program_activity_assignment extends Migration
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

        $this->createTable('program_activity_assignment', [
            'program_id' => $this->integer(11)->notNull(),
            'activity_id' => $this->integer(11)->notNull(),
        ], $tableOptions);
        
        $this->addPrimaryKey(
            'pk-program_activity_assignment',
            'program_activity_assignment',
            ['program_id', 'activity_id']
        );

        $this->addForeignKey(
            'fk-program_activity_assignment-programs',
            'program_activity_assignment',
            'program_id',
            'programs',
            'id',
            'cascade',
            'cascade'
        );

        $this->addForeignKey(
            'fk-program_activity_assignment-directory_program_activity',
            'program_activity_assignment',
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
        $this->dropForeignKey('fk-program_activity_assignment-programs', 'program_activity_assignment');
        $this->dropForeignKey('fk-program_activity_assignment-directory_program_activity', 'program_activity_assignment');
        $this->dropTable('program_activity_assignment');
    }
}
