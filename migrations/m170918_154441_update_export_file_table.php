<?php

use yii\db\Migration;

class m170918_154441_update_export_file_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('export_file', 'export_type', $this->string() . ' AFTER `file`');
    }

    public function safeDown()
    {
        $this->dropColumn('export_file', 'export_type');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170918_154441_update_export_file_table cannot be reverted.\n";

        return false;
    }
    */
}
