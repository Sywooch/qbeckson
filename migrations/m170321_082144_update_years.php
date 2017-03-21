<?php

use yii\db\Migration;

class m170321_082144_update_years extends Migration
{
    public function up()
    {
        $this->addColumn('{{%years}}', 'name', $this->string() . ' AFTER `id`');
        $this->addColumn('{{%years}}', 'results', $this->text());
    }

    public function down()
    {
        echo "m170321_082144_update_years cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
