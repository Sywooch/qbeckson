<?php

use yii\db\Migration;

class m171221_085623_alter_contracts_table extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('contracts', 'creation_status');

        $this->addColumn('contracts', 'parent_id', $this->integer(11)->after('id')->defaultValue(null)->comment('id родительского контракта'));
        $this->addForeignKey('fk_parent_id-id', 'contracts', 'parent_id', 'contracts', 'id', 'RESTRICT', 'RESTRICT');
        $this->addColumn('contracts', 'auto_prolongation_enabled', $this->boolean()->defaultValue(1)->comment('установлена ли автоматическая пролонгация контракта, 0 - нет, 1 - да'));
    }

    public function safeDown()
    {
        $this->dropColumn('contracts', 'auto_prolongation_enabled');
        $this->dropForeignKey('fk_parent_id-id', 'contracts');
        $this->dropColumn('contracts', 'parent_id');

        $this->addColumn('contracts', 'creation_status', $this->integer(1)->defaultValue(null)->comment('статус создания договора'));
    }
}
