<?php

use yii\db\Migration;

class m171029_094619_alter_operators_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('operator_settings', 'children_average', $this->integer(11)->defaultValue(null)->comment('среднее значение кол-ва детей'));
    }

    public function safeDown()
    {
        $this->dropColumn('operator_settings', 'children_average');
    }
}
