<?php

use yii\db\Migration;

/**
 * Class m170606_050000_update_directory_program_direction
 */
class m170606_050000_update_directory_program_direction extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('directory_program_direction', 'old_name', 'varchar(255) DEFAULT NULL');

        $oldDirectionData = [
            'Техническая (робототехника)',
            'Техническая (иная)',
            'Художественная',
            'Естественнонаучная',
            'Социально-педагогическая',
            'Туристско-краеведческая',
            'Физкультурно-спортивная',
        ];
        foreach ($oldDirectionData as $key => $record) {
            $this->update('{{%directory_program_direction}}', [
                'old_name' => $record,
            ], 'id=' . ($key + 1));
        }
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('directory_program_direction', 'old_name');
    }
}
