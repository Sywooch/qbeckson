<?php

use yii\db\Migration;

class m171113_081451_alter_payers_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('payers', 'certificate_can_create_contract', $this->boolean()->after('certificate_can_use_future_balance')->defaultValue(1)->comment('разрешено ли сертификату создавать контракт'));
        $this->addColumn('payers', 'certificate_cant_create_contract_at', $this->dateTime()->after('certificate_can_create_contract')->defaultValue(null)->comment('сертификату запрещается создавать контракт с указанной даты и времени'));
    }

    public function safeDown()
    {
        $this->dropColumn('payers', 'certificate_cant_create_contract_at');
        $this->dropColumn('payers', 'certificate_can_create_contract');
    }
}
