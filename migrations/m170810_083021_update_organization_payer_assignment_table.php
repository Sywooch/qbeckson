<?php

use yii\db\Migration;

class m170810_083021_update_organization_payer_assignment_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('organization_payer_assignment', 'certificate_accounting_limit', $this->integer()->defaultValue(0));
    }

    public function safeDown()
    {
        $this->dropColumn('organization_payer_assignment', 'certificate_accounting_limit');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170810_083021_update_organization_payer_assignment_table cannot be reverted.\n";

        return false;
    }
    */
}
