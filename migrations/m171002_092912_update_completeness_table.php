<?php

use yii\db\Migration;

class m171002_092912_update_completeness_table extends Migration
{
    public function safeUp()
    {
        $this->createIndex('idx_completeness_contract_id', 'completeness', 'contract_id');
        $this->createIndex('idx_completeness_month', 'completeness', 'month');
        $this->createIndex('idx_completeness_preinvoice', 'completeness', 'preinvoice');
    }

    public function safeDown()
    {
        $this->dropIndex('idx_completeness_contract_id', 'completeness');
        $this->dropIndex('idx_completeness_month', 'completeness');
        $this->dropIndex('idx_completeness_preinvoice', 'completeness');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171002_092912_update_completeness_table cannot be reverted.\n";

        return false;
    }
    */
}
