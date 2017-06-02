<?php

use yii\db\Migration;

class m170602_074335_add_test_data extends Migration
{
    public function up()
    {
        $directionData = [
            'Техническая (робототехника)',
            'Техническая (иная)',
            'Естественнонаучная',
            'Физкультурно-спортивная',
            'Художественная',
            'Туристско-краеведческая',
            'Социально-педагогическая'
        ];

        foreach ($directionData as $record) {
            $this->insert('{{%directory_program_direction}}', [
                'name' => $record,
            ]);
        }

        $activityData = [
            ['direction_id' => 1, 'name' => 'Test-1-1'],
            ['direction_id' => 1, 'name' => 'Test-1-2'],
            ['direction_id' => 2, 'name' => 'Test-2-1'],
            ['direction_id' => 2, 'name' => 'Test-2-2'],
            ['direction_id' => 3, 'name' => 'Test-3-1'],
            ['direction_id' => 3, 'name' => 'Test-3-2'],
            ['direction_id' => 4, 'name' => 'Test-4-1'],
            ['direction_id' => 4, 'name' => 'Test-4-2'],
            ['direction_id' => 5, 'name' => 'Test-5-1'],
            ['direction_id' => 5, 'name' => 'Test-5-2'],
            ['direction_id' => 6, 'name' => 'Test-6-1'],
            ['direction_id' => 6, 'name' => 'Test-6-2'],
            ['direction_id' => 7, 'name' => 'Test-7-1'],
            ['direction_id' => 7, 'name' => 'Test-7-2'],
        ];

        foreach ($activityData as $record) {
            $this->insert('{{%directory_program_activity}}', [
                'direction_id' => $record['direction_id'],
                'name' => $record['name'],
            ]);
        }
    }

    public function down()
    {
        return true;
    }
}
