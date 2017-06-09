<?php

use yii\db\Migration;

/**
 * Class m170606_095450_update_programs_direction_id
 */
class m170606_095450_update_programs_direction_id extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->execute('
            UPDATE programs
                INNER JOIN directory_program_direction ON directory_program_direction.old_name = programs.directivity
                    SET programs.direction_id = directory_program_direction.id
        ');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->execute('UPDATE programs SET programs.direction_id = NULL');
    }
}
