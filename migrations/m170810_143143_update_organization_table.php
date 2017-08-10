<?php

use yii\db\Migration;

class m170810_143143_update_organization_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('organization', 'certificate_accounting_limit', $this->integer()->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn('organization', 'certificate_accounting_limit');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170810_143143_update_organization_table cannot be reverted.\n";

        return false;
    }
    */
}
