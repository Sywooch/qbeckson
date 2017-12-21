<?php

use yii\db\Migration;

class m171221_053525_years_hours_to_decimal extends Migration
{
    public function safeUp()
    {
        $this->alterColumn(
            '{{%years}}',
            'hours',
            $this->decimal(9, 2)->notNull()
        );
    }

    public function safeDown()
    {
        echo "m171221_053525_years_hours_to_decimal cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171221_053525_years_hours_to_decimal cannot be reverted.\n";

        return false;
    }
    */
}
