<?php

use yii\db\Migration;

class m171020_075250_alter_operator_settings_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('operator_settings', 'module_max_price', $this->integer(11)->defaultValue(150)->comment('максимальное значение цены модуля от нормативной стоимости в %'));
    }

    public function safeDown()
    {
        $this->dropColumn('operator_settings', 'module_max_price');
    }
}
