<?php

use yii\db\Migration;

/**
 * Class m170606_074207_update_programs_table
 */
class m170606_074207_update_programs_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('programs', 'direction_id', 'int(11) DEFAULT NULL');
        $this->addForeignKey(
            'fk-programs-directory_program_direction',
            'programs',
            'direction_id',
            'directory_program_direction',
            'id'
        );

        $this->execute('ALTER TABLE `programs` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-programs-directory_program_direction', 'programs');
        $this->dropColumn('programs', 'direction_id');
    }
}
