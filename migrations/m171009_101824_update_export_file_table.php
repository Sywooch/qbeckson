<?php

use yii\db\Migration;

class m171009_101824_update_export_file_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('export_file', 'status', $this->integer()->defaultValue(0));
        $this->addColumn('export_file', 'search_model', $this->text());
        $this->addColumn('export_file', 'columns', $this->text());
        $this->addColumn('export_file', 'group', $this->string());
        $this->addColumn('export_file', 'table', $this->string());
    }

    public function safeDown()
    {
        $this->dropColumn('export_file', 'status');
        $this->dropColumn('export_file', 'search_model');
        $this->dropColumn('export_file', 'columns');
        $this->dropColumn('export_file', 'group');
        $this->dropColumn('export_file', 'table');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171009_101824_update_export_file_table cannot be reverted.\n";

        return false;
    }
    */
}
