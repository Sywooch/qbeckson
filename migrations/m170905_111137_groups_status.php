<?php

use yii\db\Migration;

class m170905_111137_groups_status extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%groups}}','status', $this->integer()->defaultValue(\app\models\Groups::STATUS_ACTIVE));
        $this->createIndex('idx_groups_status', '{{%groups}}', 'status');
    }

    public function safeDown()
    {
        $this->dropIndex('idx_groups_status', '{{%groups}}');
        $this->dropColumn('{{%groups}}','status');
    }
}
