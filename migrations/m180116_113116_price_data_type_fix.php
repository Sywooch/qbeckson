<?php

use yii\db\Migration;

class m180116_113116_price_data_type_fix extends Migration
{
    public function safeUp()
    {
        $this->alterColumn(
            '{{%programs}}',
            'price',
            $this->money(12, 2)->null()
        );
        $this->alterColumn(
            '{{%programs}}',
            'normative_price',
            $this->money(12, 2)->notNull()
        );
    }

    public function safeDown()
    {
        $this->alterColumn(
            '{{%programs}}',
            'price',
            $this->float()->null()
        );
        $this->alterColumn(
            '{{%programs}}',
            'normative_price',
            $this->float()->notNull()
        );
    }
}
