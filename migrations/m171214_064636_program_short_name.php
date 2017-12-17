<?php

use yii\db\Migration;

class m171214_064636_program_short_name extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('{{%programs}}', 'short_name');
        $this->addColumn(
            '{{%programs}}',
            'short_name',
            $this->string(64)->null()
                ->after('name')
        );
        $this->processPrograms();
        $this->alterColumn('{{%programs}}', 'short_name', $this->string(64)->notNull());
    }

    private function processPrograms()
    {
        $expression = <<<SQL
IF(SUBSTR(name, 1, 61) = name, name, CONCAT(SUBSTR(name, 1, 61), '...')) 
SQL;
        \app\models\Programs::updateAll([
            'short_name' => new \yii\db\Expression($expression)
        ]);
    }

    public function safeDown()
    {
        echo "m171214_064636_program_short_name cannot be reverted.\n";

        return false;
    }
}
