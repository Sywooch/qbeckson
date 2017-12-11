<?php

use yii\db\Migration;

class m171211_123755_program_age_numeric extends Migration
{
    public function safeUp()
    {
        $this->alterColumn(
            '{{%programs}}',
            'age_group_min',
            $this->decimal(4, 2)->null()
        );
        $this->alterColumn(
            '{{%programs}}',
            'age_group_max',
            $this->decimal(4, 2)->notNull()
        );
    }

    public function safeDown()
    {
        echo "m171211_123755_program_age_numeric cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171211_123755_program_age_numeric cannot be reverted.\n";

        return false;
    }
    */
}
