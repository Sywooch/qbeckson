<?php

use yii\db\Migration;

class m171201_110141_programmeModuleVerification extends Migration
{
    public function safeUp()
    {
        $condition = <<<SQL
(select verification FROM programs WHERE id = program_id)
SQL;

        $this->addColumn(
            '{{%years}}',
            'verification',
            $this->integer()->notNull()->defaultValue(0)
        );
        $this->createIndex('idx_years_verification', '{{%years}}', 'verification');
        $this->update(
            '{{%years}}',
            ['verification' => new \yii\db\Expression($condition)]
        );
    }

    public function safeDown()
    {
        $this->dropIndex('idx_years_verification', '{{%years}}');
        $this->dropColumn('{{%years}}', 'verification');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171201_110141_programmeModuleVerification cannot be reverted.\n";

        return false;
    }
    */
}
