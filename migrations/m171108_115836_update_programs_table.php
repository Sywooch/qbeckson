<?php

use yii\db\Migration;

class m171108_115836_update_programs_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('programs', 'refuse_reason', $this->text());
    }

    public function safeDown()
    {
        $this->dropColumn('programs', 'refuse_reason');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171108_115836_update_programs_table cannot be reverted.\n";

        return false;
    }
    */
}
