<?php

use yii\db\Migration;

class m171129_070216_periodicField extends Migration
{
    public function safeUp()
    {
        $this->createTable(
            '{{%periodic_field}}',
            [
                'id' => $this->primaryKey(),
                'table_name' => $this->string(255)->notNull(),
                'field_name' => $this->string(255)->notNull(),
                'created_at' => $this->integer()->notNull(),
                'created_by' => $this->integer()->notNull(),
                'value' => $this->text()
            ]
        );
        $this->createIndex(
            'idx_periodic_field_table_field',
            '{{%periodic_field}}',
            ['table_name', 'field_name']
        );
        $this->createIndex(
            'idx_periodic_field_created_at',
            '{{%periodic_field}}',
            'created_at'
        );
    }

    public function safeDown()
    {
        echo "m171129_070216_periodicField cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171129_070216_periodicField cannot be reverted.\n";

        return false;
    }
    */
}
