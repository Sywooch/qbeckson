<?php

use yii\db\Migration;

class m171228_140311_age_group_max_program_default_0 extends Migration
{
    public function safeUp()
    {
        $this->alterColumn(
            '{{%programs}}',
            'age_group_max',
            $this->decimal(4, 2)->null()->defaultValue(0)
        );
    }

    public function safeDown()
    {
        true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171228_140311_age_group_max_program_default_0 cannot be reverted.\n";

        return false;
    }
    */
}
