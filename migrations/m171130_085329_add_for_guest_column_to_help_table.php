<?php

use yii\db\Migration;

/**
 * Handles adding for_guest to table `help`.
 */
class m171130_085329_add_for_guest_column_to_help_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('help', 'for_guest', $this->smallInteger(1)->notNull()->defaultValue(0));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('help', 'for_guest');
    }
}
