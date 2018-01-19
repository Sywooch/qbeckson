<?php

use yii\db\Migration;

class m180116_113116_price_data_type_fix extends Migration
{
    public function safeUp()
    {
        $this->alterColumn(
            '{{%years}}',
            'price',
            $this->money(12, 2)->null()
        );
        $this->alterColumn(
            '{{%years}}',
            'normative_price',
            $this->money(12, 2)->notNull()
        );
    }

    public function safeDown()
    {
        $this->alterColumn(
            '{{%years}}',
            'price',
            $this->float()->null()
        );
        $this->alterColumn(
            '{{%years}}',
            'normative_price',
            $this->float()->notNull()
        );
    }
}
