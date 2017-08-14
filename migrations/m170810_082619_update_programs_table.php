<?php

use yii\db\Migration;

class m170810_082619_update_programs_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('programs', 'is_municipal_task', $this->boolean()->defaultValue(0));
        $this->addColumn('programs', 'certificate_accounting_limit', $this->integer()->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn('programs', 'is_municipal_task');
        $this->dropColumn('programs', 'certificate_accounting_limit');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170810_082619_update_programs_table cannot be reverted.\n";

        return false;
    }
    */
}
