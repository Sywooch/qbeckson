<?php

use yii\db\Migration;

class m171214_153345_alter_programs_contracts_tables extends Migration
{
    public function safeUp()
    {
        $this->addColumn('programs', 'auto_prolongation_enabled', $this->boolean()->defaultValue(1)->comment('установлена ли автоматическая пролонгация программы, 0 - нет, 1 - да'));
        $this->addColumn('contracts', 'creation_status', $this->integer(1)->defaultValue(null)->comment('статус создания договора'));
    }

    public function safeDown()
    {
        $this->dropColumn('contracts', 'creation_status');
        $this->dropColumn('programs', 'auto_prolongation_enabled');
    }
}
