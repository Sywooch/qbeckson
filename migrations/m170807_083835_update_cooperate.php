<?php

use yii\db\Migration;

/**
 * Class m170807_083835_update_cooperate
 */
class m170807_083835_update_cooperate extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('cooperate', 'reject_reason', 'TEXT DEFAULT NULL');
        $this->addColumn('cooperate', 'appeal_reason', 'TEXT DEFAULT NULL');
        $this->addColumn('cooperate', 'created_date', 'DATETIME DEFAULT NULL');
        $this->alterColumn('cooperate', 'date', 'DATE DEFAULT NULL');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('cooperate', 'reject_reason');
        $this->dropColumn('cooperate', 'appeal_reason');
        $this->dropColumn('cooperate', 'created_date');
    }
}
